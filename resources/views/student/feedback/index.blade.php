@extends('layouts.app')

@section('title', 'Provide Feedback')

@section('content')
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
                        <i class="bi bi-star-fill text-warning fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Enhanced Feedback Form -->
        <div class="col-lg-8 col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-pencil-square me-2"></i>Submit Your Feedback
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
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
        
        <!-- Enhanced Previous Feedback -->
        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #ff9933, #e6851a);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2"></i>Your Feedback History
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($studentFeedback as $feedback)
                            <div class="list-group-item">
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
                                @if($feedback->comments)
                                    <p class="mb-1 small">{{ Str::limit($feedback->comments, 100) }}</p>
                                @endif
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $feedback->created_at->diffForHumans() }}
                                </small>
                            </div>
                        @empty
                            <div class="list-group-item">
                                <p class="mb-0 text-center text-muted">You haven't provided any feedback yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-lightbulb me-2"></i>Why Your Feedback Matters
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Helps improve meal quality</li>
                        <li>Influences future menu planning</li>
                        <li>Reduces food waste</li>
                        <li>Ensures dietary needs are met</li>
                    </ul>
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
    });
</script>
@endpush
