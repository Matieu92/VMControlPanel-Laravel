<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServerPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function updateSystems(Request $request, ServerPlan $plan)
    {
        $validated = $request->validate([
            'systems' => 'nullable|array',
            'systems.*' => 'exists:operating_systems,id',
        ]);

        $plan->operatingSystems()->sync($validated['systems'] ?? []);

        return back()->with('success', 'Dostępne systemy operacyjne dla planu zostały zaktualizowane.');
    }
}
