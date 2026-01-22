<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatingSystem;
use App\Models\Server;
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
        if ($system->servers()->exists()) {
            return redirect()
                ->route('admin.systems.index')
                ->with('error', 'Nie można usunąć systemu, ponieważ jest on obecnie zainstalowany na aktywnych maszynach. Przed usunięciem należy zmienić system na tych serwerach.');
        }

        $system->delete();

        return redirect()
            ->route('admin.systems.index')
            ->with('success', 'System został pomyślnie usunięty z repozytorium.');
    }

    public function detachServers(Request $request, OperatingSystem $system) {
        $allCurrentServers = $system->servers()->pluck('id')->toArray();
        $keepServers = $request->input('server_ids', []);
        
        $toDetach = array_diff($allCurrentServers, $keepServers);
        
        if(!empty($toDetach)) {
            Server::whereIn('id', $toDetach)->update(['operating_system_id' => null]);
        }

        return back()->with('success', 'Zasoby zostały zaktualizowane.');
    }
}