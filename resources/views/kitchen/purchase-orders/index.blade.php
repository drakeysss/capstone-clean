@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2>Purchase Orders - Kitchen View</h2>
            <p class="text-muted">View and confirm delivery of purchase orders</p>
        </div>
    </div>



    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('kitchen.purchase-orders.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('kitchen.purchase-orders.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Orders</h5>
                </div>
                <div class="card-body">
                    @if($purchaseOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Created By</th>
                                        <th>Order Date</th>
                                        <th>Status</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Expected Delivery</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrders as $order)
                                        <tr class="{{ $order->expected_delivery_date && $order->expected_delivery_date->isPast() && $order->status !== 'delivered' ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $order->order_number }}</strong>
                                                @if($order->expected_delivery_date && $order->expected_delivery_date->isPast() && $order->status !== 'delivered')
                                                    <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                                @endif
                                            </td>
                                            <td>{{ $order->creator->user_fname }} {{ $order->creator->user_lname }}</td>
                                            <td>{{ $order->order_date->format('M d, Y') }}</td>
                                            <td>
                                                @switch($order->status)
                                                    @case('approved')
                                                        <span class="badge badge-info">Approved</span>
                                                        @break
                                                    @case('ordered')
                                                        <span class="badge badge-warning">Ordered</span>
                                                        @break
                                                    @case('delivered')
                                                        <span class="badge badge-success">Delivered</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $order->items->count() }} items</td>
                                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('M d, Y') : 'Not set' }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('kitchen.purchase-orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($order->canBeDelivered())
                                                        <a href="{{ route('kitchen.purchase-orders.confirm-delivery', $order) }}" class="btn btn-sm btn-success">
                                                            <i class="fas fa-truck"></i> Confirm Delivery
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $purchaseOrders->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5>No Purchase Orders Found</h5>
                            <p class="text-muted">No purchase orders match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
