@extends('layouts.app')

@section('title', 'Student Feedback - Kitchen Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Student Feedback</h3>
                        <p class="mb-0 text-muted">View student feedback on meals and preparation</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary fs-6">{{ $stats['total_feedback'] }} Total Reviews</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-primary">{{ $stats['average_rating'] }}</h2>
                    <p class="mb-0">Average Rating</p>
                    <div class="mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $stats['average_rating'] ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-success">{{ $stats['total_feedback'] }}</h2>
                    <p class="mb-0">Total Feedback</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-info">{{ $stats['recent_feedback'] }}</h2>
                    <p class="mb-0">This Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-warning">{{ $stats['rating_distribution'][5] }}</h2>
                    <p class="mb-0">5-Star Reviews</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-secondary">{{ $stats['anonymous_feedback'] }}</h2>
                    <p class="mb-0">Anonymous</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-dark">{{ $stats['identified_feedback'] }}</h2>
                    <p class="mb-0">Identified</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Meal Type Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Meal Type Performance</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-warning">{{ $stats['meal_type_stats']['breakfast']['avg_rating'] ?? 'N/A' }}</h4>
                                <p class="mb-1"><strong>Breakfast</strong></p>
                                <small class="text-muted">{{ $stats['meal_type_stats']['breakfast']['count'] }} reviews</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-warning">{{ $stats['meal_type_stats']['lunch']['avg_rating'] ?? 'N/A' }}</h4>
                                <p class="mb-1"><strong>Lunch</strong></p>
                                <small class="text-muted">{{ $stats['meal_type_stats']['lunch']['count'] }} reviews</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-warning">{{ $stats['meal_type_stats']['dinner']['avg_rating'] ?? 'N/A' }}</h4>
                                <p class="mb-1"><strong>Dinner</strong></p>
                                <small class="text-muted">{{ $stats['meal_type_stats']['dinner']['count'] }} reviews</small>
                            </div>
                        </div>
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
                    <form method="GET" action="{{ route('kitchen.feedback') }}">
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
                                <a href="{{ route('kitchen.feedback') }}" class="btn btn-outline-secondary">
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
                                        <div class="alert alert-warning p-2 mb-0">
                                            <small><i class="bi bi-exclamation-triangle me-1"></i>Needs Attention</small>
                                        </div>
                                    @elseif($feedback->rating >= 5)
                                        <div class="alert alert-success p-2 mb-0">
                                            <small><i class="bi bi-star me-1"></i>Excellent!</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center">
                            <i class="bi bi-chat-dots fs-1 text-muted"></i>
                            <p class="mb-0 mt-2">No feedback found matching your criteria.</p>
                            <small class="text-muted">Try adjusting your filters or check back later.</small>
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
@endsection
