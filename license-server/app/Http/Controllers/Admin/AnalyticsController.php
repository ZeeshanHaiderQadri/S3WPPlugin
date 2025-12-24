<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UsageStat;
use App\Models\License;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // User growth (last 12 months)
        $userGrowth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Revenue trend (last 12 months)
        $revenueTrend = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Plan distribution
        $planDistribution = License::select('plan_id', DB::raw('COUNT(*) as count'))
            ->where('status', 'active')
            ->groupBy('plan_id')
            ->with('plan')
            ->get();
        
        return view('admin.analytics.index', compact(
            'userGrowth',
            'revenueTrend',
            'planDistribution'
        ));
    }
    
    public function revenue()
    {
        // Detailed revenue analytics
        $stats = [
            'today' => Order::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'this_week' => Order::where('status', 'completed')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('amount'),
            'this_month' => Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'this_year' => Order::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];
        
        return view('admin.analytics.revenue', compact('stats'));
    }
    
    public function usage()
    {
        // Usage analytics
        $stats = UsageStat::where('date', '>=', now()->subDays(30))
            ->select(
                DB::raw('SUM(uploads_count) as total_uploads'),
                DB::raw('SUM(total_size) as total_size'),
                DB::raw('SUM(api_calls) as total_calls')
            )
            ->first();
        
        return view('admin.analytics.usage', compact('stats'));
    }
}
