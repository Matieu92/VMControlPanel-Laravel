<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Node;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServerMigrationController extends Controller
{
    public function create(Server $server)
    {
        $nodes = Node::where('id', '!=', $server->node_id)
                     ->where('is_active', true)
                     ->get();

        return view('admin.servers.migrate', compact('server', 'nodes'));
    }

public function migrate(Request $request, Server $server)
    {
        $validated = $request->validate([
            'new_node_id' => 'required|exists:nodes,id',
        ]);

        if ($validated['new_node_id'] == $server->node_id) {
            return back()->withErrors(['new_node_id' => 'Serwer już znajduje się na tym węźle.']);
        }

        $newNode = Node::findOrFail($validated['new_node_id']);
        $plan = $server->plan;

        $usedRam = $newNode->servers->sum(function($s) {
            return $s->plan ? $s->plan->ram_mb : 0;
        });
        
        if (($newNode->total_ram_mb - $usedRam) < $plan->ram_mb) {
            return back()->withErrors(['error' => "Węzeł {$newNode->name} nie ma wystarczającej ilości wolnej pamięci RAM (Wolne: " . ($newNode->total_ram_mb - $usedRam) . "MB)."]);
        }

        $oldNodeName = $server->node->name;
        
        $server->update(['node_id' => $newNode->id,
        'status' => 'stopped']);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'SERVER_MIGRATION',
            'details' => "Przeniesiono serwer {$server->hostname} (ID: {$server->id}) z węzła {$oldNodeName} na {$newNode->name}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.servers.index')->with('success', "Migracja serwera {$server->hostname} zakończona pomyślnie.");
    }
}