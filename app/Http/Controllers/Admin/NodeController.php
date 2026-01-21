<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Models\Location;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    public function index()
    {
        $nodes = Node::with('location')->get();
        return view('admin.nodes.index', compact('nodes'));
    }

    public function create()
    {
        return view('admin.nodes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'total_ram_mb' => 'required|integer|min:1024',
            'total_cpu_cores' => 'required|integer|min:1',
            'city' => 'required|string',
            'country_code' => 'required|string|size:2',
        ]);

        $location = Location::firstOrCreate([
            'city' => $request->city,
            'country_code' => strtoupper($request->country_code),
        ]);

        Node::create([
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'total_ram_mb' => $request->total_ram_mb,
            'total_cpu_cores' => $request->total_cpu_cores,
            'location_id' => $location->id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.nodes.index')->with('success', 'Węzeł dodany.');
    }

    public function edit(Node $node)
    {
        return view('admin.nodes.edit', compact('node'));
    }

    public function update(Request $request, Node $node)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'total_ram_mb' => 'required|integer|min:1024',
            'total_cpu_cores' => 'required|integer|min:1',
            'city' => 'required|string',
            'country_code' => 'required|string|size:2',
        ]);

        $location = Location::firstOrCreate([
            'city' => $request->city,
            'country_code' => strtoupper($request->country_code),
        ]);

        $node->update([
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'total_ram_mb' => $request->total_ram_mb,
            'total_cpu_cores' => $request->total_cpu_cores,
            'location_id' => $location->id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.nodes.index')->with('success', 'Węzeł zaktualizowany.');
    }

    public function destroy(Node $node)
    {
        if ($node->servers()->count() > 0) {
            return back()->withErrors(['error' => 'Nie można usunąć węzła, na którym działają serwery.']);
        }
        $node->delete();
        return redirect()->route('admin.nodes.index')->with('success', 'Węzeł usunięty.');
    }
}