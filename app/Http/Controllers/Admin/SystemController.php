<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatingSystem;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function index()
    {
        $systems = OperatingSystem::all();
        return view('admin.systems.index', compact('systems'));
    }

    public function create()
    {
        return view('admin.systems.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
        ]);

        OperatingSystem::create($validated);
        return redirect()->route('admin.systems.index')->with('success', 'System dodany.');
    }

    public function destroy(OperatingSystem $system)
    {
        $system->delete();
        return redirect()->route('admin.systems.index')->with('success', 'System usunięty.');
    }
}