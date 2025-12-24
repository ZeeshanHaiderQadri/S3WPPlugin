@extends('admin.layout')

@section('title', 'Licenses')
@section('page-title', 'License Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Licenses</h3>
        <div style="display: flex; gap: 10px;">
            <form method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Search license key..." value="{{ request('search') }}" class="form-control" style="width: 250px;">
                <select name="status" class="form-control" style="width: 150px;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <select name="plan" class="form-control" style="width: 150px;">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>License Key</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Expires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($licenses as $license)
                <tr>
                    <td><code>{{ $license->license_key }}</code></td>
                    <td>{{ $license->user->name }}</td>
                    <td><strong>{{ $license->plan->name }}</strong></td>
                    <td>
                        <span class="badge badge-{{ $license->status === 'active' ? 'success' : ($license->status === 'expired' ? 'warning' : 'danger') }}">
                            {{ ucfirst($license->status) }}
                        </span>
                    </td>
                    <td>{{ $license->expires_at ? $license->expires_at->format('M d, Y') : 'Never' }}</td>
                    <td>
                        <a href="{{ route('admin.licenses.show', $license->id) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">No licenses found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $licenses->links() }}
        </div>
    </div>
</div>
@endsection
