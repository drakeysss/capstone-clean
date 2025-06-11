@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Post Assessment</h2>
                    <p class="text-muted" style="color: white;">Track and analyze leftover food to improve meal planning efficiency</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
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
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                        <div id="bulk-actions" style="display: none;">
                            <button type="button" class="btn btn-danger btn-sm" id="bulk-delete-btn">
                                <i class="bi bi-trash me-1"></i>
                                Delete Selected (<span id="selected-count">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all-assessments">
                                            <label class="form-check-label" for="select-all-assessments"></label>
                                        </div>
                                    </th>
                                    <th>Date</th>
                                    <th>Meal Type</th>
                                    <th>Prepared</th>
                                    <th>Leftover</th>
                                    <th>Waste %</th>
                                    <th>Submitted By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessments as $assessment)
                                <tr class="assessment-item"
                                    data-assessment-created="{{ $assessment->completed_at ? $assessment->completed_at->toISOString() : $assessment->created_at->toISOString() }}"
                                    data-assessment-id="{{ $assessment->id }}"
                                    data-meal-type="{{ $assessment->meal_type }}"
                                    data-waste-percentage="{{ $assessment->planned_portions > 0 ? round(($assessment->leftover_portions / $assessment->planned_portions) * 100, 1) : 0 }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input assessment-checkbox" type="checkbox"
                                                   value="{{ $assessment->id }}"
                                                   id="assessment-{{ $assessment->id }}">
                                            <label class="form-check-label" for="assessment-{{ $assessment->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $assessment->date->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assessment->date->format('l') }}</small>
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
                                        <strong>{{ number_format($assessment->planned_portions, 1) }}</strong>
                                        <small class="text-muted">servings</small>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($assessment->leftover_portions, 1) }}</strong>
                                        <small class="text-muted">servings</small>
                                    </td>
                                    <td>
                                        @php
                                            $wastePercentage = $assessment->planned_portions > 0
                                                ? round(($assessment->leftover_portions / $assessment->planned_portions) * 100, 1)
                                                : 0;
                                        @endphp
                                        <span class="badge
                                            @if($wastePercentage < 10) bg-success
                                            @elseif($wastePercentage < 20) bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $wastePercentage }}%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2 text-muted"></i>
                                            <div>
                                                <strong>{{ $assessment->assessedBy->name ?? 'Kitchen Team' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $assessment->completed_at->format('g:i A') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary view-report-btn"
                                                data-id="{{ $assessment->id }}"
                                                data-date="{{ $assessment->date->format('Y-m-d') }}"
                                                data-meal-type="{{ $assessment->meal_type }}"
                                                data-prepared="{{ $assessment->planned_portions }}"
                                                data-leftover="{{ $assessment->leftover_portions }}"
                                                data-waste-percentage="{{ $wastePercentage }}"
                                                data-notes="{{ $assessment->notes }}"
                                                data-image-path="{{ $assessment->image_path }}"
                                                data-submitted-by="{{ $assessment->assessedBy->name ?? 'Kitchen Team' }}"
                                                data-submitted-at="{{ $assessment->completed_at->format('M d, Y g:i A') }}">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning edit-assessment-btn"
                                                data-id="{{ $assessment->id }}"
                                                data-date="{{ $assessment->date->format('Y-m-d') }}"
                                                data-meal-type="{{ $assessment->meal_type }}"
                                                data-total-prepared="{{ $assessment->planned_portions }}"
                                                data-total-leftover="{{ $assessment->leftover_portions }}"
                                                data-total-consumed="{{ $assessment->actual_portions_served }}"
                                                data-notes="{{ $assessment->notes }}"
                                                data-image-path="{{ $assessment->image_path }}"
                                                title="Edit this assessment report">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-assessment-btn"
                                                data-id="{{ $assessment->id }}"
                                                data-date="{{ $assessment->date->format('M d, Y') }}"
                                                data-meal-type="{{ ucfirst($assessment->meal_type) }}"
                                                data-submitted-by="{{ $assessment->assessedBy->name ?? 'Kitchen Team' }}"
                                                title="Delete this assessment report">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            <p class="mb-0">No leftover reports found</p>
                                            <small>Kitchen team hasn't submitted any reports yet</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

<!-- Edit Assessment Modal (For Kitchen Staff) -->
<div class="modal fade" id="editAssessmentModal" tabindex="-1" aria-labelledby="editAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssessmentModalLabel">Edit Post Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAssessmentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_assessment_id" name="id">
                    <div class="mb-3">
                        <label for="edit_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="edit_date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_meal_type" class="form-label">Meal Type</label>
                        <select class="form-select" id="edit_meal_type" name="meal_type" required>
                            <option value="">Select meal type</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_total_prepared" class="form-label">Total Food Prepared (kg)</label>
                        <input type="number" class="form-control" id="edit_total_prepared" name="total_prepared" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_total_leftover" class="form-label">Total Food Leftover (kg)</label>
                        <input type="number" class="form-control" id="edit_total_leftover" name="total_leftover" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_total_consumed" class="form-label">Total Food Consumed (kg)</label>
                        <input type="number" class="form-control" id="edit_total_consumed" name="total_consumed" step="0.01" min="0" readonly>
                        <small class="text-muted">Automatically calculated (Prepared - Leftover)</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_report_image" class="form-label">
                            <i class="bi bi-camera me-2"></i>Update Photo (Optional)
                        </label>
                        <input type="file" class="form-control" id="edit_report_image" name="report_image" accept="image/*">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload a new photo to replace the existing one. Supported formats: JPEG, PNG, GIF (Max: 5MB)
                        </div>
                        <div id="edit_image_preview" class="mt-3" style="display: none;">
                            <img id="edit_preview_img" src="" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="edit_remove_image">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </div>
                        <div id="current_image_display" class="mt-3" style="display: none;">
                            <label class="form-label fw-bold">Current Photo:</label>
                            <div class="border rounded p-2 bg-light">
                                <img id="current_image_preview" src="" alt="Current Photo" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateAssessmentBtn">Update Assessment</button>
            </div>
        </div>
    </div>
</div>

<!-- View Report Modal (For Cook) -->
<div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReportModalLabel">
                    <i class="bi bi-clipboard-data me-2"></i>
                    Kitchen Leftover Report Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                            <label class="form-label fw-bold">Total Prepared</label>
                            <p id="view_report_prepared" class="form-control-static"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Total Leftover</label>
                            <p id="view_report_leftover" class="form-control-static"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Total Consumed</label>
                            <p id="view_report_consumed" class="form-control-static"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Waste Percentage</label>
                            <p id="view_report_waste_percentage" class="form-control-static"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kitchen Notes</label>
                            <div id="view_report_notes" class="border rounded p-3 bg-light"></div>
                        </div>
                    </div>
                </div>

                <!-- Image Section -->
                <div class="row mt-4" id="report_image_section" style="display: none;">
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">
                    <i class="bi bi-camera me-2"></i>Leftover Report Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fullSizeImage" src="" alt="Full Size Report Photo" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadImageBtn" href="" download class="btn btn-primary">
                    <i class="bi bi-download me-1"></i>Download Image
                </a>
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
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirm Delete Assessment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-trash display-4 text-danger"></i>
                </div>
                <h6 class="text-center mb-3">Are you sure you want to delete this assessment report?</h6>
                <div class="alert alert-warning">
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
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The assessment data and any attached images will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i> Delete Assessment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteConfirmModal" tabindex="-1" aria-labelledby="bulkDeleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="bulkDeleteConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirm Bulk Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-trash display-4 text-danger"></i>
                </div>
                <h6 class="text-center mb-3">Are you sure you want to delete <span id="bulk_delete_count" class="fw-bold text-danger"></span> assessment reports?</h6>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All selected assessment data and any attached images will be permanently deleted.
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Selected Reports:</strong>
                    <ul id="bulk_delete_list" class="mb-0 mt-2"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmBulkDeleteBtn">
                    <i class="bi bi-trash me-1"></i> Delete All Selected
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
    #editAssessmentModal, #viewReportModal, #imageModal, #deleteConfirmModal, #bulkDeleteConfirmModal {
        z-index: 999999 !important;
        pointer-events: auto !important;
    }

    #editAssessmentModal .modal-dialog, #viewReportModal .modal-dialog, #imageModal .modal-dialog,
    #deleteConfirmModal .modal-dialog, #bulkDeleteConfirmModal .modal-dialog {
        pointer-events: auto !important;
    }

    #editAssessmentModal .modal-content, #viewReportModal .modal-content, #imageModal .modal-content,
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };

        const dateString = now.toLocaleDateString('en-US', dateOptions);
        const timeString = now.toLocaleTimeString('en-US', timeOptions);

        document.getElementById('currentDateTime').innerHTML = `${dateString}<br><small>${timeString}</small>`;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000); // Update every second for real-time display
    
    // Add event listeners only if elements exist (for forms that have these fields)
    const totalPreparedEl = document.getElementById('total_prepared');
    const totalLeftoverEl = document.getElementById('total_leftover');

    if (totalPreparedEl && totalLeftoverEl) {
        totalPreparedEl.addEventListener('input', calculateConsumed);
        totalLeftoverEl.addEventListener('input', calculateConsumed);
    }

    // Calculate consumed food in the main form
    function calculateConsumed() {
        const totalPrepared = parseFloat(document.getElementById('total_prepared').value) || 0;
        const totalLeftover = parseFloat(document.getElementById('total_leftover').value) || 0;

        if (totalPrepared >= totalLeftover) {
            const totalConsumed = totalPrepared - totalLeftover;
            document.getElementById('total_consumed').value = totalConsumed.toFixed(2);
        } else {
            // If leftover is greater than prepared (invalid input), clear the consumed field
            document.getElementById('total_consumed').value = '';
        }
    }

    // Period and meal filtering (only if filter elements exist)
    function filterAssessments() {
        const periodFilter = document.getElementById('periodFilter');
        const mealFilter = document.getElementById('mealFilter');

        if (!periodFilter || !mealFilter) return;

        const assessmentItems = document.querySelectorAll('.assessment-item');

        const today = new Date();
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay());
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

        assessmentItems.forEach(item => {
            const itemDate = new Date(item.dataset.assessmentCreated);
            const itemMeal = item.dataset.mealType;

            let periodMatch = true;
            if (periodFilter.value === 'week') {
                periodMatch = itemDate >= weekStart;
            } else if (periodFilter.value === 'month') {
                periodMatch = itemDate >= monthStart;
            }
            // For 'all', show all dates

            const mealMatch = mealFilter.value === 'all' || itemMeal === mealFilter.value;

            if (periodMatch && mealMatch) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    // Add event listeners only if filter elements exist
    const periodFilterEl = document.getElementById('periodFilter');
    const mealFilterEl = document.getElementById('mealFilter');

    if (periodFilterEl) periodFilterEl.addEventListener('change', filterAssessments);
    if (mealFilterEl) mealFilterEl.addEventListener('change', filterAssessments);
    
    // Edit assessment modal (for kitchen staff)
    document.querySelectorAll('.edit-assessment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const date = this.dataset.date;
            const mealType = this.dataset.mealType;
            const totalPrepared = this.dataset.totalPrepared;
            // Handle both old and new data structure
            const totalLeftover = this.dataset.totalLeftover || this.dataset.totalWasted;
            const totalConsumed = this.dataset.totalConsumed || (totalPrepared - totalLeftover);
            const notes = this.dataset.notes;
            
            document.getElementById('edit_assessment_id').value = id;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_meal_type').value = mealType;
            document.getElementById('edit_total_prepared').value = totalPrepared;
            document.getElementById('edit_total_leftover').value = totalLeftover;
            document.getElementById('edit_total_consumed').value = totalConsumed;
            document.getElementById('edit_notes').value = notes;
            
            // Add event listeners to calculate consumed automatically
            document.getElementById('edit_total_prepared').addEventListener('input', calculateEditConsumed);
            document.getElementById('edit_total_leftover').addEventListener('input', calculateEditConsumed);

            // Handle current image display
            const imagePath = this.dataset.imagePath;
            const currentImageDisplay = document.getElementById('current_image_display');
            const currentImagePreview = document.getElementById('current_image_preview');

            if (imagePath && imagePath !== 'null' && imagePath !== '' && imagePath !== 'undefined') {
                const imageSrc = imagePath.startsWith('http') ? imagePath :
                               imagePath.startsWith('/') ? imagePath : '/' + imagePath;
                currentImagePreview.src = imageSrc;
                currentImageDisplay.style.display = 'block';
            } else {
                currentImageDisplay.style.display = 'none';
            }

            // Reset image upload fields
            document.getElementById('edit_report_image').value = '';
            document.getElementById('edit_image_preview').style.display = 'none';

            showModalSimple('editAssessmentModal');
        });
    });

    // Image upload functionality for edit modal
    document.addEventListener('DOMContentLoaded', function() {
        const editImageInput = document.getElementById('edit_report_image');
        const editImagePreview = document.getElementById('edit_image_preview');
        const editPreviewImg = document.getElementById('edit_preview_img');
        const editRemoveImageBtn = document.getElementById('edit_remove_image');

        if (editImageInput) {
            editImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];

                if (file) {
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size must be less than 5MB');
                        editImageInput.value = '';
                        return;
                    }

                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select a valid image file');
                        editImageInput.value = '';
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editPreviewImg.src = e.target.result;
                        editImagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    editImagePreview.style.display = 'none';
                }
            });

            // Remove image functionality
            if (editRemoveImageBtn) {
                editRemoveImageBtn.addEventListener('click', function() {
                    editImageInput.value = '';
                    editImagePreview.style.display = 'none';
                    editPreviewImg.src = '';
                });
            }
        }
    });
    
    // Calculate consumed food in edit modal
    function calculateEditConsumed() {
        const totalPrepared = parseFloat(document.getElementById('edit_total_prepared').value) || 0;
        const totalLeftover = parseFloat(document.getElementById('edit_total_leftover').value) || 0;
        
        if (totalPrepared >= totalLeftover) {
            const totalConsumed = totalPrepared - totalLeftover;
            document.getElementById('edit_total_consumed').value = totalConsumed.toFixed(2);
        } else {
            document.getElementById('edit_total_consumed').value = '';
        }
    }
    
    // Update assessment button handler
    document.getElementById('updateAssessmentBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.getElementById('editAssessmentForm');
        const id = document.getElementById('edit_assessment_id').value;
        const formData = new FormData(form);
        const updateBtn = this;
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
        // Add method override for PUT request
        formData.append('_method', 'PUT');

        // Debug: Log form data being sent
        console.log('📝 Sending assessment update data:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        fetch(`/cook/post-assessment/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => {
            console.log('📊 Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📋 Response data:', data);
            if (data.success) {
                alert('Assessment updated successfully!');
                // Close modal and refresh data
                hideModalSimple('editAssessmentModal');
                location.reload();
            } else {
                console.error('❌ Update failed:', data);
                let errorMessage = data.message || 'Failed to update assessment';

                // Show validation errors if available
                if (data.errors) {
                    console.error('🔍 Validation errors:', data.errors);
                    errorMessage += '\n\nValidation errors:';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += `\n• ${field}: ${data.errors[field].join(', ')}`;
                    });
                }

                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('💥 Fetch error:', error);
            alert('An error occurred while updating assessment: ' + error.message);
        })
        .finally(() => {
            updateBtn.disabled = false;
            updateBtn.innerHTML = 'Update Assessment';
        });
    });
    
    // View report modal (for cook)
    document.querySelectorAll('.view-report-btn').forEach(button => {
        button.addEventListener('click', function() {
            const date = this.dataset.date;
            const mealType = this.dataset.mealType;
            const prepared = parseFloat(this.dataset.prepared);
            const leftover = parseFloat(this.dataset.leftover);
            const consumed = prepared - leftover;
            const wastePercentage = this.dataset.wastePercentage;
            const notes = this.dataset.notes || 'No notes provided';
            const imagePath = this.dataset.imagePath;
            const submittedBy = this.dataset.submittedBy;
            const submittedAt = this.dataset.submittedAt;

            // Format date
            const formattedDate = new Date(date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Populate modal
            document.getElementById('view_report_date').textContent = formattedDate;
            document.getElementById('view_report_meal_type').innerHTML = `<span class="badge ${
                mealType === 'breakfast' ? 'bg-warning' :
                mealType === 'lunch' ? 'bg-primary' : 'bg-info'
            }">${mealType.charAt(0).toUpperCase() + mealType.slice(1)}</span>`;
            document.getElementById('view_report_submitted_by').textContent = submittedBy;
            document.getElementById('view_report_submitted_at').textContent = submittedAt;
            document.getElementById('view_report_prepared').innerHTML = `<strong>${prepared.toFixed(1)}</strong> <small class="text-muted">servings</small>`;
            document.getElementById('view_report_leftover').innerHTML = `<strong>${leftover.toFixed(1)}</strong> <small class="text-muted">servings</small>`;
            document.getElementById('view_report_consumed').innerHTML = `<strong>${consumed.toFixed(1)}</strong> <small class="text-muted">servings</small>`;
            document.getElementById('view_report_waste_percentage').innerHTML = `<span class="badge ${
                wastePercentage < 10 ? 'bg-success' :
                wastePercentage < 20 ? 'bg-warning' : 'bg-danger'
            }">${wastePercentage}%</span>`;
            document.getElementById('view_report_notes').textContent = notes;

            // Handle image display
            const imageSection = document.getElementById('report_image_section');
            const reportImage = document.getElementById('view_report_image');

            if (imagePath && imagePath !== 'null' && imagePath !== '' && imagePath !== 'undefined') {
                // Add loading state
                reportImage.classList.add('image-loading');

                // Handle both absolute and relative paths
                const imageSrc = imagePath.startsWith('http') ? imagePath :
                                imagePath.startsWith('/') ? imagePath : '/' + imagePath;

                // Create a new image to test if it loads
                const testImage = new Image();
                testImage.onload = function() {
                    reportImage.src = imageSrc;
                    reportImage.classList.remove('image-loading');
                    imageSection.style.display = 'block';
                };

                testImage.onerror = function() {
                    console.log('Image failed to load:', imageSrc);
                    reportImage.classList.remove('image-loading');
                    imageSection.style.display = 'none';

                    // Show a message that image is not available
                    const noImageDiv = document.createElement('div');
                    noImageDiv.className = 'alert alert-info';
                    noImageDiv.innerHTML = '<i class="bi bi-image me-2"></i>Image attachment is not available or could not be loaded.';
                    imageSection.appendChild(noImageDiv);
                    imageSection.style.display = 'block';
                };

                testImage.src = imageSrc;
            } else {
                imageSection.style.display = 'none';
            }

            // Show modal
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

        assessmentItems.forEach(item => {
            const createdAt = new Date(item.dataset.assessmentCreated);
            if (createdAt > oneHourAgo) {
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
            }
        });

        // Debug: Log current page info
        console.log('Cook Post-Assessment page loaded');
        console.log('Total assessments found:', assessmentItems.length);

        // Check if uploads directory is accessible
        fetch('/uploads/post-assessments/', {method: 'HEAD'})
            .then(response => {
                if (response.ok) {
                    console.log('✅ Uploads directory is accessible');
                } else {
                    console.warn('⚠️ Uploads directory may not be accessible');
                }
            })
            .catch(error => {
                console.warn('⚠️ Could not check uploads directory accessibility');
            });
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
        deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Deleting...';

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
                                    <td colspan="8" class="text-center py-4">
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

    // Bulk delete functionality
    const selectAllCheckbox = document.getElementById('select-all-assessments');
    const assessmentCheckboxes = document.querySelectorAll('.assessment-checkbox');
    const bulkActionsDiv = document.getElementById('bulk-actions');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const selectedCountSpan = document.getElementById('selected-count');

    // Handle select all checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            assessmentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Handle individual checkboxes
    assessmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();

            // Update select all checkbox state
            if (selectAllCheckbox) {
                const checkedCount = document.querySelectorAll('.assessment-checkbox:checked').length;
                const totalCount = assessmentCheckboxes.length;

                selectAllCheckbox.checked = checkedCount === totalCount;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            }
        });
    });

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.assessment-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkActionsDiv.style.display = 'block';
            selectedCountSpan.textContent = count;
        } else {
            bulkActionsDiv.style.display = 'none';
        }
    }

    // Handle bulk delete button
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.assessment-checkbox:checked');
            const selectedAssessments = Array.from(checkedBoxes).map(checkbox => {
                const row = checkbox.closest('tr');
                return {
                    id: checkbox.value,
                    date: row.querySelector('td:nth-child(2) strong').textContent,
                    mealType: row.querySelector('td:nth-child(3) .badge').textContent,
                    submittedBy: row.querySelector('td:nth-child(7) strong').textContent
                };
            });

            if (selectedAssessments.length === 0) return;

            // Populate bulk delete modal
            document.getElementById('bulk_delete_count').textContent = selectedAssessments.length;
            const listElement = document.getElementById('bulk_delete_list');
            listElement.innerHTML = selectedAssessments.map(assessment =>
                `<li>${assessment.date} - ${assessment.mealType} (by ${assessment.submittedBy})</li>`
            ).join('');

            // Show bulk delete modal
            showModalSimple('bulkDeleteConfirmModal');
        });
    }

    // Handle confirm bulk delete
    document.getElementById('confirmBulkDeleteBtn').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.assessment-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);

        if (selectedIds.length === 0) return;

        const confirmBtn = this;
        const originalText = confirmBtn.innerHTML;

        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Deleting...';

        // Delete assessments one by one
        let deletedCount = 0;
        let failedCount = 0;

        const deletePromises = selectedIds.map(id =>
            fetch(`/cook/post-assessment/${id}`, {
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
                    deletedCount++;
                    // Remove the row
                    const row = document.querySelector(`tr[data-assessment-id="${id}"]`);
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-100%)';
                        setTimeout(() => row.remove(), 300);
                    }
                } else {
                    failedCount++;
                }
            })
            .catch(error => {
                console.error('Delete error for ID', id, ':', error);
                failedCount++;
            })
        );

        Promise.all(deletePromises).then(() => {
            // Show results
            if (deletedCount > 0) {
                showToast(`Successfully deleted ${deletedCount} assessment(s)`, 'success');
            }
            if (failedCount > 0) {
                showToast(`Failed to delete ${failedCount} assessment(s)`, 'error');
            }

            // Reset UI
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateBulkActions();

            // Check if table is empty
            setTimeout(() => {
                const remainingRows = document.querySelectorAll('.assessment-item');
                if (remainingRows.length === 0) {
                    const tableBody = document.querySelector('tbody');
                    if (tableBody) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        <p class="mb-0">No leftover reports found</p>
                                        <small>Kitchen team hasn't submitted any reports yet</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                }
            }, 500);

            // Close modal
            hideModalSimple('bulkDeleteConfirmModal');
        }).finally(() => {
            // Reset button state
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        });
    });

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
