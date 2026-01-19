<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Node;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ServerMigrationController extends Controller
{
    public function migrate(Request $request, Server $server)
    {
        $validated = $request->validate([
            'new_node_id' => 'required|exists:nodes,id|different:server.node_id',
        ]);

        $newNode = Node::findOrFail($validated['new_node_id']);
        $plan = $server->plan;

        $usedRam = $newNode->servers()->sum(DB::raw('(select ram_mb from server_plans where id = servers.server_plan_id)'));
        if (($newNode->total_ram_mb - $usedRam) < $plan->ram_mb) {
            return back()->withErrors(['error' => 'Docelowy węzeł nie posiada wystarczającej ilości pamięci RAM.']);
        }

        $oldNodeName = $server->node->name;
        $server->update(['node_id' => $newNode->id]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'SERVER_MIGRATION',
            'details' => "Przeniesiono serwer {$server->hostname} z {$oldNodeName} na {$newNode->name}",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Migracja serwera zakończona pomyślnie.');
    }
}