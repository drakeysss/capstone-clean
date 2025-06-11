@extends('layouts.app')

@section('title', 'Inventory Report Details - Cook Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Inventory Report Details</h3>
                        <p class="mb-0 text-muted">Detailed view of kitchen inventory report</p>
                    </div>
                    <div>
                        <a href="{{ route('cook.inventory.reports') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Information -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Report Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Report ID:</strong> #{{ $report->id }}</p>
                            <p><strong>Submitted by:</strong> {{ $report->user->name ?? 'Kitchen Staff' }}</p>
                            <p><strong>Submission Date:</strong> {{ $report->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Items:</strong> {{ $report->items->count() }}</p>
                            <p><strong>Items Needing Restock:</strong> {{ $report->items->where('needs_restock', true)->count() }}</p>
                            <p><strong>Status:</strong>
                                @if($report->approved_at)
                                    <span class="badge bg-success">Approved</span>
                                @elseif($report->items->where('needs_restock', true)->count() > 0)
                                    <span class="badge bg-warning">Needs Approval</span>
                                @else
                                    <span class="badge bg-info">Under Review</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($report->notes)
                        <div class="mt-3">
                            <strong>Notes from Kitchen:</strong>
                            <div class="alert alert-info mt-2">
                                {{ $report->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Quick Stats</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <h3 class="text-success">{{ $report->items->where('needs_restock', false)->count() }}</h3>
                            <small class="text-muted">In Stock</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-warning">{{ $report->items->where('needs_restock', true)->count() }}</h3>
                            <small class="text-muted">Need Restock</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Needing Attention -->
    @php
        $restockItems = $report->items->where('needs_restock', true);
    @endphp
    @if($restockItems->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Items Requiring Immediate Attention</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($restockItems as $item)
                        <div class="col-md-4 mb-3">
                            <div class="border border-warning rounded p-3 bg-warning bg-opacity-10">
                                <h6 class="text-warning">{{ $item->ingredient->name ?? 'Unknown Item' }}</h6>
                                <p class="mb-1"><strong>Current Stock:</strong> {{ $item->current_stock }}</p>
                                @if($item->notes)
                                    <p class="mb-0 small text-muted"><strong>Notes:</strong> {{ $item->notes }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Approval Section -->
    @if(!$report->approved_at)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Approve Inventory Report</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cook.inventory.approve-report', $report->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                            <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"
                                      placeholder="Add any notes about this approval or restocking instructions..."></textarea>
                        </div>

                        @if($restockItems->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Items Approved for Restocking:</label>
                            <div class="row">
                                @foreach($restockItems as $item)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="approved_items[]"
                                               value="{{ $item->id }}" id="item_{{ $item->id }}" checked>
                                        <label class="form-check-label" for="item_{{ $item->id }}">
                                            <strong>{{ $item->ingredient->name ?? 'Unknown Item' }}</strong>
                                            <br><small class="text-muted">Current: {{ $item->current_stock }} units</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-1"></i>Approve Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Report Approved</strong> on {{ $report->approved_at->format('M d, Y \a\t g:i A') }}
                @if($report->approval_notes)
                    <br><strong>Notes:</strong> {{ $report->approval_notes }}
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- All Reported Items -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>All Reported Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Current Stock</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report->items as $item)
                                <tr class="{{ $item->needs_restock ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>{{ $item->ingredient->name ?? 'Unknown Item' }}</strong>
                                        @if($item->ingredient && $item->ingredient->supplier)
                                            <br><small class="text-muted">Supplier: {{ $item->ingredient->supplier }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $item->current_stock }}</span>
                                        @if($item->ingredient)
                                            <small class="text-muted">{{ $item->ingredient->unit ?? '' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->needs_restock)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Needs Restock
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>In Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->notes)
                                            <span class="text-muted">{{ $item->notes }}</span>
                                        @else
                                            <span class="text-muted">No notes</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->ingredient)
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="updateInventory({{ $item->ingredient->id }}, '{{ $item->ingredient->name }}', {{ $item->current_stock }})">
                                                <i class="bi bi-pencil me-1"></i>Update Inventory
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="mb-0 mt-2">No items in this report.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Inventory Modal -->
<div class="modal fade" id="updateInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="updateInventoryForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="update_item_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="update_item_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="update_quantity" class="form-label">New Quantity *</label>
                        <input type="number" step="0.01" class="form-control" id="update_quantity" name="quantity" required>
                        <small class="text-muted">Enter the corrected quantity based on kitchen report</small>
                    </div>
                    <div class="mb-3">
                        <label for="update_reorder_point" class="form-label">Reorder Point</label>
                        <input type="number" step="0.01" class="form-control" id="update_reorder_point" name="reorder_point">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateInventory(itemId, itemName, currentStock) {
    document.getElementById('updateInventoryForm').action = `/cook/inventory/${itemId}`;
    document.getElementById('update_item_name').value = itemName;
    document.getElementById('update_quantity').value = currentStock;

    const modal = new bootstrap.Modal(document.getElementById('updateInventoryModal'));
    modal.show();
}
</script>

@endsection
