<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'plan']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        
        $orders = $query->latest()->paginate(20);
        
        $stats = [
            'total_revenue' => Order::where('status', 'completed')->sum('amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'failed_orders' => Order::where('status', 'failed')->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'stats'));
    }
    
    public function show($id)
    {
        $order = Order::with(['user', 'plan'])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }
    
    public function refund($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status !== 'completed') {
            return back()->with('error', 'Only completed orders can be refunded');
        }
        
        // TODO: Implement Stripe refund
        $order->update(['status' => 'refunded']);
        
        return back()->with('success', 'Order refunded successfully');
    }
}
