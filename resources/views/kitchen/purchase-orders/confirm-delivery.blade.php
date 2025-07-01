@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Confirm Delivery - {{ $purchaseOrder->order_number }}</h2>
                <a href="{{ route('kitchen.purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Purchase Order Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> {{ $purchaseOrder->order_number }}</p>
                            <p><strong>Created By:</strong> {{ $purchaseOrder->creator->user_fname }} {{ $purchaseOrder->creator->user_lname }}</p>
                            <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge badge-info">{{ ucfirst($purchaseOrder->status) }}</span>
                            </p>
                            <p><strong>Expected Delivery:</strong> 
                                {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'Not set' }}
                            </p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($purchaseOrder->total_amount, 2) }}</p>
                        </div>
                    </div>
                    @if($purchaseOrder->notes)
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Notes:</strong> {{ $purchaseOrder->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Confirmation Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Confirmation</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('kitchen.purchase-orders.process-delivery', $purchaseOrder) }}" id="deliveryForm">
                        @csrf
                        
                        <!-- Delivery Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="actual_delivery_date" class="form-label">Actual Delivery Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('actual_delivery_date') is-invalid @enderror" 
                                       id="actual_delivery_date" name="actual_delivery_date" 
                                       value="{{ old('actual_delivery_date', date('Y-m-d')) }}" required>
                                @error('actual_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="delivery_notes" class="form-label">Delivery Notes</label>
                                <textarea class="form-control @error('delivery_notes') is-invalid @enderror" 
                                          id="delivery_notes" name="delivery_notes" rows="3" 
                                          placeholder="Any notes about the delivery condition, quality, etc.">{{ old('delivery_notes') }}</textarea>
                                @error('delivery_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Items Delivered -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Items Delivered</h5>
                                <p class="text-muted">Confirm the quantities actually delivered for each item. This will update the inventory automatically.</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Item Name</th>
                                                <th>Ordered Quantity</th>
                                                <th>Delivered Quantity <span class="text-danger">*</span></th>
                                                <th>Unit</th>
                                                <th>Unit Price</th>
                                                <th>Total Value</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchaseOrder->items as $index => $item)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $item->item_name }}</strong>
                                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    </td>
                                                    <td>{{ $item->quantity_ordered }} {{ $item->unit }}</td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control quantity-delivered @error('items.'.$index.'.quantity_delivered') is-invalid @enderror" 
                                                               name="items[{{ $index }}][quantity_delivered]" 
                                                               value="{{ old('items.'.$index.'.quantity_delivered', $item->quantity_ordered) }}"
                                                               step="0.01" min="0" max="{{ $item->quantity_ordered }}" 
                                                               data-unit-price="{{ $item->unit_price }}"
                                                               data-row="{{ $index }}"
                                                               onchange="calculateRowValue({{ $index }})" required>
                                                        @error('items.'.$index.'.quantity_delivered')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>{{ $item->unit }}</td>
                                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                                    <td>
                                                        <span class="row-value" id="row-value-{{ $index }}">
                                                            ₱{{ number_format($item->quantity_ordered * $item->unit_price, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" 
                                                               name="items[{{ $index }}][notes]" 
                                                               value="{{ old('items.'.$index.'.notes') }}"
                                                               placeholder="Condition notes">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-right">Total Delivered Value:</th>
                                                <th id="totalDeliveredValue">₱{{ number_format($purchaseOrder->total_amount, 2) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Quick Actions</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="markAllAsDelivered()">
                                        Mark All as Fully Delivered
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="clearAllQuantities()">
                                        Clear All Quantities
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('kitchen.purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Confirm Delivery & Update Inventory
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Calculate row value based on delivered quantity
function calculateRowValue(rowIndex) {
    const quantityInput = document.querySelector(`input[name="items[${rowIndex}][quantity_delivered]"]`);
    const unitPrice = parseFloat(quantityInput.dataset.unitPrice);
    const quantity = parseFloat(quantityInput.value) || 0;
    const total = quantity * unitPrice;
    
    document.getElementById(`row-value-${rowIndex}`).textContent = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
    
    calculateTotalDeliveredValue();
}

// Calculate total delivered value
function calculateTotalDeliveredValue() {
    let total = 0;
    document.querySelectorAll('.quantity-delivered').forEach(function(input) {
        const quantity = parseFloat(input.value) || 0;
        const unitPrice = parseFloat(input.dataset.unitPrice);
        total += quantity * unitPrice;
    });
    
    document.getElementById('totalDeliveredValue').textContent = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
}

// Mark all items as fully delivered
function markAllAsDelivered() {
    document.querySelectorAll('.quantity-delivered').forEach(function(input) {
        const maxQuantity = parseFloat(input.getAttribute('max'));
        input.value = maxQuantity;
        const rowIndex = input.dataset.row;
        calculateRowValue(rowIndex);
    });
}

// Clear all quantities
function clearAllQuantities() {
    document.querySelectorAll('.quantity-delivered').forEach(function(input) {
        input.value = 0;
        const rowIndex = input.dataset.row;
        calculateRowValue(rowIndex);
    });
}

// Form validation
document.getElementById('deliveryForm').addEventListener('submit', function(e) {
    let hasDeliveredItems = false;
    document.querySelectorAll('.quantity-delivered').forEach(function(input) {
        if (parseFloat(input.value) > 0) {
            hasDeliveredItems = true;
        }
    });
    
    if (!hasDeliveredItems) {
        e.preventDefault();
        alert('Please specify delivered quantities for at least one item.');
        return false;
    }
});

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotalDeliveredValue();
});
</script>
@endsection
