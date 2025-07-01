@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Create Purchase Order</h2>
                <a href="{{ route('cook.purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    @if($lowStockItems->count() > 0)
        <!-- Low Stock Alert -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Low Stock Items</h5>
                    <p>The following items are running low and may need restocking:</p>
                    <div class="row">
                        @foreach($lowStockItems as $item)
                            <div class="col-md-4 mb-2">
                                <strong>{{ $item->name }}</strong>: {{ $item->quantity }} {{ $item->unit }} 
                                (Reorder at: {{ $item->reorder_point }} {{ $item->unit }})
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addLowStockItems()">
                        <i class="fas fa-plus"></i> Add All Low Stock Items
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Purchase Order Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Order Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cook.purchase-orders.store') }}" id="purchaseOrderForm">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('order_date') is-invalid @enderror" 
                                       id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                                <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" 
                                       id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                                @error('expected_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" placeholder="Additional notes or special instructions">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Order Items</h5>
                                    <button type="button" class="btn btn-success" onclick="addItemRow()">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th width="30%">Item</th>
                                                <th width="15%">Current Stock</th>
                                                <th width="15%">Quantity</th>
                                                <th width="15%">Unit Price</th>
                                                <th width="15%">Total</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            <!-- Items will be added here dynamically -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-right">Grand Total:</th>
                                                <th id="grandTotal">₱0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('cook.purchase-orders.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Purchase Order
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

<!-- Item Selection Modal -->
<div class="modal fade" id="itemSelectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearch" placeholder="Search items...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Current Stock</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="availableItemsList">
                            @foreach($allItems as $item)
                                <tr data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}" 
                                    data-current-stock="{{ $item->quantity }}" data-unit="{{ $item->unit }}" 
                                    data-unit-price="{{ $item->unit_price }}">
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="selectItem(this)">
                                            Select
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let itemCounter = 0;
let selectedItems = [];

// Add item row to the table
function addItemRow() {
    $('#itemSelectionModal').modal('show');
}

// Select item from modal
function selectItem(button) {
    const row = button.closest('tr');
    const itemId = row.dataset.itemId;
    const itemName = row.dataset.itemName;
    const currentStock = row.dataset.currentStock;
    const unit = row.dataset.unit;
    const unitPrice = row.dataset.unitPrice;

    // Check if item already selected
    if (selectedItems.includes(itemId)) {
        alert('This item is already added to the order.');
        return;
    }

    selectedItems.push(itemId);
    itemCounter++;

    const newRow = `
        <tr id="item-row-${itemCounter}">
            <td>
                ${itemName}
                <input type="hidden" name="items[${itemCounter}][inventory_id]" value="${itemId}">
            </td>
            <td>${currentStock} ${unit}</td>
            <td>
                <input type="number" class="form-control quantity-input" 
                       name="items[${itemCounter}][quantity]" 
                       step="0.01" min="0.01" required 
                       onchange="calculateRowTotal(${itemCounter})">
            </td>
            <td>
                <input type="number" class="form-control unit-price-input" 
                       name="items[${itemCounter}][unit_price]" 
                       step="0.01" min="0" value="${unitPrice}" required 
                       onchange="calculateRowTotal(${itemCounter})">
            </td>
            <td>
                <span class="row-total" id="row-total-${itemCounter}">₱0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemCounter}, '${itemId}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#itemsTableBody').append(newRow);
    $('#itemSelectionModal').modal('hide');
    calculateGrandTotal();
}

// Remove item row
function removeItem(rowId, itemId) {
    $(`#item-row-${rowId}`).remove();
    selectedItems = selectedItems.filter(id => id !== itemId);
    calculateGrandTotal();
}

// Calculate row total
function calculateRowTotal(rowId) {
    const quantity = parseFloat($(`input[name="items[${rowId}][quantity]"]`).val()) || 0;
    const unitPrice = parseFloat($(`input[name="items[${rowId}][unit_price]"]`).val()) || 0;
    const total = quantity * unitPrice;
    
    $(`#row-total-${rowId}`).text('₱' + total.toFixed(2));
    calculateGrandTotal();
}

// Calculate grand total
function calculateGrandTotal() {
    let grandTotal = 0;
    $('.row-total').each(function() {
        const amount = parseFloat($(this).text().replace('₱', '').replace(',', '')) || 0;
        grandTotal += amount;
    });
    $('#grandTotal').text('₱' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
}

// Add low stock items
function addLowStockItems() {
    @foreach($lowStockItems as $item)
        if (!selectedItems.includes('{{ $item->id }}')) {
            selectedItems.push('{{ $item->id }}');
            itemCounter++;
            
            const suggestedQuantity = Math.max({{ $item->reorder_point }} * 2, {{ $item->reorder_point }} - {{ $item->quantity }} + 10);
            
            const newRow = `
                <tr id="item-row-${itemCounter}">
                    <td>
                        {{ $item->name }}
                        <input type="hidden" name="items[${itemCounter}][inventory_id]" value="{{ $item->id }}">
                    </td>
                    <td>{{ $item->quantity }} {{ $item->unit }}</td>
                    <td>
                        <input type="number" class="form-control quantity-input" 
                               name="items[${itemCounter}][quantity]" 
                               step="0.01" min="0.01" value="${suggestedQuantity}" required 
                               onchange="calculateRowTotal(${itemCounter})">
                    </td>
                    <td>
                        <input type="number" class="form-control unit-price-input" 
                               name="items[${itemCounter}][unit_price]" 
                               step="0.01" min="0" value="{{ $item->unit_price }}" required 
                               onchange="calculateRowTotal(${itemCounter})">
                    </td>
                    <td>
                        <span class="row-total" id="row-total-${itemCounter}">₱${(suggestedQuantity * {{ $item->unit_price }}).toFixed(2)}</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemCounter}, '{{ $item->id }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#itemsTableBody').append(newRow);
        }
    @endforeach
    
    calculateGrandTotal();
}

// Item search functionality
$('#itemSearch').on('keyup', function() {
    const searchTerm = $(this).val().toLowerCase();
    $('#availableItemsList tr').each(function() {
        const itemName = $(this).find('td:first').text().toLowerCase();
        if (itemName.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

// Form validation
$('#purchaseOrderForm').on('submit', function(e) {
    if ($('#itemsTableBody tr').length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the purchase order.');
        return false;
    }
});
</script>
@endsection
