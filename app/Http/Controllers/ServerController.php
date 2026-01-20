<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerPlan;
use App\Models\Node;
use App\Models\OperatingSystem;
use App\Models\Subscription;
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
        $plans = ServerPlan::all();
        $osList = OperatingSystem::all();

        return view('servers.create', compact('plans', 'osList'));
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

        return response()->json(['message' => 'Serwer uruchomiony.', 'status' => 'running']);
    }

    public function stop(Server $server)
    {
        if ($server->user_id !== Auth::id()) abort(403);

        sleep(4);

        $server->update(['status' => 'stopped']);

        return response()->json(['message' => 'Serwer zatrzymany.', 'status' => 'stopped']);
    }

    public function restart(Server $server)
    {
        if ($server->user_id !== Auth::id()) abort(403);

        sleep(10);

        $server->update(['status' => 'running']);

        return response()->json(['message' => 'Serwer zrestartowany.', 'status' => 'running']);
    }
}