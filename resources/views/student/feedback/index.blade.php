@extends('layouts.app')

@section('title', 'Provide Feedback')

@section('content')
<!-- Add CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <!-- Enhanced Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <div>
                        <h3 class="mb-1 fw-bold">
                            <i class="bi bi-chat-square-text me-2"></i>Meal Feedback
                        </h3>
                        <p class="mb-0 opacity-75">Share your thoughts about the meals you've had</p>
                    </div>
                    <div class="text-end">
                        <span id="currentDateTime" class="fs-6 text-white"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Enhanced Feedback Form -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-pencil-square me-2"></i>Submit Your Feedback
                    </h5>
                </div>
                <div class="card-body">
                    
                    <form action="{{ route('student.feedback.store') }}" method="POST">
                        @csrf

                        <!-- Manual Meal Input Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="meal_name" class="form-label">Meal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="meal_name" name="meal_name" value="{{ old('meal_name') }}" placeholder="e.g., Chicken Adobo, Beef Stew" required>
                                @error('meal_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="meal_type" class="form-label">Meal Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="meal_type" name="meal_type" required>
                                    <option value="">Select meal type</option>
                                    <option value="breakfast" {{ old('meal_type') == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                    <option value="lunch" {{ old('meal_type') == 'lunch' ? 'selected' : '' }}>Lunch</option>
                                    <option value="dinner" {{ old('meal_type') == 'dinner' ? 'selected' : '' }}>Dinner</option>
                                </select>
                                @error('meal_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="meal_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="meal_date" name="meal_date" value="{{ old('meal_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('meal_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                            
                        <div class="mb-4">
                            <label class="form-label">How would you rate this meal? <span class="text-danger">*</span></label>
                            <div class="rating-stars mb-3">
                                <div class="d-flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input visually-hidden" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                            <label class="form-check-label rating-label" for="rating{{ $i }}">
                                                <i class="bi bi-star rating-icon"></i>
                                                <span class="rating-text">{{ $i }}</span>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <small class="text-muted">1 = Poor, 2 = Fair, 3 = Good, 4 = Very Good, 5 = Excellent</small>
                            </div>
                            @error('rating')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                            
                        <div class="mb-4">
                            <label for="comment" class="form-label">Comments (optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="What did you like or dislike about this meal?">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="suggestions" class="form-label">Suggestions for improvement (optional)</label>
                            <textarea class="form-control" id="suggestions" name="suggestions" rows="3" placeholder="How could we improve this meal?">{{ old('suggestions') }}</textarea>
                            @error('suggestions')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="card" style="border-color: #ff9933;">
                                <div class="card-header" style="background-color: #fff3e0;">
                                    <h6 class="mb-0"><i class="bi bi-shield-check me-2" style="color: #ff9933;"></i>Privacy Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_anonymous">
                                            <strong>Submit feedback anonymously</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        When checked, your identity will be hidden from cook and kitchen staff. They will only see "Anonymous Student" instead of your name.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn text-white" style="background-color: #ff9933; border-color: #ff9933;">
                                <i class="bi bi-send me-2"></i>Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2"></i>Your Feedback History
                </h5>
                @if($studentFeedback->count() > 0)
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="deleteAllHistoryBtn">
                    <i class="bi bi-trash me-1"></i>Delete All History
                </button>
                @endif
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($studentFeedback as $feedback)
                        <div class="list-group-item d-flex justify-content-between align-items-start" id="feedback-history-{{ $feedback->id }}">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1">
                                        {{ $feedback->meal_name ?? ucfirst($feedback->meal_type) }}
                                        <small class="text-muted">- {{ $feedback->meal_date->format('M d, Y') }}</small>
                                    </h6>
                                    <span class="badge text-white" style="background-color: #22bbea;">{{ $feedback->created_at->format('M d') }}</span>
                                </div>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $feedback->rating ? 'bi-star-fill' : 'bi-star' }}" style="color: #ff9933;"></i>
                                    @endfor
                                    <span class="ms-2 small text-muted">({{ $feedback->rating }}/5)</span>
                                </div>

                                @if($feedback->comments || $feedback->suggestions)
                                    <div class="mb-2">
                                        @if($feedback->comments)
                                            <span class="small">
                                                <strong class="text-primary">
                                                    <i class="bi bi-chat-text me-1"></i>Comments:
                                                </strong>
                                                <span class="text-muted">{{ Str::limit($feedback->comments, 80) }}</span>
                                            </span>
                                        @endif

                                        @if($feedback->comments && $feedback->suggestions)
                                            <span class="mx-2 text-muted">•</span>
                                        @endif

                                        @if($feedback->suggestions)
                                            <span class="small">
                                                <strong class="text-success">
                                                    <i class="bi bi-lightbulb me-1"></i>Suggestions:
                                                </strong>
                                                <span class="text-muted">{{ Str::limit($feedback->suggestions, 80) }}</span>
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $feedback->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm ms-2 delete-history-btn" data-id="{{ $feedback->id }}" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-chat-square-text" style="font-size: 4rem; color: #dee2e6;"></i>
                            </div>
                            <h5 class="text-muted mb-3">No Feedback History Yet</h5>
                            <p class="text-muted mb-4">
                                You haven't provided any meal feedback yet.<br>
                                Start sharing your thoughts about the meals to help improve our service!
                            </p>
                          
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .rating-stars {
        font-size: 1.5rem;
    }
    
    .rating-label {
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        transition: all 0.2s ease;
    }
    
    .rating-icon {
        font-size: 1.75rem;
        color: #adb5bd;
    }
    
    .rating-text {
        display: none;
    }
    
    .form-check-input:checked + .rating-label .rating-icon {
        color: #ff9933;
    }

    .form-check-input:checked + .rating-label {
        background-color: #fff3e0;
    }

    .form-check-input:focus + .rating-label {
        box-shadow: 0 0 0 0.25rem rgba(255, 153, 51, 0.25);
    }

    .form-check-input:hover + .rating-label .rating-icon {
        color: #ff9933;
    }

    .btn:hover {
        background-color: #e6851a !important;
        border-color: #e6851a !important;
    }

    .date-time-block { text-align: center; }
    .date-line { font-size: 1.15rem; font-weight: 500; }
    .time-line { font-size: 1rem; font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize star ratings
        const ratingInputs = document.querySelectorAll('input[name="rating"]');
        const ratingLabels = document.querySelectorAll('.rating-label');
        
        ratingLabels.forEach(label => {
            label.addEventListener('mouseover', function() {
                const currentRating = parseInt(this.querySelector('.rating-text').textContent);
                
                ratingLabels.forEach(l => {
                    const labelRating = parseInt(l.querySelector('.rating-text').textContent);
                    const star = l.querySelector('.rating-icon');
                    
                    if (labelRating <= currentRating) {
                        star.classList.remove('bi-star');
                        star.classList.add('bi-star-fill');
                        star.style.color = '#ff9933';
                    } else {
                        star.classList.remove('bi-star-fill');
                        star.classList.add('bi-star');
                        star.style.color = '#adb5bd';
                    }
                });
            });
        });
        
        const ratingContainer = document.querySelector('.rating-stars');
        if (ratingContainer) {
            ratingContainer.addEventListener('mouseout', function() {
                ratingLabels.forEach(label => {
                    const input = document.querySelector(`#${label.getAttribute('for')}`);
                    const star = label.querySelector('.rating-icon');
                    
                    if (!input.checked) {
                        star.classList.remove('bi-star-fill');
                        star.classList.add('bi-star');
                        star.style.color = '#adb5bd';
                    } else {
                        star.classList.remove('bi-star');
                        star.classList.add('bi-star-fill');
                        star.style.color = '#ff9933';
                    }
                });
            });
        }
        // Delete single feedback history
        document.querySelectorAll('.delete-history-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this feedback history?')) {
                    fetch(`/student/feedback/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.getElementById(`feedback-history-${id}`);
                            if (row) row.remove();
                        } else {
                            alert(data.message || 'Failed to delete feedback history.');
                        }
                    })
                    .catch(() => alert('An error occurred while deleting feedback history.'));
                }
            });
        });
        // Delete all feedback history
        const deleteAllBtn = document.getElementById('deleteAllHistoryBtn');
        if (deleteAllBtn) {
            deleteAllBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete ALL feedback history?')) {
                    fetch(`/student/feedback/delete-all`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('div[id^="feedback-history-"]').forEach(row => row.remove());
                        } else {
                            alert(data.message || 'Failed to delete all feedback history.');
                        }
                    })
                    .catch(() => alert('An error occurred while deleting all feedback history.'));
                }
            });
        }

        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const dateString = now.toLocaleDateString('en-US', dateOptions);
            const timeString = now.toLocaleTimeString('en-US', timeOptions);
            const currentDateTimeElement = document.getElementById('currentDateTime');
            if (currentDateTimeElement) {
                currentDateTimeElement.textContent = `${dateString} ${timeString}`;
            }
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    });
</script>
@endpush
