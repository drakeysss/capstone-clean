@extends('layouts.app')

@section('title', "Student Dashboard")

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="text-muted" style="color: white;">Your meal planning and feedback dashboard</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Dashboard Overview Section -->
    <div class="row mb-4">
        <!-- Today's Menu -->
        <div class="col-md-6 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Today's Menu</h5>
                        <small class="text-muted">
                            {{ now()->format('l, F j, Y') }}
                        </small>
                    </div>
                    <a href="{{ route('student.menu') }}" class="btn btn-sm btn-outline-primary">View Full Menu</a>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Meal Type</th>
                                <th>Menu Item</th>
                                <th>Ingredients</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayMenu ?? [] as $mealType => $menus)
                                @foreach($menus as $menu)
                                <tr>
                                    <td>{{ ucfirst($mealType) }}</td>
                                    <td>{{ $menu->name ?? 'No meal planned' }}</td>
                                    <td>
                                        <small class="text-muted">
                                            @if(is_array($menu->ingredients))
                                                {{ implode(', ', $menu->ingredients) }}
                                            @else
                                                {{ $menu->ingredients ?? 'No ingredients listed' }}
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No menu available for today</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- My Feedback History -->
        <div class="col-md-6 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">My Recent Feedback</h5>
                    <a href="{{ route('student.feedback') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Meal</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $recentFeedback = \App\Models\Feedback::where('student_id', Auth::id())
                                    ->orderBy('created_at', 'desc')
                                    ->take(3)
                                    ->get();
                            @endphp
                            @forelse($recentFeedback as $feedback)
                                <tr>
                                    <td>{{ $feedback->created_at->format('M d, Y') }}</td>
                                    <td>{{ $feedback->meal_name ?? ucfirst($feedback->meal_type) }}</td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $feedback->rating ? 'bi-star-fill' : 'bi-star' }}" style="color: #ff9933; font-size: 0.8rem;"></i>
                                        @endfor
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No feedback submitted yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Recent Pre-Orders -->
        <div class="col-md-6 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Recent Pre-Orders</h5>
                    <a href="{{ route('student.pre-order') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Meal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentPreOrders ?? [] as $preOrder)
                                <tr>
                                    <td>{{ $preOrder->date->format('M d, Y') }}</td>
                                    <td>{{ ucfirst($preOrder->meal_type) }}</td>
                                    <td>
                                        <span class="status-badge {{ $preOrder->is_attending ? 'active' : 'cancelled' }}">
                                            {{ $preOrder->is_attending ? 'Attending' : 'Not Attending' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No recent pre-orders</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>

    <!-- Meal Attendance Polls Section -->
    @if(count($activeMealPolls ?? []) > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Meal Attendance Polls</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($activeMealPolls as $poll)
                            <div class="list-group-item {{ isset($pollResponses[$poll->id]) ? 'bg-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1">{{ $poll->title }}</h5>
                                        <p class="mb-1">{{ $poll->content }}</p>
                                        <small class="text-muted">Expires on {{ date('M d, Y', strtotime($poll->expiry_date)) }}</small>
                                    </div>
                                    @if(isset($pollResponses[$poll->id]))
                                        <span class="badge bg-success">Response Submitted: {{ $pollResponses[$poll->id] }}</span>
                                    @endif
                                </div>

                                @if(!isset($pollResponses[$poll->id]))
                                <div class="mt-3">
                                    <form action="{{ route('student.poll-response.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="announcement_id" value="{{ $poll->id }}">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Will you attend this meal?</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach(json_decode($poll->poll_options) as $option)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="response" value="{{ $option }}" id="option{{ $poll->id }}_{{ $loop->index }}">
                                                        <label class="form-check-label" for="option{{ $poll->id }}_{{ $loop->index }}">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Submit Response</button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    </div>

    <!-- Meal Polls Section -->
    @if(count($activeMealPolls ?? []) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title"><i class="bi bi-check2-square me-2"></i>Meal Polls</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i> Please participate in the meal polls below to help us plan our menu better. Your input helps us reduce food waste and improve meal options.
                    </div>

                    <div class="row">
                        @foreach($activeMealPolls as $poll)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $poll->title }}</h6>
                                        <span class="badge bg-primary">{{ date('M d, Y', strtotime($poll->expiry_date)) }}</span>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $poll->content }}</p>

                                        @php
                                            $hasResponded = isset($pollResponses[$poll->id]);
                                        @endphp

                                        @if($hasResponded)
                                            <div class="alert alert-success">
                                                <i class="bi bi-check-circle me-2"></i> You've already responded to this poll. Thank you!
                                            </div>
                                        @else
                                            <form action="{{ route('student.poll-response.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="announcement_id" value="{{ $poll->id }}">

                                                <div class="mb-3">
                                                    <label class="form-label">Your response:</label>
                                                    <div class="list-group">
                                                        @foreach(json_decode($poll->poll_options) as $option)
                                                            <label class="list-group-item">
                                                                <input class="form-check-input me-1" type="radio" name="response" value="{{ $option }}" required>
                                                                <strong>{{ $option }}</strong>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="comment" class="form-label">Additional Comments (Optional):</label>
                                                    <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Submit Response</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* General Styles */
    .container-fluid {
        background-color: #f8f9fc;
    }

    /* Welcome Card */
    .welcome-card {
        background: #22bbea;
        color: white;
        border-radius: 10px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .current-time {
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Main Cards */
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        transition: all 0.3s ease;
    }

    .main-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
    }

    .card-header {
        background: none;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #ff9933;
    }

    /* Table Styles */
    .table {
        margin: 0;
    }

    .table th {
        font-weight: 600;
        color: #6c757d;
        border-top: none;
        font-size: 0.875rem;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.pending {
        background-color: #f6c23e;
        color: white;
    }

    .status-badge.completed {
        background-color: #1cc88a;
        color: white;
    }

    .status-badge.cancelled {
        background-color: #e74a3b;
        color: white;
    }

    .status-badge.active {
        background-color: #1cc88a;
        color: white;
    }

    /* Spending Stats */
    .spending-stat {
        padding: 10px;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .spending-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 5px;
    }

    .spending-label {
        font-size: 0.8rem;
        color: #6c757d;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
            padding: 15px;
        }

        .current-time {
            font-size: 1rem;
            justify-content: center;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }

        .card-title {
            font-size: 1rem;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
        }

        .spending-value {
            font-size: 1.2rem;
        }

        .spending-label {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding: 0.5rem !important;
        }

        .welcome-card {
            padding: 10px;
        }

        .spending-stat {
            padding: 8px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    console.log('🚀 Student Dashboard script starting...');

    {!! \App\Services\WeekCycleService::getJavaScriptFunction() !!}

    console.log('📅 Week cycle function loaded');

    // UNIFIED: Real-time date and time display
    function updateDateTime() {
        const weekInfo = getCurrentWeekCycle();
        document.getElementById('currentDateTime').innerHTML = `${weekInfo.displayDate}<br><small>${weekInfo.timeString}</small>`;
    }

    updateDateTime();
    setInterval(updateDateTime, 1000); // Update every second for real-time display

    // Poll response handling
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form[action*="poll-response"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Response submitted successfully!');
                        form.reset();
                        location.reload(); // Refresh to show updated data
                    } else {
                        alert(data.message || 'Failed to submit response');
                    }
                })
                .catch(error => {
                    alert('An error occurred while submitting response');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Response';
                });
            });
        });

        console.log('✅ Student Dashboard loaded successfully');
    });
</script>
@endpush
