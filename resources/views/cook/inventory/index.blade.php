@extends('layouts.app')

@section('title', 'Stock Management - Cook Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Gradient Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #22bbea, #1a9bd1); color: #fff;">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <i class="bi bi-clipboard-check fs-1 me-3"></i>
                        <div>
                            <h3 class="mb-1" style="color: #fff;">Stock Management</h3>
                            <p class="mb-0" style="color: #e0f7fa;">Review kitchen inventory reports and approve restocking decisions</p>
                        </div>
                    </div>
                    <div id="currentDateTimeBlock" class="date-time-block">
                        <div id="currentDate" class="date-line">Date</div>
                        <div id="currentTime" class="time-line">Time</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
    
    <!-- Kitchen Inventory Reports -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Kitchen Inventory Reports</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <form method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Reports</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="needs_restock" {{ request('status') == 'needs_restock' ? 'selected' : '' }}>Needs Restock</option>
                            </select>
                        </form>
                        @if($recentChecks->count() > 0)
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmClearAllReports({{ $recentChecks->total() }})">
                                <i class="bi bi-trash3 me-1"></i>Clear All
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($recentChecks as $check)
                        <div class="border-bottom p-4 notification-item"
                             data-inventory-created="{{ $check->created_at->toISOString() }}"
                             data-created-at="{{ $check->created_at->toISOString() }}"
                             data-timestamp="{{ $check->created_at->toISOString() }}">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="bg-{{ $check->approved_at ? 'success' : 'warning' }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-{{ $check->approved_at ? 'check-circle' : 'clock' }}"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Inventory Report</h6>
                                            <p class="mb-1">
                                                <strong>Submitted by:</strong> {{ $check->user->name ?? 'Kitchen Staff' }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Date:</strong> {{ $check->created_at->format('M d, Y \a\t g:i A') }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Items Reported:</strong> {{ $check->items->count() }} items
                                            </p>
                                            @if($check->notes)
                                                <p class="mb-0 text-muted">
                                                    <strong>Notes:</strong> {{ Str::limit($check->notes, 100) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="mb-2">
                                        @if($check->approved_at)
                                            <span class="badge bg-success">Approved</span>
                                            <br><small class="text-muted">{{ $check->approved_at->format('M d, Y') }}</small>
                                        @else
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @endif
                                        @php
                                            $restockItems = $check->items->where('needs_restock', true)->count();
                                        @endphp
                                        @if($restockItems > 0)
                                            <br><span class="badge bg-danger">{{ $restockItems }} items need restock</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('cook.inventory.show-report', $check->id) }}" class="btn btn-outline-primary btn-sm me-2">
                                            <i class="bi bi-eye me-1"></i>Review & Approve
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="confirmDeleteReport({{ $check->id }}, '{{ $check->user->name ?? 'Kitchen Staff' }}', '{{ $check->created_at->format('M d, Y') }}')">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center">
                            <div class="mb-4">
                                <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                            </div>
                            <h4 class="text-muted">Waiting for Kitchen Reports</h4>
                            <p class="text-muted mb-4">
                                The kitchen team has not submitted any inventory reports yet.<br>
                                Stock management requires kitchen staff to submit inventory counts first.
                            </p>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>How it works:</strong>
                                <ol class="text-start mt-2 mb-0">
                                    <li>Kitchen staff counts physical inventory</li>
                                    <li>Kitchen submits inventory report to cook</li>
                                    <li>Cook reviews and approves restocking decisions</li>
                                    <li>Cook records approved deliveries/restocks</li>
                                </ol>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if($recentChecks->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $recentChecks->links() }}
                </div>
            @endif
        </div>
    </div>


</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteReportModal" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteReportModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Inventory Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
                <p>Are you sure you want to delete this inventory report?</p>
                <div class="bg-light p-3 rounded">
                    <strong>Report Details:</strong><br>
                    <span id="delete_report_info"></span>
                </div>
                <p class="mt-3 text-muted">
                    <small>The kitchen team will be notified about this deletion.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear All Confirmation Modal -->
<div class="modal fade" id="clearAllReportsModal" tabindex="-1" aria-labelledby="clearAllReportsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="clearAllReportsModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Clear All Inventory Reports
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>DANGER:</strong> This action will permanently delete ALL inventory reports!
                </div>
                <p>Are you sure you want to delete all inventory reports? This action cannot be undone.</p>
                <div class="bg-light p-3 rounded">
                    <strong>This will delete:</strong><br>
                    <span id="clear_all_count"></span> inventory reports<br>
                    <small class="text-muted">All kitchen staff will be notified about this action.</small>
                </div>
                <div class="mt-3">
                    <label for="clearAllReason" class="form-label">Reason for clearing all reports (optional):</label>
                    <textarea class="form-control" id="clearAllReason" rows="2" placeholder="e.g., End of month cleanup, System reset, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmClearAllBtn">
                    <i class="bi bi-trash3 me-1"></i>Clear All Reports
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
/* ULTIMATE MODAL FIXES - HIGHEST PRIORITY */
.modal {
    z-index: 999999 !important;
    position: fixed !important;
}

.modal-backdrop {
    z-index: 999998 !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    pointer-events: auto !important;
}

.modal.show {
    z-index: 999999 !important;
    display: block !important;
}

.modal-dialog {
    z-index: 1000000 !important;
    position: relative !important;
    pointer-events: auto !important;
}

.modal-content {
    z-index: 1000001 !important;
    position: relative !important;
    pointer-events: auto !important;
}

/* Ensure modals are clickable */
#deleteReportModal, #clearAllReportsModal {
    z-index: 999999 !important;
    pointer-events: auto !important;
}

#deleteReportModal .modal-dialog, #clearAllReportsModal .modal-dialog {
    pointer-events: auto !important;
}

#deleteReportModal .modal-content, #clearAllReportsModal .modal-content {
    pointer-events: auto !important;
}

.date-time-block { text-align: center; min-width: 150px; }
.date-line { font-size: 1.15rem; font-weight: 500; }
.time-line { font-size: 1rem; font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace; }
</style>

<script>
let reportToDelete = null;

// SIMPLE MODAL FUNCTIONS - NO BOOTSTRAP DEPENDENCY
function showModalSimple(modalId) {
    const modalElement = document.getElementById(modalId);
    if (!modalElement) return;

    // Clean up any existing stuff
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.style.overflow = 'hidden';

    // Show modal manually
    modalElement.style.cssText = `
        display: block !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 999999 !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        pointer-events: auto !important;
    `;

    modalElement.classList.add('show');

    // Style the dialog
    const modalDialog = modalElement.querySelector('.modal-dialog');
    if (modalDialog) {
        modalDialog.style.cssText = `
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            z-index: 1000000 !important;
            pointer-events: auto !important;
            margin: 0 !important;
        `;
    }

    // Ensure content is clickable
    const modalContent = modalElement.querySelector('.modal-content');
    if (modalContent) {
        modalContent.style.cssText = `
            pointer-events: auto !important;
            z-index: 1000001 !important;
            background: white !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        `;
    }

    // Make all inputs clickable
    modalElement.querySelectorAll('input, textarea, button, select').forEach(el => {
        el.style.pointerEvents = 'auto';
    });

    // Close on backdrop click
    modalElement.onclick = function(e) {
        if (e.target === modalElement) {
            hideModalSimple(modalId);
        }
    };

    // Close button functionality
    modalElement.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(btn => {
        btn.onclick = function() {
            hideModalSimple(modalId);
        };
    });
}

function hideModalSimple(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        document.body.style.overflow = '';
    }
}

function confirmDeleteReport(reportId, submittedBy, submittedDate) {
    reportToDelete = reportId;

    // Update modal content
    document.getElementById('delete_report_info').innerHTML = `
        <strong>Inventory Report</strong><br>
        Submitted by: ${submittedBy}<br>
        Date: ${submittedDate}
    `;

    // Show modal using simple modal function
    showModalSimple('deleteReportModal');
}

function confirmClearAllReports(totalCount) {
    // Update modal content
    document.getElementById('clear_all_count').textContent = totalCount;

    // Show modal using simple modal function
    showModalSimple('clearAllReportsModal');
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (!reportToDelete) return;

            const button = this;
            const originalText = button.innerHTML;

            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';

            // Send delete request
            fetch(`/cook/stock-management/reports/${reportToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide modal using simple modal function
                    hideModalSimple('deleteReportModal');

                    // Show success message
                    showAlert('success', 'Inventory report deleted successfully!');

                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to delete report');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showAlert('danger', 'Failed to delete inventory report. Please try again.');

                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    }

    // Handle clear all confirmation
    const confirmClearAllBtn = document.getElementById('confirmClearAllBtn');
    if (confirmClearAllBtn) {
        confirmClearAllBtn.addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            const reason = document.getElementById('clearAllReason').value;

            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Clearing All...';

            // Send clear all request
            fetch('/cook/stock-management/clear-all', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide modal using simple modal function
                    hideModalSimple('clearAllReportsModal');

                    // Show success message
                    showAlert('success', data.message);

                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to clear all reports');
                }
            })
            .catch(error => {
                console.error('Clear all error:', error);
                showAlert('danger', 'Failed to clear all inventory reports. Please try again.');

                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    }

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

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
