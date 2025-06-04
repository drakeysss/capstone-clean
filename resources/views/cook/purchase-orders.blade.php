@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Purchase Orders</h2>
                    <p class="text-muted" style="color: white;">Manage purchase orders for ingredients and supplies</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Management Section -->
    <div class="row">
        <div class="col-xl-4 mb-4">
            <div class="card main-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Create Purchase Order</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cook.purchase-orders.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">Select supplier</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="order_date" class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="order_date" name="order_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control" id="expected_delivery_date" name="expected_delivery_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Order Items</label>
                            <div id="order-items-container">
                                <div class="order-item mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Item</label>
                                            <select class="form-select item-select" name="items[0][item_id]" required>
                                                <option value="">Select item</option>
                                                @foreach($inventoryItems ?? [] as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" class="form-control" name="items[0][quantity]" min="1" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Unit Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="items[0][unit_price]" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-item-btn" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Another Item
                            </button>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Create Purchase Order</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Purchase Orders</h5>
                    <div>
                        <select id="statusFilter" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Supplier</th>
                                    <th>Order Date</th>
                                    <th>Delivery Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders ?? [] as $order)
                                <tr class="po-item" data-status="{{ $order->status }}">
                                    <td>PO-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                    <td>{{ date('M d, Y', strtotime($order->order_date)) }}</td>
                                    <td>{{ date('M d, Y', strtotime($order->expected_delivery_date)) }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($order->status == 'pending') bg-warning
                                            @elseif($order->status == 'approved') bg-info
                                            @elseif($order->status == 'delivered') bg-success
                                            @elseif($order->status == 'cancelled') bg-danger
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary view-po-btn" 
                                                data-id="{{ $order->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if($order->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-outline-success approve-po-btn" 
                                                data-id="{{ $order->id }}">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger cancel-po-btn" 
                                                data-id="{{ $order->id }}">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                            @endif
                                            @if($order->status == 'approved')
                                            <button type="button" class="btn btn-sm btn-outline-info deliver-po-btn" 
                                                data-id="{{ $order->id }}">
                                                <i class="bi bi-truck"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No purchase orders found</td>
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

<!-- View Purchase Order Modal -->
<div class="modal fade" id="viewPOModal" tabindex="-1" aria-labelledby="viewPOModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPOModalLabel">Purchase Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>PO #:</strong> <span id="view-po-number"></span></p>
                        <p><strong>Supplier:</strong> <span id="view-po-supplier"></span></p>
                        <p><strong>Status:</strong> <span id="view-po-status"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Order Date:</strong> <span id="view-po-order-date"></span></p>
                        <p><strong>Expected Delivery:</strong> <span id="view-po-delivery-date"></span></p>
                        <p><strong>Total Amount:</strong> <span id="view-po-total"></span></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p><strong>Notes:</strong></p>
                    <p id="view-po-notes"></p>
                </div>
                
                <h6>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="view-po-items">
                            <!-- Items will be added dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-po-btn">Print</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .po-item.hidden {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Real-time date and time display
    function updateDateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit'
        };
        
        document.getElementById('currentDateTime').innerHTML = `${now.toLocaleDateString('en-US', options)} ${now.toLocaleTimeString('en-US', timeOptions)}`;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 60000);
    
    // Status filtering
    document.getElementById('statusFilter').addEventListener('change', function() {
        const filterValue = this.value;
        const poItems = document.querySelectorAll('.po-item');
        
        poItems.forEach(item => {
            if (filterValue === 'all' || item.dataset.status === filterValue) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
    
    // Add order item
    let itemCount = 1;
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('order-items-container');
        const newItem = document.createElement('div');
        newItem.className = 'order-item mb-3 p-3 border rounded';
        newItem.innerHTML = `
            <div class="d-flex justify-content-end mb-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Item</label>
                    <select class="form-select item-select" name="items[${itemCount}][item_id]" required>
                        <option value="">Select item</option>
                        @foreach($inventoryItems ?? [] as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="items[${itemCount}][quantity]" min="1" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Unit Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="items[${itemCount}][unit_price]" step="0.01" min="0" required>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        itemCount++;
        
        // Add event listener to remove button
        newItem.querySelector('.remove-item-btn').addEventListener('click', function() {
            container.removeChild(newItem);
        });
    });
    
    // View purchase order details
    document.querySelectorAll('.view-po-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            
            // In a real application, you would fetch the PO details from the server
            // For now, we'll just show a placeholder modal
            document.getElementById('view-po-number').textContent = `PO-${id.padStart(5, '0')}`;
            document.getElementById('view-po-supplier').textContent = 'Sample Supplier';
            document.getElementById('view-po-status').textContent = 'Pending';
            document.getElementById('view-po-order-date').textContent = '2023-05-15';
            document.getElementById('view-po-delivery-date').textContent = '2023-05-20';
            document.getElementById('view-po-total').textContent = '$1,250.00';
            document.getElementById('view-po-notes').textContent = 'This is a sample purchase order.';
            
            // Sample items
            document.getElementById('view-po-items').innerHTML = `
                <tr>
                    <td>Rice (kg)</td>
                    <td>50</td>
                    <td>$2.50</td>
                    <td>$125.00</td>
                </tr>
                <tr>
                    <td>Chicken (kg)</td>
                    <td>25</td>
                    <td>$5.00</td>
                    <td>$125.00</td>
                </tr>
            `;
            
            const viewPOModal = new bootstrap.Modal(document.getElementById('viewPOModal'));
            viewPOModal.show();
        });
    });
    
    // Print purchase order
    document.getElementById('print-po-btn').addEventListener('click', function() {
        window.print();
    });
</script>
@endpush
