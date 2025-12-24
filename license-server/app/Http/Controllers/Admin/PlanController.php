<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount(['licenses', 'orders'])->orderBy('sort_order')->get();
        
        return view('admin.plans.index', compact('plans'));
    }
    
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        
        return view('admin.plans.edit', compact('plan'));
    }
    
    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'media_limit' => 'nullable|integer|min:0',
            'max_sites' => 'required|integer|min:1',
            'features' => 'nullable|array',
        ]);
        
        $plan->update($validated);
        
        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'Plan updated successfully');
    }
    
    public function toggle($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);
        
        $status = $plan->is_active ? 'enabled' : 'disabled';
        
        return back()->with('success', "Plan {$status} successfully");
    }
}
