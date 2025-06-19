@extends('layouts.app')

@section('title', 'Student Feedback - Cook Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Student Feedback</h3>
                        <p class="mb-0 text-muted">View and analyze student feedback on meals</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary fs-6 me-3">{{ $stats['total_feedback'] }} Total Reviews</span>
                        @if($stats['total_feedback'] > 0)
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDeleteAll()">
                                <i class="bi bi-trash me-1"></i> Delete All Feedback
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter & Search Feedback</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cook.feedback') }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search in Comments & Suggestions</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search for keywords...">
                            </div>
                            <div class="col-md-3">
                                <label for="anonymous_filter" class="form-label">Student Identity</label>
                                <select class="form-control" id="anonymous_filter" name="anonymous_filter">
                                    <option value="">All Feedback</option>
                                    <option value="identified" {{ request('anonymous_filter') == 'identified' ? 'selected' : '' }}>Identified Students</option>
                                    <option value="anonymous" {{ request('anonymous_filter') == 'anonymous' ? 'selected' : '' }}>Anonymous Students</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-control" id="rating" name="rating">
                                    <option value="">All Ratings</option>
                                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Stars)</option>
                                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Stars)</option>
                                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3 Stars)</option>
                                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ (2 Stars)</option>
                                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ (1 Star)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="meal_type" class="form-label">Meal Type</label>
                                <select class="form-control" id="meal_type" name="meal_type">
                                    <option value="">All Meals</option>
                                    <option value="breakfast" {{ request('meal_type') == 'breakfast' ? 'selected' : '' }}>🌅 Breakfast</option>
                                    <option value="lunch" {{ request('meal_type') == 'lunch' ? 'selected' : '' }}>🌞 Lunch</option>
                                    <option value="dinner" {{ request('meal_type') == 'dinner' ? 'selected' : '' }}>🌙 Dinner</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('cook.feedback') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Feedback</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($feedbacks as $feedback)
                        <div class="border-bottom p-3 {{ $feedback->rating <= 2 ? 'bg-light-danger' : '' }} feedback-item"
                             data-feedback-created="{{ $feedback->created_at->toISOString() }}"
                             data-feedback-id="{{ $feedback->id }}"
                             data-rating="{{ $feedback->rating }}">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-primary me-2">{{ ucfirst($feedback->meal_type) }}</span>
                                                {{ $feedback->meal_date->format('M d, Y') }}
                                                @if($feedback->is_anonymous)
                                                    <span class="badge bg-secondary ms-2">
                                                        <i class="bi bi-incognito"></i> Anonymous
                                                    </span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $i <= $feedback->rating ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                            @endfor
                                            <span class="ms-2 fw-bold">{{ $feedback->rating }}/5</span>
                                        </div>
                                    </div>

                                    @if($feedback->comments)
                                        <div class="mb-2">
                                            <strong class="text-primary">💬 Comments:</strong>
                                            <p class="mb-0 ms-3">{{ $feedback->comments }}</p>
                                        </div>
                                    @endif

                                    @if($feedback->suggestions)
                                        <div class="mb-2">
                                            <strong class="text-info">💡 Suggestions:</strong>
                                            <p class="mb-0 ms-3 text-info">{{ $feedback->suggestions }}</p>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-person-circle me-1"></i>
                                            @if($feedback->is_anonymous)
                                                Anonymous Student
                                            @else
                                                {{ $feedback->student->name ?? 'Student' }}
                                            @endif
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $feedback->created_at->format('M d, Y \a\t g:i A') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $feedback->rating >= 4 ? 'success' : ($feedback->rating >= 3 ? 'warning' : 'danger') }} fs-6">
                                            {{ $feedback->rating }}/5 Stars
                                        </span>
                                    </div>
                                    @if($feedback->rating <= 2)
                                        <div class="alert alert-warning p-2 mb-2">
                                            <small><i class="bi bi-exclamation-triangle me-1"></i>Needs Attention</small>
                                        </div>
                                    @elseif($feedback->rating >= 5)
                                        <div class="alert alert-success p-2 mb-2">
                                            <small><i class="bi bi-star me-1"></i>Excellent!</small>
                                        </div>
                                    @endif

                                    <!-- Delete Button -->
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="confirmDelete({{ $feedback->id }}, '{{ $feedback->meal_type }}', '{{ $feedback->meal_date->format('M d, Y') }}')"
                                                title="Delete this feedback">
                                            <i class="bi bi-trash"></i>
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
                            <h4 class="text-muted">Waiting for Student Feedback</h4>
                            <p class="text-muted mb-4">
                                Students haven't submitted any feedback yet.<br>
                                Feedback will appear here once students rate their meals.
                            </p>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>How it works:</strong>
                                <ol class="text-start mt-2 mb-0">
                                    <li>Students eat their meals</li>
                                    <li>Students submit feedback and ratings</li>
                                    <li>Cook reviews feedback to improve meals</li>
                                    <li>Cook can delete inappropriate feedback</li>
                                </ol>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Pagination -->
            @if($feedbacks->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Confirm delete single feedback
function confirmDelete(feedbackId, mealType, mealDate) {
    if (confirm(`Are you sure you want to delete this feedback?\n\nMeal: ${mealType} on ${mealDate}\n\nThis action cannot be undone.`)) {
        deleteFeedback(feedbackId);
    }
}

// Confirm delete all feedback
function confirmDeleteAll() {
    const totalFeedback = {{ $stats['total_feedback'] }};
    if (confirm(`Are you sure you want to delete ALL ${totalFeedback} feedback records?\n\nThis will permanently remove all student feedback from the system.\n\nThis action cannot be undone.`)) {
        if (confirm('This is your final warning!\n\nDeleting all feedback will remove valuable student input data.\n\nAre you absolutely sure?')) {
            deleteAllFeedback();
        }
    }
}

// Delete single feedback via AJAX
function deleteFeedback(feedbackId) {
    const deleteBtn = document.querySelector(`button[onclick*="${feedbackId}"]`);
    const originalText = deleteBtn ? deleteBtn.innerHTML : '';

    // Show loading state
    if (deleteBtn) {
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
    }

    fetch(`/cook/feedback/${feedbackId}`, {
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
            // Remove the feedback item from the page
            const feedbackElement = document.querySelector(`[data-feedback-id="${feedbackId}"]`);
            if (feedbackElement) {
                feedbackElement.style.transition = 'opacity 0.3s ease';
                feedbackElement.style.opacity = '0';
                setTimeout(() => {
                    feedbackElement.remove();
                    // Update the total count
                    updateFeedbackCount(-1);
                }, 300);
            }

            // Show success message
            showAlert('success', 'Feedback deleted successfully');
        } else {
            showAlert('error', data.message || 'Failed to delete feedback');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while deleting feedback');

        // Reset button on error
        if (deleteBtn) {
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalText;
        }
    });
}

// Delete all feedback via AJAX
function deleteAllFeedback() {
    const deleteAllBtn = document.querySelector('button[onclick="confirmDeleteAll()"]');
    const originalText = deleteAllBtn ? deleteAllBtn.innerHTML : '';

    // Show loading state
    if (deleteAllBtn) {
        deleteAllBtn.disabled = true;
        deleteAllBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Clearing All...';
    }

    fetch('/cook/feedback', {
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
            // Show success message before reload
            showAlert('success', 'All feedback deleted successfully');

            // Reload the page to show empty state
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('error', data.message || 'Failed to delete all feedback');

            // Reset button on error
            if (deleteAllBtn) {
                deleteAllBtn.disabled = false;
                deleteAllBtn.innerHTML = originalText;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while deleting all feedback');

        // Reset button on error
        if (deleteAllBtn) {
            deleteAllBtn.disabled = false;
            deleteAllBtn.innerHTML = originalText;
        }
    });
}

// Update feedback count in header
function updateFeedbackCount(change) {
    const countBadge = document.querySelector('.badge.bg-primary.fs-6');
    if (countBadge) {
        const currentText = countBadge.textContent;
        const currentCount = parseInt(currentText.match(/\d+/)[0]);
        const newCount = currentCount + change;
        countBadge.textContent = `${newCount} Total Reviews`;

        // Hide delete all button if no feedback left
        if (newCount === 0) {
            const deleteAllBtn = document.querySelector('button[onclick="confirmDeleteAll()"]');
            if (deleteAllBtn) {
                deleteAllBtn.style.display = 'none';
            }
        }
    }
}

// Show alert messages
function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert at the top of the container
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush

@endsection
