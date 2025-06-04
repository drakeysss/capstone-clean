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

    <!-- Post Assessment Section -->
    <div class="row">
        @if(Auth::user()->role === 'kitchen')
        <div class="col-12 mb-4">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title">Record Post Assessment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cook.post-assessment.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="meal_type" class="form-label">Meal Type</label>
                            <select class="form-select" id="meal_type" name="meal_type" required>
                                <option value="">Select meal type</option>
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="total_prepared" class="form-label">Total Food Prepared (kg)</label>
                            <input type="number" class="form-control" id="total_prepared" name="total_prepared" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="total_leftover" class="form-label">Total Food Leftover (kg)</label>
                            <input type="number" class="form-control" id="total_leftover" name="total_leftover" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="total_consumed" class="form-label">Total Food Consumed (kg)</label>
                            <input type="number" class="form-control" id="total_consumed" name="total_consumed" step="0.01" min="0" readonly>
                            <small class="text-muted">Automatically calculated (Prepared - Leftover)</small>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Assessment</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="col-12 mb-4">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Post Assessment Records</h5>
                    <div>
                        <select id="periodFilter" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="all">All Time</option>
                        </select>
                        <select id="mealFilter" class="form-select form-select-sm d-inline-block w-auto ms-2">
                            <option value="all">All Meals</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Meal</th>
                                    <th>Leftover (kg)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessments ?? [] as $assessment)
                                <tr class="assessment-item" 
                                    data-date="{{ $assessment->date }}" 
                                    data-meal="{{ $assessment->meal_type }}">
                                    <td>{{ date('M d, Y', strtotime($assessment->date)) }}</td>
                                    <td>{{ ucfirst($assessment->meal_type) }}</td>
                                    <td>{{ number_format($assessment->total_leftover ?? $assessment->total_wasted, 2) }}</td>
                                    <td>
                                        @php
                                            $leftoverPercentage = $assessment->total_prepared > 0 
                                                ? round((($assessment->total_leftover ?? $assessment->total_wasted) / $assessment->total_prepared) * 100, 1) 
                                                : 0;
                                        @endphp
                                        <span class="badge 
                                            @if($leftoverPercentage < 10) bg-success
                                            @elseif($leftoverPercentage < 20) bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $leftoverPercentage }}%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if(Auth::user()->role === 'kitchen')
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-assessment-btn" 
                                                data-id="{{ $assessment->id }}"
                                                data-date="{{ $assessment->date }}"
                                                data-meal-type="{{ $assessment->meal_type }}"
                                                data-total-prepared="{{ $assessment->total_prepared }}"
                                                data-total-leftover="{{ $assessment->total_leftover ?? $assessment->total_wasted }}"
                                                data-notes="{{ $assessment->notes }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary view-assessment-btn" 
                                                data-id="{{ $assessment->id }}"
                                                data-date="{{ $assessment->date }}"
                                                data-meal-type="{{ $assessment->meal_type }}"
                                                data-total-prepared="{{ $assessment->total_prepared }}"
                                                data-total-leftover="{{ $assessment->total_leftover ?? $assessment->total_wasted }}"
                                                data-notes="{{ $assessment->notes }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No post assessment records found</td>
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
                <form id="editAssessmentForm" method="POST">
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateAssessmentBtn">Update Assessment</button>
            </div>
        </div>
    </div>
</div>

<!-- View Assessment Modal (For Admin/Cook) -->
<div class="modal fade" id="viewAssessmentModal" tabindex="-1" aria-labelledby="viewAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAssessmentModalLabel">View Post Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <p id="view_date" class="form-control-static"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Meal Type</label>
                    <p id="view_meal_type" class="form-control-static"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Food Prepared (kg)</label>
                    <p id="view_total_prepared" class="form-control-static"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Food Leftover (kg)</label>
                    <p id="view_total_leftover" class="form-control-static"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Food Consumed (kg)</label>
                    <p id="view_total_consumed" class="form-control-static"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Leftover Percentage</label>
                    <p id="view_leftover_percentage" class="form-control-static"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <p id="view_notes" class="form-control-static"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            minute: '2-digit'
        };
        
        document.getElementById('currentDateTime').innerHTML = `${now.toLocaleDateString('en-US', dateOptions)} ${now.toLocaleTimeString('en-US', timeOptions)}`;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 60000);
    
    // Add event listeners to calculate consumed automatically in the main form
    document.getElementById('total_prepared').addEventListener('input', calculateConsumed);
    document.getElementById('total_leftover').addEventListener('input', calculateConsumed);
    
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
    
    // Period and meal filtering
    function filterAssessments() {
        const periodFilter = document.getElementById('periodFilter').value;
        const mealFilter = document.getElementById('mealFilter').value;
        const assessmentItems = document.querySelectorAll('.assessment-item');
        
        const today = new Date();
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay());
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        
        assessmentItems.forEach(item => {
            const itemDate = new Date(item.dataset.date);
            const itemMeal = item.dataset.meal;
            
            let periodMatch = true;
            if (periodFilter === 'week') {
                periodMatch = itemDate >= weekStart;
            } else if (periodFilter === 'month') {
                periodMatch = itemDate >= monthStart;
            }
            // For 'all', show all dates
            
            const mealMatch = mealFilter === 'all' || itemMeal === mealFilter;
            
            if (periodMatch && mealMatch) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }
    
    document.getElementById('periodFilter').addEventListener('change', filterAssessments);
    document.getElementById('mealFilter').addEventListener('change', filterAssessments);
    
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
            
            const editAssessmentModal = new bootstrap.Modal(document.getElementById('editAssessmentModal'));
            editAssessmentModal.show();
        });
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
    document.getElementById('updateAssessmentBtn').addEventListener('click', function() {
        const form = document.getElementById('editAssessmentForm');
        const id = document.getElementById('edit_assessment_id').value;
        form.action = `/cook/post-assessment/${id}`;
        form.submit();
    });
    
    // View assessment modal (for admin/cook)
    document.querySelectorAll('.view-assessment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const date = this.dataset.date;
            const mealType = this.dataset.mealType;
            const totalPrepared = parseFloat(this.dataset.totalPrepared);
            // Handle both old and new data structure
            const totalLeftover = parseFloat(this.dataset.totalLeftover || this.dataset.totalWasted);
            const totalConsumed = parseFloat(this.dataset.totalConsumed || (totalPrepared - totalLeftover));
            const notes = this.dataset.notes;
            
            // Calculate leftover percentage
            const leftoverPercentage = totalPrepared > 0 
                ? ((totalLeftover / totalPrepared) * 100).toFixed(1) + '%'
                : '0%';
            
            // Format date
            const formattedDate = new Date(date).toLocaleDateString('en-US', {
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            });
            
            document.getElementById('view_date').textContent = formattedDate;
            document.getElementById('view_meal_type').textContent = mealType.charAt(0).toUpperCase() + mealType.slice(1);
            document.getElementById('view_total_prepared').textContent = totalPrepared.toFixed(2) + ' kg';
            document.getElementById('view_total_leftover').textContent = totalLeftover.toFixed(2) + ' kg';
            document.getElementById('view_total_consumed').textContent = totalConsumed.toFixed(2) + ' kg';
            document.getElementById('view_leftover_percentage').textContent = leftoverPercentage;
            document.getElementById('view_notes').textContent = notes || 'No notes provided';
            
            const viewAssessmentModal = new bootstrap.Modal(document.getElementById('viewAssessmentModal'));
            viewAssessmentModal.show();
        });
    });
    
    // No chart needed as we've removed the waste trends section
</script>
@endpush
