<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\License;
use App\Models\Order;
use App\Models\MediaUpload;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $users = $query->withCount(['licenses', 'orders'])
            ->latest()
            ->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }
    
    public function show($id)
    {
        $user = User::with(['licenses.plan', 'orders.plan'])->findOrFail($id);
        
        $stats = [
            'total_licenses' => $user->licenses->count(),
            'active_licenses' => $user->licenses->where('status', 'active')->count(),
            'total_orders' => $user->orders->count(),
            'total_spent' => $user->orders->where('status', 'completed')->sum('amount'),
            'total_uploads' => MediaUpload::whereIn('license_id', $user->licenses->pluck('id'))->count(),
        ];
        
        return view('admin.users.show', compact('user', 'stats'));
    }
    
    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);
        
        // Suspend all user licenses
        $user->licenses()->update(['status' => 'suspended']);
        
        return back()->with('success', 'User suspended successfully');
    }
    
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);
        
        // Reactivate licenses that were suspended
        $user->licenses()
            ->where('status', 'suspended')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->update(['status' => 'active']);
        
        return back()->with('success', 'User activated successfully');
    }
}
