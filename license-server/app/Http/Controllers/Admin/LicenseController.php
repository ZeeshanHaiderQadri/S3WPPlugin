<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use App\Models\Plan;
use App\Models\MediaUpload;
use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    protected $licenseService;
    
    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }
    
    public function index(Request $request)
    {
        $query = License::with(['user', 'plan']);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_key', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan_id', $request->plan);
        }
        
        $licenses = $query->latest()->paginate(20);
        $plans = Plan::all();
        
        return view('admin.licenses.index', compact('licenses', 'plans'));
    }
    
    public function show($id)
    {
        $license = License::with(['user', 'plan', 'activations', 'mediaUploads' => function($q) {
            $q->latest()->limit(50);
        }])->findOrFail($id);
        
        $stats = [
            'total_uploads' => $license->mediaUploads()->count(),
            'total_size' => $license->mediaUploads()->sum('file_size'),
            'active_sites' => $license->activations()->where('is_active', true)->count(),
            'usage_percentage' => $license->plan->media_limit 
                ? ($license->mediaUploads()->count() / $license->plan->media_limit) * 100 
                : 0,
        ];
        
        return view('admin.licenses.show', compact('license', 'stats'));
    }
    
    public function suspend($id)
    {
        $license = License::findOrFail($id);
        $license->update(['status' => 'suspended']);
        
        return back()->with('success', 'License suspended successfully');
    }
    
    public function activate($id)
    {
        $license = License::findOrFail($id);
        
        // Check if not expired
        if ($license->expires_at && $license->expires_at < now()) {
            return back()->with('error', 'Cannot activate expired license. Please extend first.');
        }
        
        $license->update(['status' => 'active']);
        
        return back()->with('success', 'License activated successfully');
    }
    
    public function extend(Request $request, $id)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:36',
        ]);
        
        $license = License::findOrFail($id);
        
        $currentExpiry = $license->expires_at ?? now();
        $newExpiry = $currentExpiry->addMonths($request->months);
        
        $license->update([
            'expires_at' => $newExpiry,
            'status' => 'active',
        ]);
        
        return back()->with('success', "License extended by {$request->months} months");
    }
    
    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
        ]);
        
        $user = User::findOrFail($request->user_id);
        $plan = Plan::findOrFail($request->plan_id);
        
        $license = $this->licenseService->createLicense($user, $plan);
        
        return redirect()
            ->route('admin.licenses.show', $license->id)
            ->with('success', 'License created successfully');
    }
}
