@extends('layouts.app')

@section('title', "Today's Menu - Student Dashboard")

@section('content')
<div class="container-fluid">
    <!-- Enhanced Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <div>
                        <h3 class="mb-1 fw-bold">
                            <i class="bi bi-house-door me-2"></i>Today's Menu
                        </h3>
                        <p class="mb-0 opacity-75">Welcome, {{ Auth::user()->name ?? 'Student' }}!</p>
                    </div>
                    <div class="text-end">
                        <div id="currentDateTimeBlock" class="date-time-block">
                            <div id="currentDate" class="date-line">Date</div>
                            <div id="currentTime" class="time-line">Time</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="/student/pre-order" class="btn btn-primary w-100">
                                <i class="bi bi-clipboard-check me-2"></i>Pre-Orders & Polls
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="/student/menu" class="btn btn-outline-primary w-100">
                                <i class="bi bi-calendar-week me-2"></i>View Menu
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="/student/feedback" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-chat-dots me-2"></i>Give Feedback
                            </a>
                        </div>
                    </div>
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
    #currentTime {
        font-size: 1.5rem;
        font-weight: bold;
    }

    #currentDate {
        font-size: 1rem;
    }

    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Food Waste Prevention Styles */
    .impact-stat {
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    /* Meal Menu Styles */
    .meal-item {
        padding: 10px;
        border-radius: 5px;
        transition: all 0.2s ease;
    }

    .meal-item:hover {
        background-color: #f8f9fa;
    }

    .week-menu {
        transition: all 0.3s ease;
    }

    .badge {
        font-size: 0.7rem;
        font-weight: normal;
        padding: 0.3rem 0.5rem;
    }

    .impact-stat:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .impact-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 5px;
    }

    .impact-label {
        font-size: 0.9rem;
        color: #555;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .date-time-block { text-align: center; }
    .date-line { font-size: 1.15rem; font-weight: 500; }
    .time-line { font-size: 1rem; font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace; }
</style>
@endpush

@push('scripts')
<script>
    function updateDateTimeHeader() {
        const now = new Date();
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const timeString = now.toLocaleTimeString('en-US', timeOptions);
        const dateString = now.toLocaleDateString('en-US', dateOptions);
        const el = document.getElementById('currentDateTime');
        if (el) el.textContent = `${dateString} ${timeString}`;
    }
    updateDateTimeHeader();
    setInterval(updateDateTimeHeader, 1000);

    // Week cycle toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const weekCycleSelect = document.getElementById('weekCycleSelect');
        const week1Menu = document.getElementById('week1Menu');
        const week2Menu = document.getElementById('week2Menu');

        if (weekCycleSelect && week1Menu && week2Menu) {
            weekCycleSelect.addEventListener('change', function() {
                const selectedCycle = this.value;

                if (selectedCycle === '1') {
                    week1Menu.style.display = 'block';
                    week2Menu.style.display = 'none';
                } else {
                    week1Menu.style.display = 'none';
                    week2Menu.style.display = 'block';
                }
            });
        }
    });

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
    });

    document.addEventListener('DOMContentLoaded', function() {
        function updateDateTimeBlock() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', timeOptions);
        }
        updateDateTimeBlock();
        setInterval(updateDateTimeBlock, 1000);
    });
</script>
@endpush
