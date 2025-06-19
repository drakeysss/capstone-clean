@extends('layouts.app')

@section('content')
<div class="col-12 mb-4">
    <div class="card border-0 bg-primary text-white overflow-hidden">
        <div class="card-body p-4 position-relative" style="background-color: var(--secondary-color);">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="fw-bold mb-1"><i class="bi bi-clipboard-check me-2"></i>Inventory</h4>
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
                <div>
                    @if(isset($stats) && $stats['total_reports'] > 0)
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="confirmDeleteAll()">
                            <i class="bi bi-trash me-1"></i>Delete All Reports
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if(isset($stats))
                    <!-- Statistics Row -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="h4 mb-0 text-primary">{{ $stats['total_reports'] }}</div>
                                <small class="text-muted">Total Reports</small>
                            </div>
                        </div>
                        <!-- <div class="col-md-4">
                            <div class="text-center">
                                <div class="h4 mb-0 text-info">{{ $stats['total_items_reported'] ?? 0 }}</div>
                                <small class="text-muted">Items Reported</small>
                            </div> -->
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="h4 mb-0 text-success">
                                    {{ $stats['last_report_date'] ? $stats['last_report_date']->diffForHumans() : 'Never' }}
                                </div>
                                <small class="text-muted">Last Report</small>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($allChecks) && $allChecks->count() > 0)
                    <!-- Reports Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Items Count</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allChecks as $check)
                                <tr id="report-row-{{ $check->id }}">
                                    <td>
                                        <strong>{{ $check->created_at->format('M d, Y') }}</strong><br>
                                        <small class="text-muted">{{ $check->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $check->items->count() }} items</span>
                                        @if($check->items->where('needs_restock', true)->count() > 0)
                                            <br><small class="text-warning">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                {{ $check->items->where('needs_restock', true)->count() }} need restock
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($check->notes)
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $check->notes }}">
                                                {{ $check->notes }}
                                            </span>
                                        @else
                                            <span class="text-muted">No notes</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($check->approved_at)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Approved
                                            </span>
                                            <br><small class="text-muted">{{ $check->approved_at->diffForHumans() }}</small>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="confirmDelete({{ $check->id }}, '{{ $check->created_at->format('M d, Y') }}')"
                                                title="Delete Report">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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
            
            // Add event listener to the remove button
            const removeButton = newItem.querySelector('.remove-item');
            removeButton.addEventListener('click', function() {
                container.removeChild(newItem);
            });
        });

        // Delete confirmation and AJAX functions
        window.confirmDelete = function(reportId, reportDate) {
            if (confirm(`Are you sure you want to delete the inventory report from ${reportDate}?\n\nThis action cannot be undone.`)) {
                deleteReport(reportId);
            }
        };

        window.confirmDeleteAll = function() {
            if (confirm('Are you sure you want to delete ALL your inventory reports?\n\nThis will permanently remove all your submitted reports and cannot be undone.')) {
                deleteAllReports();
            }
        };

        function deleteReport(reportId) {
            // Show loading state
            const row = document.getElementById(`report-row-${reportId}`);
            if (row) {
                row.style.opacity = '0.5';
                row.style.pointerEvents = 'none';
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
                    // Remove the row with animation
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.transform = 'translateX(-100%)';
                        row.style.opacity = '0';

                        setTimeout(() => {
                            row.remove();

                            // Check if table is empty and reload page if needed
                            const tbody = document.querySelector('tbody');
                            if (tbody && tbody.children.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }

                    // Show success message
                    showAlert('success', data.message);
                } else {
                    // Restore row state
                    if (row) {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'auto';
                    }
                    showAlert('danger', data.message || 'Failed to delete report');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Restore row state
                if (row) {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                }
                showAlert('danger', 'An error occurred while deleting the report');
            });
        }

        function deleteAllReports() {
            // Show loading state
            const deleteAllBtn = document.querySelector('button[onclick="confirmDeleteAll()"]');
            if (deleteAllBtn) {
                deleteAllBtn.disabled = true;
                deleteAllBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
            }

            fetch('/kitchen/inventory/delete-all/reports', {
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
                    showAlert('success', data.message);
                    // Reload page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', data.message || 'Failed to delete reports');
                    // Restore button state
                    if (deleteAllBtn) {
                        deleteAllBtn.disabled = false;
                        deleteAllBtn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete All Reports';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while deleting reports');
                // Restore button state
                if (deleteAllBtn) {
                    deleteAllBtn.disabled = false;
                    deleteAllBtn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete All Reports';
                }
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
</style>
@endpush
