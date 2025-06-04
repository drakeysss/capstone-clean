@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Inventory Management</h2>
                    <p class="text-muted" style="color: white;">View and manage kitchen inventory items</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Inventory Items</h5>
                    <div class="d-flex">
                        @if(Auth::user()->role === 'kitchen')
                        <button type="button" class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Item
                        </button>
                        @endif
                        <select class="form-select form-select-sm me-2" id="categoryFilter">
                            <option value="all">All Categories</option>
                            <option value="dairy">Dairy</option>
                            <option value="produce">Produce</option>
                            <option value="meat">Meat</option>
                            <option value="grains">Grains</option>
                            <option value="other">Other</option>
                        </select>
                        <select class="form-select form-select-sm" id="stockFilter">
                            <option value="all">All Items</option>
                            <option value="low">Low Stock</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Delivery Notifications -->
                    <div id="deliveryNotifications">
                        @if(isset($deliveryNotifications) && count($deliveryNotifications) > 0)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-truck me-2"></i>
                            <strong>Delivery Update:</strong> {{ count($deliveryNotifications) }} items have been delivered and added to inventory.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Category</th>
                                    <th>Last Updated</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryItems ?? [] as $item)
                                <tr class="inventory-item" data-category="{{ $item->category }}" data-stock="{{ $item->quantity < $item->reorder_level ? 'low' : 'normal' }}">
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ ucfirst($item->category) }}</td>
                                    <td>{{ $item->updated_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($item->quantity < $item->reorder_level)
                                        <span class="badge bg-danger">Low Stock</span>
                                        @else
                                        <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if(Auth::user()->role === 'kitchen')
                                            <!-- Kitchen staff can edit items -->
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-item-btn" 
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-quantity="{{ $item->quantity }}"
                                                data-unit="{{ $item->unit }}"
                                                data-category="{{ $item->category }}"
                                                data-reorder-level="{{ $item->reorder_level }}"
                                                data-bs-toggle="modal" data-bs-target="#editItemModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @endif
                                            
                                            @if(Auth::user()->role === 'cook' || Auth::user()->role === 'admin')
                                            <!-- Cook/admin can notify about deliveries -->
                                            <button type="button" class="btn btn-sm btn-outline-info notify-delivery-btn"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-bs-toggle="modal" data-bs-target="#notifyDeliveryModal">
                                                <i class="bi bi-truck"></i> Notify Delivery
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No inventory items found</td>
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

<!-- Add Item Modal -->

@push('scripts')
<script>
    // Real-time date and time display
    function updateDateTime() {
        const now = new Date();
        const dateOptions = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric'
        };
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit'
        };
        
        document.getElementById('currentDateTime').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} ${now.toLocaleTimeString('en-US', timeOptions)}`;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 60000);
    
    // Category and stock filtering
    document.addEventListener('DOMContentLoaded', function() {
        const categoryFilter = document.getElementById('categoryFilter');
        const stockFilter = document.getElementById('stockFilter');
        
        function filterItems() {
            const categoryValue = categoryFilter.value;
            const stockValue = stockFilter.value;
            
            document.querySelectorAll('.inventory-item').forEach(item => {
                const categoryMatch = categoryValue === 'all' || item.dataset.category === categoryValue;
                const stockMatch = stockValue === 'all' || item.dataset.stock === stockValue;
                
                if (categoryMatch && stockMatch) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        categoryFilter.addEventListener('change', filterItems);
        stockFilter.addEventListener('change', filterItems);
        
        // Handle edit item modal (for kitchen staff)
        document.querySelectorAll('.edit-item-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const quantity = this.dataset.quantity;
                const unit = this.dataset.unit;
                const category = this.dataset.category;
                const reorderLevel = this.dataset.reorderLevel;
                
                document.getElementById('edit_item_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_quantity').value = quantity;
                document.getElementById('edit_unit').value = unit;
                document.getElementById('edit_category').value = category;
                document.getElementById('edit_reorder_level').value = reorderLevel;
                
                document.getElementById('editItemForm').action = `/cook/inventory/${id}`;
            });
        });
        
        // Handle notify delivery modal (for cook/admin)
        document.querySelectorAll('.notify-delivery-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                document.getElementById('delivery_item_id').value = id;
                document.getElementById('delivery_item_name').textContent = name;
            });
        });
    });
</script>
@endpush
<!-- Add Item Modal (For Kitchen Staff) -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Add New Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm" action="{{ route('cook.inventory.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit</label>
                        <select class="form-select" id="unit" name="unit" required>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="g">Grams (g)</option>
                            <option value="l">Liters (l)</option>
                            <option value="ml">Milliliters (ml)</option>
                            <option value="pcs">Pieces (pcs)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="dairy">Dairy</option>
                            <option value="produce">Produce</option>
                            <option value="meat">Meat</option>
                            <option value="grains">Grains</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reorder_level" class="form-label">Reorder Level</label>
                        <input type="number" class="form-control" id="reorder_level" name="reorder_level" min="0" step="0.01" required>
                        <small class="text-muted">Minimum quantity before item is marked as low stock</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addItemForm" class="btn btn-primary">Add Item</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal (For Kitchen Staff) -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">Edit Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editItemForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_item_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_unit" class="form-label">Unit</label>
                        <select class="form-select" id="edit_unit" name="unit" required>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="g">Grams (g)</option>
                            <option value="l">Liters (l)</option>
                            <option value="ml">Milliliters (ml)</option>
                            <option value="pcs">Pieces (pcs)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Category</label>
                        <select class="form-select" id="edit_category" name="category" required>
                            <option value="dairy">Dairy</option>
                            <option value="produce">Produce</option>
                            <option value="meat">Meat</option>
                            <option value="grains">Grains</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_reorder_level" class="form-label">Reorder Level</label>
                        <input type="number" class="form-control" id="edit_reorder_level" name="reorder_level" min="0" step="0.01" required>
                        <small class="text-muted">Minimum quantity before item is marked as low stock</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editItemForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Notify Delivery Modal (For Cook/Admin) -->
<div class="modal fade" id="notifyDeliveryModal" tabindex="-1" aria-labelledby="notifyDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notifyDeliveryModalLabel">Notify Kitchen Team of Delivery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="notifyDeliveryForm" action="{{ route('cook.inventory.notify-delivery') }}" method="POST">
                    @csrf
                    <input type="hidden" id="delivery_item_id" name="item_id">
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <p id="delivery_item_name" class="form-control-static fw-bold"></p>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_quantity" class="form-label">Delivery Quantity</label>
                        <input type="number" class="form-control" id="delivery_quantity" name="quantity" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_status" class="form-label">Delivery Status</label>
                        <select class="form-select" id="delivery_status" name="status" required>
                            <option value="scheduled">Scheduled for Delivery</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="delivery_notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="notifyDeliveryForm" class="btn btn-primary">Send Notification</button>
            </div>
        </div>
    </div>
</div>
@endsection