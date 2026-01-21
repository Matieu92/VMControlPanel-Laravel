<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerPlan;
use App\Models\Node;
use App\Models\OperatingSystem;
use App\Models\Subscription;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::where('user_id', Auth::id())
            ->with(['subscription.plan', 'operatingSystem', 'node'])
            ->get();

        return view('servers.index', compact('servers'));
    }

    public function create()
    {
        $osList = OperatingSystem::orderBy('name')->orderBy('version')->get();
        
        $plans = ServerPlan::with('operatingSystems')->orderBy('price')->get();

        return view('servers.create', compact('osList', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:255|unique:servers,hostname',
            'server_plan_id' => 'required|exists:server_plans,id',
            'operating_system_id' => 'required|exists:operating_systems,id',
        ]);

        $plan = ServerPlan::findOrFail($validated['server_plan_id']);

        $node = Node::where('is_active', true)->get()->filter(function ($node) use ($plan) {
            
            $usedRam = $node->servers->sum(function ($server) {
                return $server->subscription?->plan?->ram_mb ?? 0;
            });

            return ($node->total_ram_mb - $usedRam) >= $plan->ram_mb;
        })->first();

        if (!$node) {
            return back()->withErrors(['error' => 'Brak wolnych zasobów w infrastrukturze dla wybranego planu. Skontaktuj się z supportem.']);
        }

        DB::transaction(function () use ($validated, $node, $plan) {
            
            $sub = Subscription::create([
                'user_id' => Auth::id(),
                'server_plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
                'status' => 'active'
            ]);

            Server::create([
                'hostname' => $validated['hostname'],
                'user_id' => Auth::id(),
                'subscription_id' => $sub->id,
                'node_id' => $node->id,
                'operating_system_id' => $validated['operating_system_id'],
                'status' => 'provisioning',
                'ip_address' => '10.0.' . rand(1, 255) . '.' . rand(1, 255),
            ]);
        });

        return redirect()->route('servers.index')->with('success', "Serwer został utworzony na węźle {$node->name}.");
    }

    public function show(Server $server)
    {
        if ($server->user_id !== Auth::id()) {
            abort(403, 'Nie masz dostępu do tego serwera.');
        }

        $server->load(['subscription.plan', 'operatingSystem', 'node']);

        return view('servers.show', compact('server'));
    }

    public function start(Server $server)
    {
        if ($server->user_id !== Auth::id()) abort(403);

        if ($server->status === 'running') {
            return response()->json(['message' => 'Serwer już działa.', 'status' => 'running']);
        }

        sleep(5);

        $server->update(['status' => 'running']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SERVER_START',
            'details' => "Uruchomiono serwer: {$server->hostname} (IP: {$server->ip_address})",
            'ip_address' => request()->ip(),
        ]);

        return response()->json(['message' => 'Serwer uruchomiony.', 'status' => 'running']);
    }

    public function stop(Server $server)
    {
        if ($server->user_id !== Auth::id()) abort(403);

        sleep(4);

        $server->update(['status' => 'stopped']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SERVER_STOP',
            'details' => "Zatrzymano serwer: {$server->hostname}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json(['message' => 'Serwer zatrzymany.', 'status' => 'stopped']);
    }

    public function restart(Server $server)
    {
        if ($server->user_id !== Auth::id()) abort(403);

        sleep(10);

        $server->update(['status' => 'running']);

        return response()->json(['message' => 'Serwer zrestartowany.', 'status' => 'running']);
    }

    public function postReinstall(Request $request, Server $server)
    {
        if ($server->user_id !== Auth::id()) abort(403);

        if ($server->status === 'running') {
            return back()->with('error', 'Nie można przeinstalować systemu na uruchomionym serwerze. Wyłącz go najpierw.');
        }

        $request->validate([
            'operating_system_id' => 'required|exists:operating_systems,id'
        ]);

        if (!$server->subscription->plan->operatingSystems->contains($request->operating_system_id)) {
            return back()->with('error', 'Wybrany system nie jest kompatybilny z Twoim obecnym planem zasobów.');
        }

        $oldOsName = $server->operatingSystem ? $server->operatingSystem->name : 'Brak';

        $newOs = \App\Models\OperatingSystem::findOrFail($request->operating_system_id);
        $newOsName = $newOs->name;

        $server->update([
            'operating_system_id' => $request->operating_system_id,
            'status' => 'provisioning',
            'root_password' => str()->random(16)
        ]);

        AuditLog::create([
        'user_id' => Auth::id(),
        'action' => 'SERVER_REINSTALL',
        'details' => "Zlecono reinstalację serwera {$server->hostname}. Zmiana z {$oldOsName} na {$newOsName}",
        'ip_address' => $request->ip(),
        ]);

        return redirect()->route('servers.show', $server)->with('success', 'Proces reinstalacji został zainicjowany. Dane są właśnie nadpisywane.');
    }
}