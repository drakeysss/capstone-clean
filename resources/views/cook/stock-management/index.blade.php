@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #22bbea, #1a9bd1); color: #fff;">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <i class="bi bi-receipt fs-1 me-3"></i>
                        <div>
                            <h3 class="mb-1" style="color: #fff;">Stock Management</h3>
                            <p class="mb-0" style="color: #e0f7fa;">Review kitchen inventory reports in receipt format</p>
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



    <!-- Inventory Reports in Receipt Style -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>Kitchen Inventory Reports (Receipt Style)
                    </h5>
                    <div>
                        @if($recentChecks->count() > 0)
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteAll()">
                                <i class="bi bi-trash me-1"></i>Delete All Reports
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($recentChecks->count() > 0)
                        <div class="row justify-content-center">
                            @foreach($recentChecks->take(1) as $check)
                            <div class="col-lg-8 mb-4">
                                <div class="card border receipt-card">
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
                                                <small class="text-muted">REPORTED BY:</small><br>
                                                <strong>{{ $check->user->name ?? 'Unknown' }}</strong>
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

                                        <!-- Cook's Reply Section -->
                                        @if($check->approval_notes)
                                        <div class="mb-3">
                                            <small class="text-muted">COOK'S REPLY:</small><br>
                                            <div class="bg-light p-2 rounded small">
                                                {{ $check->approval_notes }}
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Combined Approve & Reply Section -->
                                        @if(!$check->approved_at)
                                        <div class="mb-3">
                                            <form action="/cook/stock-management/reports/{{ $check->id }}/approve" method="POST">
                                                @csrf
                                                <div class="mb-2">
                                                    <label class="form-label small text-muted">Reply to Kitchen Team:</label>
                                                    <textarea name="approval_notes" class="form-control form-control-sm" rows="2" placeholder="Type your reply to kitchen team (optional)..."></textarea>
                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="confirmDelete({{ $check->id }})">
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

                        <!-- Pagination -->
                        @if($recentChecks->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $recentChecks->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-receipt fs-1 text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Inventory Reports</h5>
                            <p class="text-muted">
                                No inventory reports have been submitted by the kitchen team yet.<br>
                                Reports will appear here once kitchen staff submit inventory counts.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete All Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Are you sure you want to delete ALL inventory reports? This action cannot be undone.
                </div>
                <p>This will permanently remove all inventory reports from the system.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteAllReports()">
                    <i class="bi bi-trash"></i> Delete All Reports
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.date-time-block { text-align: center; color: #fff; }
.date-line { font-size: 1.15rem; font-weight: 500; }
.time-line { font-size: 1rem; font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace; }
.card { border-radius: 1rem !important; }
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update date/time
    function updateDateTimeBlock() {
        const now = new Date();
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', dateOptions);
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', timeOptions);
    }
    updateDateTimeBlock();
    setInterval(updateDateTimeBlock, 1000);

    // Delete confirmation
    window.confirmDelete = function(reportId) {
        if (confirm('Are you sure you want to delete this inventory report?\n\nThis action cannot be undone.')) {
            deleteReport(reportId);
        }
    };

    // Delete all confirmation
    window.confirmDeleteAll = function() {
        new bootstrap.Modal(document.getElementById('deleteAllModal')).show();
    };

    // Delete single report
    function deleteReport(reportId) {
        fetch(`/cook/stock-management/reports/${reportId}`, {
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
                location.reload();
            } else {
                alert('Failed to delete report: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the report');
        });
    }

    // Delete all reports
    window.deleteAllReports = function() {
        fetch('/cook/stock-management/clear-all', {
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
                location.reload();
            } else {
                alert('Failed to delete reports: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting reports');
        });
    };
});
</script>
@endpush
