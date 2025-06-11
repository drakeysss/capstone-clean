@extends('layouts.app')

@section('content')
<div class="col-12 mb-4">
    <div class="card border-0 bg-primary text-white overflow-hidden">
        <div class="card-body p-4 position-relative" style="background-color: var(--secondary-color);">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="fw-bold mb-1">Inventory Count</h4>
                    <p class="mb-0">Count inventory items and report to Cook</p>
                </div>
                <div class="col-auto">
                    <i class="bi bi-clipboard-check display-4 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Inventory Count Form -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inventory Count Form</h6>
            </div>
            <div class="card-body">
                <!-- Quick Reference Section -->
                @if(isset($existingItems) && $existingItems->count() > 0)
                <div class="alert alert-info mb-4">
                    <h6><i class="bi bi-info-circle me-2"></i>Quick Reference - Previously Reported Items</h6>
                    <div class="row">
                        @foreach($existingItems->take(8) as $item)
                        <div class="col-md-3 mb-2">
                            <small>
                                <strong>{{ $item->name }}</strong><br>
                                Last reported: {{ $item->quantity }} {{ $item->unit }}
                                @if($item->quantity <= $item->reorder_point)
                                    <span class="badge bg-warning">Low Stock</span>
                                @endif
                            </small>
                        </div>
                        @endforeach
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        These are items from previous reports. Please count current physical inventory below.
                    </small>
                </div>
                @else
                <div class="alert alert-warning mb-4">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>First Inventory Report</h6>
                    <p class="mb-0">
                        No previous inventory data found. Please count all items in the kitchen and report them below.
                        This will establish the baseline for future inventory management.
                    </p>
                </div>
                @endif

                <form id="inventoryCheckForm" action="{{ route('kitchen.inventory.check') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Notes for Cook</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any notes about this inventory count (e.g., items near expiry, damaged items, etc.)"></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Instructions:</strong> Count the physical inventory items and enter the details below. This report will be sent directly to the cook team for inventory management.
                        </div>
                    </div>
                    
                    <div id="inventory-items-container">
                        <div class="row inventory-item mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control" name="manual_items[0][name]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="manual_items[0][quantity]" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unit</label>
                                <select class="form-control" name="manual_items[0][unit]" required>
                                    <option value="kg">kg</option>
                                    <option value="liters">gallon</option>
                                    <option value="pieces">pieces</option>
                                    <option value="cans">cans</option>
                                    <option value="sachets">sachets</option>
                                    <option value="packs">packs</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Needs Restock</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="manual_items[0][needs_restock]" value="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Notes</label>
                                <input type="text" class="form-control" name="manual_items[0][notes]" placeholder="Notes">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <button type="button" id="add-item-btn" class="btn btn-sm btn-secondary">
                            <i class="bi bi-plus-circle"></i> Add Another Item
                        </button>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Submit Inventory Count
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemCount = 1;
        const container = document.getElementById('inventory-items-container');
        const addButton = document.getElementById('add-item-btn');

        // Prevent duplicate form submissions
        const inventoryForm = document.getElementById('inventoryCheckForm');
        if (inventoryForm) {
            inventoryForm.addEventListener('submit', function(e) {
                const submitBtn = inventoryForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // Disable button to prevent double-clicks
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

                    // Re-enable after 5 seconds in case of errors
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit Inventory Count';
                    }, 5000);
                }
            });
        }
        
        addButton.addEventListener('click', function() {
            const newItem = document.createElement('div');
            newItem.className = 'row inventory-item mb-3';
            newItem.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" class="form-control" name="manual_items[${itemCount}][name]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="manual_items[${itemCount}][quantity]" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit</label>
                    <select class="form-control" name="manual_items[${itemCount}][unit]" required>
                        <option value="kg">kg</option>
                        <option value="liters">liters</option>
                        <option value="pieces">pieces</option>
                        <option value="cans">cans</option>
                        <option value="sachets">sachets</option>
                        <option value="packs">packs</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Needs Restock</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="manual_items[${itemCount}][needs_restock]" value="1">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Notes</label>
                    <input type="text" class="form-control" name="manual_items[${itemCount}][notes]" placeholder="Notes">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-danger remove-item form-control">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            itemCount++;
            
            // Add event listener to the remove button
            const removeButton = newItem.querySelector('.remove-item');
            removeButton.addEventListener('click', function() {
                container.removeChild(newItem);
            });
        });
    });
</script>

@endsection
