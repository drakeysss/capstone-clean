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
                            <i class="bi bi-clipboard-data me-2"></i>Post-Meal Report
                        </h3>
                        <p class="mb-0 opacity-75">Track and analyze leftover food to improve meal planning efficiency</p>
                    </div>
                    <div class="text-end">
                        <div id="currentDateTimeBlock" class="date-time-block">
                            <div id="currentDate" class="date-line"></div>
                            <div id="currentTime" class="time-line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title">Filter Reports</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cook.post-assessment') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ $date }}">
                            </div>
                            <div class="col-md-4">
                                <label for="meal_type" class="form-label">Meal Type</label>
                                <select class="form-select" id="meal_type" name="meal_type">
                                    <option value="">All Meal Types</option>
                                    <option value="breakfast" {{ $mealType === 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                    <option value="lunch" {{ $mealType === 'lunch' ? 'selected' : '' }}>Lunch</option>
                                    <option value="dinner" {{ $mealType === 'dinner' ? 'selected' : '' }}>Dinner</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                                <a href="{{ route('cook.post-assessment') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div></div>
    
    </div>
    <div class="col-12 mb-4">
        <div class="card main-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="bi bi-clipboard-data me-2"></i>
                            Kitchen Leftover Reports
                        </h5>
                        <p class="text-muted mb-0">Reports submitted by kitchen team</p>
                    </div>
                    @if($assessments->count() > 0)
            <button id="delete-all-btn" class="btn btn-danger">
                <i class="bi bi-trash"></i> Delete All
            </button>
        @endif
                    <div id="bulk-actions" style="display: none;">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Meal Type</th>
                                <th>Food Items</th>
                                <th>Submitted By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assessments as $assessment)
                            <tr class="assessment-item"
                                data-assessment-created="{{ $assessment->completed_at ? $assessment->completed_at->toISOString() : $assessment->created_at->toISOString() }}"
                                data-assessment-id="{{ $assessment->id }}"
                                data-meal-type="{{ $assessment->meal_type }}">
                                <td>
                                    <strong>{{ $assessment->date->format('M d, Y') }}</strong>
                                    <br>
                                    <span class="text-muted">{{ $assessment->completed_at ? $assessment->completed_at->format('g:i A') : $assessment->created_at->format('g:i A') }}</span>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($assessment->meal_type === 'breakfast') bg-warning
                                        @elseif($assessment->meal_type === 'lunch') bg-primary
                                        @else bg-info
                                        @endif">
                                        {{ ucfirst($assessment->meal_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($assessment->items && count($assessment->items) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($assessment->items, 0, 3) as $item)
                                                <span class="badge bg-light text-dark border">{{ $item['name'] ?? 'Unnamed' }}</span>
                                            @endforeach
                                            @if(count($assessment->items) > 3)
                                                <span class="badge bg-secondary">+{{ count($assessment->items) - 3 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No items specified</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-muted"></i>
                                        <div>
                                            <strong>{{ $assessment->assessedBy->name ?? 'Kitchen Team' }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-report-btn"
                                            data-id="{{ $assessment->id }}"
                                            data-date="{{ $assessment->date->format('Y-m-d') }}"
                                            data-meal-type="{{ $assessment->meal_type }}"
                                            data-notes="{{ $assessment->notes }}"
                                            data-image-path="{{ $assessment->image_path }}"
                                            data-items="{{ $assessment->items ? json_encode($assessment->items) : '[]' }}"
                                            data-submitted-by="{{ $assessment->assessedBy->name ?? 'Kitchen Team' }}"
                                            data-submitted-at="{{ $assessment->completed_at->format('M d, Y g:i A') }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-assessment-btn"
                                            data-id="{{ $assessment->id }}"
                                            data-date="{{ $assessment->date->format('Y-m-d') }}"
                                            data-meal-type="{{ $assessment->meal_type }}"
                                            data-submitted-by="{{ $assessment->assessedBy->name ?? 'Kitchen Team' }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <div class="mb-4">
                                            <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                        </div>
                                        <h4 class="text-muted">Waiting for Kitchen Reports</h4>
                                        <p class="text-muted mb-4">
                                            The kitchen team hasn't submitted any post-assessment reports yet.<br>
                                            Reports will appear here once kitchen staff complete their assessments.
                                        </p>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>How it works:</strong>
                                            <ol class="text-start mt-2 mb-0">
                                                <li>Kitchen staff prepares meals</li>
                                                <li>Kitchen assesses leftover food after service</li>
                                                <li>Kitchen submits post-assessment reports</li>
                                                <li>Cook reviews reports to optimize portions</li>
                                            </ol>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="bulk-actions" style="display: none; margin-bottom: 1rem;">
                    <button id="bulk-delete-btn" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Selected (<span id="selected-count">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->role === 'kitchen')
    <!-- Notification for Kitchen Staff -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title">Waste Assessment Notifications</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Your waste assessment data has been recorded. The admin will be notified of your submission.
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- View Report Modal (For Cook) -->
                <div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" style="margin-top: 100px;">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReportModalLabel">
                    <i class="bi bi-clipboard-data me-2"></i>
                    Kitchen Leftover Report Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
          
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Date</label>
                            <p id="view_report_date" class="form-control-static"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Meal Type</label>
                            <p id="view_report_meal_type" class="form-control-static"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Submitted By</label>
                            <p id="view_report_submitted_by" class="form-control-static"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Submitted At</label>
                            <p id="view_report_submitted_at" class="form-control-static"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kitchen Notes</label>
                            <div id="view_report_notes" class="border rounded p-3 bg-light" style="min-height: 150px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Food Items Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-list-ul me-2"></i>Food Items Reported
                            </label>
                            <div id="view_report_items" class="border rounded p-3 bg-light">
                                <!-- Items will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Section -->
                <div class="row mt-4" id="report_image_section">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-camera me-2"></i>Attached Photo
                            </label>
                            <div class="border rounded p-3 text-center bg-light">
                                <img id="view_report_image" src="" alt="Leftover Report Photo"
                                     class="img-fluid rounded shadow" style="max-width: 100%; max-height: 400px; cursor: pointer;"
                                     onclick="openImageModal(this.src)">
                                <div class="mt-2">
                                    <small class="text-muted">Click image to view full size</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Full Size Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered image-modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">
                    <i class="bi bi-camera me-2"></i>Leftover Report Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center align-items-center" style="min-height: 400px;">
                <img id="fullSizeImage" src="" alt="Full Size Report Photo" class="img-fluid rounded shadow" style="max-width: 80vw; max-height: 60vh; object-fit: contain; display: block; margin: 0 auto;">
            </div>
            <div class="modal-footer justify-content-between">
                <span class="text-muted">Click image to view full size</span>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="downloadImageBtn" href="" download class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>Download Image
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-3">Are you sure you want to delete this post-assessment report?</p>
                <div class="bg-light p-3 rounded">
                    <div class="row">
                        <div class="col-sm-4"><strong>Date:</strong></div>
                        <div class="col-sm-8" id="delete_confirm_date"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4"><strong>Meal Type:</strong></div>
                        <div class="col-sm-8" id="delete_confirm_meal_type"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4"><strong>Submitted By:</strong></div>
                        <div class="col-sm-8" id="delete_confirm_submitted_by"></div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete Report
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .assessment-item.hidden {
        display: none;
    }

    .assessment-item {
        transition: all 0.3s ease;
    }

    .assessment-item:hover {
        background-color: #f8f9fa;
    }

    .waste-tips {
        background-color: #e8f5e9;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #43a047;
    }

    .chart-container {
        height: 250px;
        position: relative;
    }

    .waste-metrics {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .metric {
        text-align: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        flex: 1;
        margin: 0 5px;
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 5px;
    }

    .metric-label {
        font-size: 0.85rem;
        color: #555;
    }

    /* Image display improvements */
    #view_report_image {
        border: 2px solid #dee2e6;
        transition: transform 0.2s ease;
    }

    #view_report_image:hover {
        transform: scale(1.02);
        border-color: var(--primary-color, #ff9933);
    }

    .new-badge {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

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
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .modal-header {
        background-color: var(--primary-color, #ff9933);
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: invert(1);
    }

    /* Ensure all modals are clickable */
    #viewReportModal, #imageModal, #deleteConfirmModal, #bulkDeleteConfirmModal {
        z-index: 999999 !important;
        pointer-events: auto !important;
    }

    #viewReportModal .modal-dialog, #imageModal .modal-dialog,
    #deleteConfirmModal .modal-dialog, #bulkDeleteConfirmModal .modal-dialog {
        pointer-events: auto !important;
    }

    #viewReportModal .modal-content, #imageModal .modal-content,
    #deleteConfirmModal .modal-content, #bulkDeleteConfirmModal .modal-content {
        pointer-events: auto !important;
    }

    /* Loading state for images */
    .image-loading {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Delete functionality styles */
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-right: 2px;
    }

    .btn-group .btn:not(:last-child) {
        margin-right: 2px;
    }

    .delete-assessment-btn:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    /* Toast container styles */
    .toast-container {
        z-index: 9999 !important;
    }

    .toast {
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Row deletion animation */
    .assessment-item {
        transition: all 0.3s ease;
    }

    .assessment-item.deleting {
        opacity: 0;
        transform: translateX(-100%);
    }

    /* Modal improvements for delete confirmation */
    .modal-header.bg-danger {
        background-color: #dc3545 !important;
    }

    .modal-header.bg-danger .btn-close-white {
        filter: brightness(0) invert(1);
    }

    /* Button loading state */
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Bulk selection styles */
    .form-check-input:checked {
        background-color: var(--primary-color, #ff9933);
        border-color: var(--primary-color, #ff9933);
    }

    .form-check-input:focus {
        border-color: var(--primary-color, #ff9933);
        box-shadow: 0 0 0 0.25rem rgba(255, 153, 51, 0.25);
    }

    #bulk-actions {
        animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Selected row highlighting */
    .assessment-item:has(.assessment-checkbox:checked) {
        background-color: rgba(255, 153, 51, 0.1);
        border-left: 3px solid var(--primary-color, #ff9933);
    }

    /* Bulk delete button styling */
    #bulk-delete-btn {
        transition: all 0.3s ease;
    }

    #bulk-delete-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    /* Add margin to the modal so it doesn't overlap the header */
    .image-modal-xl {
        margin-top: 48px !important;
        margin-bottom: 48px !important;
    }
    @media (max-width: 1200px) {
        .image-modal-xl {
            margin-top: 24px !important;
            margin-bottom: 24px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // View report modal (for cook)
    document.querySelectorAll('.view-report-btn').forEach(button => {
        button.addEventListener('click', function() {
            const date = this.dataset.date;
            const mealType = this.dataset.mealType;
            const notes = this.dataset.notes || 'No notes provided';
            const imagePath = this.dataset.imagePath;
            const items = this.dataset.items ? JSON.parse(this.dataset.items) : [];
            const submittedBy = this.dataset.submittedBy;
            const submittedAt = this.dataset.submittedAt;

            // Populate modal fields
            document.getElementById('view_report_date').textContent = new Date(date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('view_report_meal_type').textContent = mealType.charAt(0).toUpperCase() + mealType.slice(1);
            document.getElementById('view_report_submitted_by').textContent = submittedBy;
            document.getElementById('view_report_submitted_at').textContent = submittedAt;
            document.getElementById('view_report_notes').textContent = notes;

            // Populate food items
            const itemsContainer = document.getElementById('view_report_items');
            if (items && items.length > 0) {
                let itemsHtml = '<div class="row">';
                items.forEach((item, index) => {
                    itemsHtml += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center p-2 bg-white rounded border">
                                <i class="bi bi-circle-fill text-primary me-2" style="font-size: 8px;"></i>
                                <span class="fw-medium">${item.name || 'Unnamed Item'}</span>
                            </div>
                        </div>
                    `;
                });
                itemsHtml += '</div>';
                itemsContainer.innerHTML = itemsHtml;
            } else {
                itemsContainer.innerHTML = '<p class="text-muted mb-0"><i class="bi bi-info-circle me-2"></i>No food items specified in this report.</p>';
            }

            // Handle image display
            const imageSection = document.getElementById('report_image_section');
            const reportImage = document.getElementById('view_report_image');
            
            if (imagePath && imagePath !== 'null' && imagePath !== '' && imagePath !== 'undefined') {
                const imageSrc = imagePath.startsWith('http') ? imagePath :
                               imagePath.startsWith('/') ? imagePath : '/' + imagePath;
                reportImage.src = imageSrc;
                imageSection.style.display = 'block';
            } else {
                imageSection.style.display = 'none';
            }

            showModalSimple('viewReportModal');
        });
    });

    // Function to open full-size image modal
    function openImageModal(imageSrc) {
        const fullSizeImage = document.getElementById('fullSizeImage');
        const downloadBtn = document.getElementById('downloadImageBtn');

        if (fullSizeImage && downloadBtn) {
            fullSizeImage.src = imageSrc;
            downloadBtn.href = imageSrc;

            // Handle image load errors in full size modal
            fullSizeImage.onerror = function() {
                console.log('Full size image failed to load:', imageSrc);
                fullSizeImage.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4=';
            };

            // Show modal
            showModalSimple('imageModal');
        }
    }

    // UNIVERSAL MODAL FUNCTION - SIMPLE AND WORKING
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
        modalElement.querySelectorAll('input, textarea, button, select, img, a').forEach(el => {
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

    // Add notification highlighting for new assessments
    document.addEventListener('DOMContentLoaded', function() {
        const assessmentItems = document.querySelectorAll('.assessment-item');
        const now = new Date();
        const oneHourAgo = new Date(now.getTime() - (60 * 60 * 1000)); // 1 hour ago

        // Use localStorage to track seen assessments
        let seenAssessments = JSON.parse(localStorage.getItem('seenAssessments') || '{}');
        let updated = false;

        assessmentItems.forEach(item => {
            const id = item.dataset.assessmentId;
            const createdAt = new Date(item.dataset.assessmentCreated);
            const isNew = createdAt > oneHourAgo && !seenAssessments[id];

            if (isNew) {
                // Highlight new assessments
                item.style.backgroundColor = '#fff3cd';
                item.style.borderLeft = '4px solid #ff9933';

                // Add a "NEW" badge
                const firstCell = item.querySelector('td:first-child');
                if (firstCell && !firstCell.querySelector('.new-badge')) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'badge bg-warning text-dark new-badge ms-2';
                    newBadge.textContent = 'NEW';
                    newBadge.style.fontSize = '0.7rem';
                    firstCell.appendChild(newBadge);
                }

                // Mark as seen for next time
                seenAssessments[id] = true;
                updated = true;
            } else {
                // Remove highlight and badge if already seen
                item.style.backgroundColor = '';
                item.style.borderLeft = '';
                const badge = item.querySelector('.new-badge');
                if (badge) badge.remove();
            }
        });

        if (updated) {
            localStorage.setItem('seenAssessments', JSON.stringify(seenAssessments));
        }
    });

    // Delete assessment functionality
    let assessmentToDelete = null;

    // Handle delete button clicks
    document.querySelectorAll('.delete-assessment-btn').forEach(button => {
        button.addEventListener('click', function() {
            assessmentToDelete = {
                id: this.dataset.id,
                date: this.dataset.date,
                mealType: this.dataset.mealType,
                submittedBy: this.dataset.submittedBy
            };

            // Populate confirmation modal
            document.getElementById('delete_confirm_date').textContent = assessmentToDelete.date;
            document.getElementById('delete_confirm_meal_type').textContent = assessmentToDelete.mealType;
            document.getElementById('delete_confirm_submitted_by').textContent = assessmentToDelete.submittedBy;

            // Show confirmation modal
            showModalSimple('deleteConfirmModal');
        });
    });

    // Handle confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!assessmentToDelete) return;

        const deleteBtn = this;
        const originalText = deleteBtn.innerHTML;

        // Show loading state
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';

        // Send delete request
        fetch(`/cook/post-assessment/${assessmentToDelete.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('Assessment deleted successfully!', 'success');

                // Remove the row from table
                const assessmentRow = document.querySelector(`tr[data-assessment-id="${assessmentToDelete.id}"]`);
                if (assessmentRow) {
                    assessmentRow.style.transition = 'all 0.3s ease';
                    assessmentRow.style.opacity = '0';
                    assessmentRow.style.transform = 'translateX(-100%)';

                    setTimeout(() => {
                        assessmentRow.remove();

                        // Check if table is empty and show empty message
                        const tableBody = document.querySelector('tbody');
                        if (tableBody && tableBody.children.length === 0) {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            <p class="mb-0">No leftover reports found</p>
                                            <small>Kitchen team hasn't submitted any reports yet</small>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }
                    }, 300);
                }

                // Close modal
                hideModalSimple('deleteConfirmModal');

            } else {
                showToast(data.message || 'Failed to delete assessment', 'error');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showToast('An error occurred while deleting the assessment', 'error');
        })
        .finally(() => {
            // Reset button state
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalText;
            assessmentToDelete = null;
        });
    });

    // Delete All button functionality
    const deleteAllBtn = document.getElementById('delete-all-btn');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete ALL post-assessment reports? This action cannot be undone.')) return;
            deleteAllBtn.disabled = true;
            deleteAllBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
            // Gather all assessment IDs
            const ids = Array.from(document.querySelectorAll('.assessment-item')).map(row => row.dataset.assessmentId);
            fetch('/cook/post-assessment/bulk-delete', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove all rows
                    document.querySelectorAll('.assessment-item').forEach(row => row.remove());
                    showToast(data.message || 'All assessments deleted successfully!', 'success');
                    // Remove the button
                    deleteAllBtn.remove();
                    // Show empty message
                    const tableBody = document.querySelector('tbody');
                    if (tableBody) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        <p class="mb-0">No leftover reports found</p>
                                        <small>Kitchen team hasn't submitted any reports yet</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                } else {
                    showToast(data.message || 'Failed to delete all assessments', 'error');
                }
            })
            .catch(error => {
                console.error('Delete all error:', error);
                showToast('An error occurred while deleting all assessments', 'error');
            })
            .finally(() => {
                deleteAllBtn.disabled = false;
                deleteAllBtn.innerHTML = '<i class="bi bi-trash"></i> Delete All';
            });
        });
    }

    // Toast notification function
    function showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        // Initialize and show toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: type === 'error' ? 5000 : 3000
        });

        toast.show();

        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
</script>
@endpush
