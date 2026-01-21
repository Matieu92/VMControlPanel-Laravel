<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerPlan;
use App\Models\AuditLog;
use App\Models\Transaction;
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

        $newPlan = ServerPlan::findOrFail($request->server_plan_id);
        $currentPlan = $server->subscription->plan;
        $user = $server->user; 

        $priceDifference = (float) $newPlan->price - (float) $currentPlan->price;

        return \DB::transaction(function () use ($request, $server, $newPlan, $currentPlan, $user, $priceDifference) {
            
            if ($priceDifference > 0) {
                if ($user->balance < $priceDifference) {
                    return back()->with('error', 'Użytkownik posiada niewystarczające środki (wymagane: ' . number_format($priceDifference, 2) . ' PLN)');
                }

                $user->decrement('balance', $priceDifference);

                Transaction::create([
                    'user_id'     => $user->id,
                    'amount'      => $priceDifference,
                    'type'        => 'PLAN_UPGRADE',
                    'description' => "Dopłata za zmianę planu serwera {$server->hostname} z {$currentPlan->name} na {$newPlan->name}"
                ]);
            }

            $oldPlanName = $currentPlan->name ?? 'Brak';
            $server->subscription->update(['server_plan_id' => $newPlan->id]);

            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'ADMIN_SERVER_PLAN_CHANGE',
                'details'    => "Zmieniono plan serwera {$server->hostname} na {$newPlan->name}. Pobrano: " . number_format($priceDifference > 0 ? $priceDifference : 0, 2) . " PLN",
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('admin.servers.index')->with('success', 'Plan został zaktualizowany, a operacja zarejestrowana w finansach.');
        });
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