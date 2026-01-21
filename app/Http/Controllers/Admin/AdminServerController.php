<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerPlan;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminServerController extends Controller
{
    public function index()
    {
        $servers = Server::with(['user', 'node', 'subscription.plan', 'operatingSystem'])->latest()->get();
        
        return view('admin.servers.index', compact('servers'));
    }

    public function editPlan(Server $server)
    {
        $plans = ServerPlan::orderBy('price', 'asc')->get();
        
        return view('admin.servers.change_plan', compact('server', 'plans'));
    }

    public function updatePlan(Request $request, Server $server)
    {
        $request->validate([
            'server_plan_id' => 'required|exists:server_plans,id',
        ]);

        $newPlan = ServerPlan::with('operatingSystems')->findOrFail($request->server_plan_id);
        $currentOsId = $server->operating_system_id;

        $isCompatible = $newPlan->operatingSystems->contains($currentOsId);

        if (!$isCompatible) {
            return back()->withErrors([
                'server_plan_id' => "Niekompatybilny plan! Obecny system ({$server->operatingSystem->name}) nie jest dozwolony w wybranym planie. Rozwiązanie: Najpierw przeinstaluj system na serwerze lub wybierz inny plan."
            ])->withInput();
        }

        $oldPlanName = $server->subscription->plan->name ?? 'Brak';
        $server->subscription->update(['server_plan_id' => $newPlan->id]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'ADMIN_SERVER_PLAN_CHANGE',
            'details' => "Zmiana planu serwera {$server->hostname} z {$oldPlanName} na {$newPlan->name}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.servers.index')->with('success', 'Plan został zmieniony.');
    }

    public function destroy(Request $request, Server $server)
    {
        $serverName = $server->hostname;
        $userName = $server->user->name;

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'SERVER_DELETE_ADMIN',
            'details' => "Administrator usunął serwer: {$serverName} należący do użytkownika: {$userName}",
            'ip_address' => $request->ip()
        ]);

        $server->delete();

        return redirect()->route('admin.servers.index')->with('success', "Serwer {$serverName} został pomyślnie usunięty.");
    }
}