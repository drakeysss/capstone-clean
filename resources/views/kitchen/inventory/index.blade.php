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
                <form id="inventoryCheckForm" action="{{ route('kitchen.inventory.check') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Notes for Cook</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any notes about this inventory count"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-muted">Please count the physical inventory items and enter the details below:</p>
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
