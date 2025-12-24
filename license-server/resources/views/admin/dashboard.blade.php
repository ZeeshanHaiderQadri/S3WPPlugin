@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
        <div class="stat-change">+{{ $stats['total_admins'] }} admins</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Active Licenses</div>
        <div class="stat-value">{{ number_format($stats['active_licenses']) }}</div>
        <div class="stat-change">{{ $stats['expired_licenses'] }} expired</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="stat-change">${{ number_format($stats['monthly_revenue'], 2) }} this month</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Media Uploads</div>
        <div class="stat-value">{{ number_format($stats['total_uploads']) }}</div>
        <div class="stat-change">{{ number_format($stats['monthly_uploads']) }} this month</div>
    </div>
</div>

<!-- Revenue by Plan -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Revenue by Plan (Last 30 Days)</h3>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Sales</th>
                    <th>Revenue</th>
                    <th>Avg. Order</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revenueByPlan as $item)
                <tr>
                    <td><strong>{{ $item->plan->name }}</strong></td>
                    <td>{{ $item->count }} orders</td>
                    <td><strong>${{ number_format($item->total, 2) }}</strong></td>
                    <td>${{ number_format($item->total / $item->count, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No revenue data yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" style="color: #8B5CF6; text-decoration: none;">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->user->name }}</td>
                        <td><strong>${{ number_format($order->amount, 2) }}</strong></td>
                        <td>
                            <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">No orders yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Licenses -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Licenses</h3>
            <a href="{{ route('admin.licenses.index') }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>License Key</th>
                        <th>Plan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLicenses as $license)
                    <tr>
                        <td>
                            <a href="{{ route('admin.licenses.show', $license->id) }}" style="color: #8B5CF6; text-decoration: none;">
                                <code>{{ $license->license_key }}</code>
                            </a>
                        </td>
                        <td>{{ $license->plan->name }}</td>
                        <td>
                            <span class="badge badge-{{ $license->status === 'active' ? 'success' : ($license->status === 'expired' ? 'warning' : 'danger') }}">
                                {{ ucfirst($license->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No licenses yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top Users -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Top Users by Uploads</h3>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>License</th>
                    <th>Uploads</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topUsers as $item)
                <tr>
                    <td>{{ $item->license->user->name }}</td>
                    <td>{{ $item->license->user->email }}</td>
                    <td><code>{{ $item->license->license_key }}</code></td>
                    <td><strong>{{ number_format($item->upload_count) }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No upload data yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
