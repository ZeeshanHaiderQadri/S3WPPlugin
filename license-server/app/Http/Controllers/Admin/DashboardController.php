<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\License;
use App\Models\Order;
use App\Models\MediaUpload;
use App\Models\UsageStat;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Overview Statistics
        $stats = [
            'total_users' => User::where('role', 'customer')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'active_licenses' => License::where('status', 'active')->count(),
            'expired_licenses' => License::where('status', 'expired')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'total_uploads' => MediaUpload::count(),
            'monthly_uploads' => MediaUpload::whereMonth('created_at', now()->month)->count(),
            'total_storage' => MediaUpload::sum('file_size'),
        ];
        
        // Recent Orders
        $recentOrders = Order::with(['user', 'plan'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Recent Licenses
        $recentLicenses = License::with(['user', 'plan'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Top Users by Uploads
        $topUsers = MediaUpload::select('license_id', DB::raw('COUNT(*) as upload_count'))
            ->groupBy('license_id')
            ->orderByDesc('upload_count')
            ->limit(10)
            ->with('license.user')
            ->get();
        
        // Revenue by Plan (Last 30 days)
        $revenueByPlan = Order::select('plan_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('plan_id')
            ->with('plan')
            ->get();
        
        // Daily uploads chart (Last 30 days)
        $dailyUploads = UsageStat::select('date', DB::raw('SUM(uploads_count) as total'))
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'recentLicenses',
            'topUsers',
            'revenueByPlan',
            'dailyUploads'
        ));
    }
}
