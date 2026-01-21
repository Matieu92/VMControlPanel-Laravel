<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServerPlan;
use App\Models\OperatingSystem;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = ServerPlan::all();
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $systems = OperatingSystem::all();
        return view('admin.plans.create', compact('systems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ram_mb' => 'required|integer|min:128',
            'cpu_cores' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'systems' => 'nullable|array',
            'systems.*' => 'exists:operating_systems,id',
        ]);

        $plan = ServerPlan::create($validated);

        if ($request->has('systems')) {
            $plan->operatingSystems()->attach($request->systems);
        }

        return redirect()->route('admin.plans.index')->with('success', 'Plan dodany pomyślnie.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServerPlan $plan)
    {
        $systems = OperatingSystem::all();
        $plan->load('operatingSystems');

        return view('admin.plans.edit', compact('plan', 'systems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServerPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ram_mb' => 'required|integer|min:128',
            'cpu_cores' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'systems' => 'nullable|array',
            'systems.*' => 'exists:operating_systems,id',
        ]);

        $plan->update($validated);

        $plan->operatingSystems()->sync($request->input('systems', []));

        return redirect()->route('admin.plans.index')->with('success', 'Plan zaktualizowany.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServerPlan $plan)
    {
        $plan->operatingSystems()->detach();
        $plan->delete();
        
        return redirect()->route('admin.plans.index')->with('success', 'Plan usunięty.');
    }
}