@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Enhanced Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <div>
                        <h3 class="mb-1 fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i>Inventory
                        </h3>
                        <p class="mb-0 opacity-75">Count inventory items and report to Cook</p>
                    </div>
                    <div class="text-end">
                        <div id="currentDateTimeBlock" class="date-time-block">
                            <div id="currentDate" class="date-line">Date</div>
                            <div id="currentTime" class="time-line">Time</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Inventory Count Form</h6>
                </div>
                <div class="card-body">
                    <form id="inventoryCheckForm" action="{{ route('kitchen.inventory.check') }}" method="POST">
                        @csrf

                        <div id="inventory-items-container">
                            <div class="row inventory-item mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Item Name</label>
                                    <input type="text" class="form-control" name="manual_items[0][name]" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" name="manual_items[0][quantity]" min="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-control" name="manual_items[0][unit]" required>
                                        <option value="kg">kg</option>
                                        <option value="liters">liters</option>
                                        <option value="pieces">pieces</option>
                                        <option value="cans">cans</option>
                                        <option value="sachets">sachets</option>
                                        <option value="packs">packs</option>
                                    </select>
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

                        <div class="mb-3">
                            <label class="form-label">Notes for Cook</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Any notes about this inventory count (e.g., items near expiry, damaged items, etc.)"></textarea>
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

    <!-- Inventory Reports History Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                        <i class="bi bi-clock-history me-2"></i>Your Inventory Reports History
                    </h6>
                </div>
                <div class="card-body">

                    @if(isset($allChecks) && $allChecks->count() > 0)
                        <!-- Pending Reports Section -->
                        @php
                            $pendingReports = $allChecks->whereNull('approved_at');
                            $approvedReports = $allChecks->whereNotNull('approved_at');
                        @endphp

                        @if($pendingReports->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">
                                <i class="bi bi-clock"></i> Pending Reports ({{ $pendingReports->count() }})
                            </h6>
                            <div class="row">
                                @foreach($pendingReports as $check)
                            <div class="col-lg-6 mb-4">
                                <div class="card border receipt-card" id="report-card-{{ $check->id }}">
                                    <!-- Receipt Header -->
                                    <div class="card-header bg-primary text-white text-center py-3">
                                        <h5 class="mb-1">INVENTORY RECEIPT</h5>
                                        <small>{{ $check->created_at->format('M d, Y h:i A') }}</small>
                                    </div>

                                    <!-- Receipt Body -->
                                    <div class="card-body p-3">
                                        <!-- Receipt Info -->
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">DATE:</small><br>
                                                <strong>{{ $check->created_at->format('M d, Y') }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">TIME:</small><br>
                                                <strong>{{ $check->created_at->format('h:i A') }}</strong>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <small class="text-muted">SUBMITTED BY:</small><br>
                                                <strong>{{ $check->user->name ?? 'You' }}</strong>
                                            </div>
                                        </div>

                                        <!-- Items Table -->
                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm table-borderless">
                                                <thead>
                                                    <tr class="border-bottom">
                                                        <th class="text-start">ITEM</th>
                                                        <th class="text-center">QTY</th>
                                                        <th class="text-end">UNIT</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($check->items as $item)
                                                    <tr>
                                                        <td class="text-start">{{ $item->inventoryItem->name ?? 'Unknown Item' }}</td>
                                                        <td class="text-center">{{ number_format($item->current_stock, 0) }}</td>
                                                        <td class="text-end">{{ $item->inventoryItem->unit ?? 'units' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="border-top">
                                                        <th class="text-start">TOTAL ITEMS:</th>
                                                        <th class="text-center">{{ $check->items->count() }}</th>
                                                        <th class="text-end">items</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <!-- Notes -->
                                        @if($check->notes)
                                        <div class="mb-3">
                                            <small class="text-muted">NOTES:</small><br>
                                            <div class="bg-light p-2 rounded small">
                                                {{ $check->notes }}
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Cook's Reply -->
                                        @if($check->approval_notes)
                                        <div class="mb-3">
                                            <small class="text-muted">COOK'S REPLY:</small><br>
                                            <div class="bg-success bg-opacity-10 p-2 rounded small border-start border-success border-3">
                                                {{ $check->approval_notes }}
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Status -->
                                        <div class="text-center mb-3">
                                            @if($check->approved_at)
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle"></i> APPROVED
                                                </span>
                                                <br><small class="text-muted">{{ $check->approved_at->format('M d, Y h:i A') }}</small>
                                            @else
                                                <span class="badge bg-warning px-3 py-2">
                                                    <i class="bi bi-clock"></i> PENDING APPROVAL
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Action Button -->
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="confirmDelete({{ $check->id }}, '{{ $check->created_at->format('M d, Y') }}')">
                                                    <i class="bi bi-trash"></i> Delete Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Receipt Footer -->
                                    <div class="card-footer bg-light text-center py-2">
                                        <small class="text-muted">
                                            Kitchen Inventory System
                                        </small>
                                    </div>
                                </div>
                            </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Approved Reports Section -->
                        @if($approvedReports->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-success mb-3">
                                <i class="bi bi-check-circle"></i> Approved Reports ({{ $approvedReports->count() }})
                            </h6>
                            <div class="row">
                                @foreach($approvedReports as $check)
                                <div class="col-lg-6 mb-4">
                                    <div class="card border receipt-card" id="report-card-{{ $check->id }}">
                                        <!-- Receipt Header -->
                                        <div class="card-header bg-success text-white text-center py-3">
                                            <h5 class="mb-1">INVENTORY RECEIPT</h5>
                                            <small>{{ $check->created_at->format('M d, Y h:i A') }}</small>
                                        </div>

                                        <!-- Receipt Body -->
                                        <div class="card-body p-3">
                                            <!-- Receipt Info -->
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <small class="text-muted">DATE:</small><br>
                                                    <strong>{{ $check->created_at->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">TIME:</small><br>
                                                    <strong>{{ $check->created_at->format('h:i A') }}</strong>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <small class="text-muted">SUBMITTED BY:</small><br>
                                                    <strong>{{ $check->user->name ?? 'You' }}</strong>
                                                </div>
                                            </div>

                                            <!-- Items Table -->
                                            <div class="table-responsive mb-3">
                                                <table class="table table-sm table-borderless">
                                                    <thead>
                                                        <tr class="border-bottom">
                                                            <th class="text-start">ITEM</th>
                                                            <th class="text-center">QTY</th>
                                                            <th class="text-end">UNIT</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($check->items as $item)
                                                        <tr>
                                                            <td class="text-start">{{ $item->inventoryItem->name ?? 'Unknown Item' }}</td>
                                                            <td class="text-center">{{ number_format($item->current_stock, 0) }}</td>
                                                            <td class="text-end">{{ $item->inventoryItem->unit ?? 'units' }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="border-top">
                                                            <th class="text-start">TOTAL ITEMS:</th>
                                                            <th class="text-center">{{ $check->items->count() }}</th>
                                                            <th class="text-end">items</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <!-- Notes -->
                                            @if($check->notes)
                                            <div class="mb-3">
                                                <small class="text-muted">NOTES:</small><br>
                                                <div class="bg-light p-2 rounded small">
                                                    {{ $check->notes }}
                                                </div>
                                            </div>
                                            @endif

                                            <!-- Cook's Reply -->
                                            @if($check->approval_notes)
                                            <div class="mb-3">
                                                <small class="text-muted">COOK'S REPLY:</small><br>
                                                <div class="bg-success bg-opacity-10 p-2 rounded small border-start border-success border-3">
                                                    {{ $check->approval_notes }}
                                                </div>
                                            </div>
                                            @endif

                                            <!-- Status -->
                                            <div class="text-center mb-3">
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle"></i> APPROVED
                                                </span>
                                                <br><small class="text-muted">{{ $check->approved_at->format('M d, Y h:i A') }}</small>
                                            </div>

                                            <!-- Action Button -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="confirmDelete({{ $check->id }}, '{{ $check->created_at->format('M d, Y') }}')">
                                                        <i class="bi bi-trash"></i> Delete Report
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Receipt Footer -->
                                        <div class="card-footer bg-light text-center py-2">
                                            <small class="text-muted">
                                                Kitchen Inventory System
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Pagination -->
                        @if($allChecks->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $allChecks->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Reports Yet</h5>
                            <p class="text-muted">
                                You haven't submitted any inventory reports yet.<br>
                                Submit your first report using the form above.
                            </p>
                        </div>
                    @endif
                </div>
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
                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="manual_items[${itemCount}][quantity]" min="0" required>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label">Notes</label>
                    <input type="text" class="form-control" name="manual_items[${itemCount}][notes]" placeholder="Notes">
                </div>
            `;
            container.appendChild(newItem);
            itemCount++;
        });

        // Delete confirmation and AJAX functions
        window.confirmDelete = function(reportId, reportDate) {
            if (confirm(`Are you sure you want to delete the inventory report from ${reportDate}?\n\nThis action cannot be undone.`)) {
                deleteReport(reportId);
            }
        };

        function deleteReport(reportId) {
            // Show loading state
            const card = document.getElementById(`report-card-${reportId}`);
            if (card) {
                card.style.opacity = '0.5';
                card.style.pointerEvents = 'none';
            }

            fetch(`/kitchen/inventory/${reportId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the card with animation
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.transform = 'scale(0.8)';
                        card.style.opacity = '0';

                        setTimeout(() => {
                            card.remove();

                            // Check if no more cards and reload page if needed
                            const remainingCards = document.querySelectorAll('[id^="report-card-"]');
                            if (remainingCards.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }

                    // Show success message
                    showAlert('success', data.message);
                } else {
                    // Restore card state
                    if (card) {
                        card.style.opacity = '1';
                        card.style.pointerEvents = 'auto';
                    }
                    showAlert('danger', data.message || 'Failed to delete report');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Restore card state
                if (card) {
                    card.style.opacity = '1';
                    card.style.pointerEvents = 'auto';
                }
                showAlert('danger', 'An error occurred while deleting the report');
            });
        }



        function showAlert(type, message) {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Add to page
            document.body.appendChild(alertDiv);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    });
</script>

@endsection

@push('styles')
<style>
.card.border-0.bg-primary {
    background: linear-gradient(135deg, #22bbea 0%, #1e9bd8 100%) !important;
    color: #fff !important;
    border-radius: 1rem !important;
    box-shadow: 0 8px 25px rgba(34, 187, 234, 0.15) !important;
    margin-bottom: 2rem !important;
}
.card.shadow.mb-4 {
    border-radius: 1rem !important;
    box-shadow: 0 2px 16px rgba(34, 187, 234, 0.10) !important;
    border: none;
}
.card-header.py-3 {
    background: #f8f9fa !important;
    border-top-left-radius: 1rem !important;
    border-top-right-radius: 1rem !important;
    font-weight: 600;
    font-size: 1.15rem;
    color: #22bbea !important;
    border-bottom: 1px solid #e3e6ea !important;
}
.btn-outline-danger {
    border: 2px solid #dc3545 !important;
    color: #dc3545 !important;
    background: #fff !important;
    font-weight: 600;
    transition: all 0.2s;
}
.btn-outline-danger:hover {
    background: #dc3545 !important;
    color: #fff !important;
    border-color: #dc3545 !important;
}
.btn-outline-primary {
    border: 2px solid #22bbea !important;
    color: #22bbea !important;
    background: #fff !important;
    font-weight: 600;
    transition: all 0.2s;
}
.btn-outline-primary:hover {
    background: #22bbea !important;
    color: #fff !important;
    border-color: #22bbea !important;
}

.receipt-card {
    border: 2px solid #dee2e6 !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    background: #fff !important;
}
.receipt-card .card-header {
    background: linear-gradient(135deg, #22bbea, #1a9bd1) !important;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    color: #fff !important;
}
.receipt-card .card-body {
    background: #fff !important;
    color: #333 !important;
}
.receipt-card .text-muted {
    color: #666 !important;
}
.receipt-card strong {
    color: #000 !important;
    font-weight: 600 !important;
}
.table-borderless td, .table-borderless th {
    border: none !important;
    padding: 0.25rem 0.5rem !important;
    color: #333 !important;
}
.table-borderless .border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}
.table-borderless .border-top {
    border-top: 1px solid #dee2e6 !important;
}
.receipt-card .bg-light {
    background-color: #f8f9fa !important;
    color: #333 !important;
}
.receipt-card .bg-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
    color: #333 !important;
    border-left: 3px solid #198754 !important;
}
.rounded-pill {
    border-radius: 50rem !important;
}
.table {
    border-radius: 0.75rem !important;
    overflow: hidden;
}
.table thead {
    background: #f8f9fa !important;
    color: #22bbea !important;
    font-weight: 600;
}
.table-hover tbody tr:hover {
    background: #eaf6fb !important;
}
.badge.bg-info {
    background: #22bbea !important;
    color: #fff !important;
}
.badge.bg-success {
    background: #28a745 !important;
    color: #fff !important;
}
.badge.bg-warning {
    background: #ffc107 !important;
    color: #856404 !important;
}
.badge.bg-danger {
    background: #dc3545 !important;
    color: #fff !important;
}
.inventory-item {
    margin-bottom: 1rem !important;
}
.table-responsive { overflow-x: auto; }
.table td, .table th { word-break: break-word !important; white-space: normal !important; }
.date-time-block { text-align: center; }
.date-line { font-size: 1.15rem; font-weight: 500; }
.time-line { font-size: 1rem; font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateDateTimeBlock() {
        const now = new Date();
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', dateOptions);
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', timeOptions);
    }
    updateDateTimeBlock();
    setInterval(updateDateTimeBlock, 1000);
});
</script>
@endpush
