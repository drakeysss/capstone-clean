@extends('layouts.app')

@section('title', 'Provide Feedback')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Provide Feedback</h3>
                        <p class="mb-0 text-muted">Share your thoughts about the meals you've had</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Feedback Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>Meal Feedback Form</h5>
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
                    
                    @if($selectedMenu)
                        <form action="{{ route('student.feedback.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $selectedMenu->id }}">
                            
                            <div class="mb-4">
                                <h5>Providing feedback for:</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $selectedMenu->menu_item }}</h5>
                                        <p class="card-text text-muted">{{ $selectedMenu->description }}</p>
                                        <div class="d-flex">
                                            <span class="badge bg-primary me-2">{{ ucfirst($selectedMenu->meal_type) }}</span>
                                            <span class="badge bg-secondary">{{ $selectedMenu->date->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">How would you rate this meal?</label>
                                <div class="rating-stars mb-3">
                                    <div class="d-flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input visually-hidden" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" required>
                                                <label class="form-check-label rating-label" for="rating{{ $i }}">
                                                    <i class="bi bi-star rating-icon"></i>
                                                    <span class="rating-text">{{ $i }}</span>
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
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
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Feedback</button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Please select a meal from the list to provide feedback.
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Recent Meals</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @forelse($pastWeekMenus as $menuItem)
                                        <a href="{{ route('student.feedback', ['menu_id' => $menuItem->id]) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $menuItem->menu_item }}</h6>
                                                <small>{{ $menuItem->date->format('M d') }}</small>
                                            </div>
                                            <p class="mb-1 text-muted small">{{ Str::limit($menuItem->description, 100) }}</p>
                                            <div>
                                                <span class="badge bg-primary">{{ ucfirst($menuItem->meal_type) }}</span>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="list-group-item">
                                            <p class="mb-0 text-center">No recent meals found.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Previous Feedback -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Your Previous Feedback</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($studentFeedback as $feedback)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1">{{ $feedback->menu->menu_item }}</h6>
                                    <span class="badge bg-primary">{{ $feedback->created_at->format('M d') }}</span>
                                </div>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $feedback->rating ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                @if($feedback->comment)
                                    <p class="mb-1 small">{{ Str::limit($feedback->comment, 100) }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item">
                                <p class="mb-0 text-center">You haven't provided any feedback yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Why Your Feedback Matters</h5>
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
        color: #ffc107;
    }
    
    .form-check-input:checked + .rating-label {
        background-color: #e9ecef;
    }
    
    .form-check-input:focus + .rating-label {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .form-check-input:hover + .rating-label .rating-icon {
        color: #ffc107;
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
                        star.style.color = '#ffc107';
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
                        star.style.color = '#ffc107';
                    }
                });
            });
        }
    });
</script>
@endpush
