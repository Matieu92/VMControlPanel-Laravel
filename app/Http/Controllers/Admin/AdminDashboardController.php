<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Node;
use App\Models\User;
use App\Models\AuditLog;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_servers' => Server::count(),
            'active_servers' => Server::where('status', 'running')->count(),
            'provisioning'   => Server::where('status', 'provisioning')->count(),
            'total_users'   => User::count(),
            'used_ram'      => Server::with('subscription.plan')->get()->sum(function($s) {
                return $s->subscription->plan->ram_mb ?? 0;
            }),
            'total_ram'     => Node::sum('total_ram_mb'),
        ];

        $latestLogs = AuditLog::with('user')->latest()->take(5)->get();
        $recentServers = Server::with(['user', 'subscription.plan'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latestLogs', 'recentServers'));
    }
}