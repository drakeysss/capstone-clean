@extends('layouts.app')

@section('title', 'Today\'s Menu - Student Dashboard')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Today's Menu</h3>
                        <p class="mb-0 text-muted">Welcome, {{ Auth::user()->name ?? 'Student' }}!</p>
                    </div>
                    <div class="text-end">
                        <div id="currentTime" class="h4 mb-0 text-primary"></div>
                        <div id="currentDate" class="text-muted"></div>
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

    <!-- Announcements Section -->
    @if(count($announcements) > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-megaphone me-2"></i>Announcements</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($announcements->where('is_poll', false) as $announcement)
                            <div class="list-group-item">
                                <h5 class="mb-1">{{ $announcement->title }}</h5>
                                <p class="mb-1">{{ $announcement->content }}</p>
                                <small class="text-muted">Posted by {{ $announcement->user->name }} · Expires on {{ $announcement->expiry_date->format('M d, Y') }}</small>
                                
                                @if($announcement->is_poll && !$announcement->pollResponses()->where('user_id', Auth::id())->exists())
                                <div class="mt-3">
                                    <form action="{{ route('student.poll-response.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="announcement_id" value="{{ $announcement->id }}">
                                        <div class="mb-3">
                                            <label class="form-label">Your response:</label>
                                            @foreach($announcement->poll_options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="response" value="{{ $option }}" id="option{{ $loop->index }}">
                                                    <label class="form-check-label" for="option{{ $loop->index }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary">Submit Response</button>
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
    
    <!-- Today's Meals Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-day me-2"></i>Today's Meals</h5>
                </div>
                <div class="card-body">
                    @if(count($todaysMenu ?? []) > 0)
                        <div class="row">
                            @foreach(['breakfast', 'lunch', 'dinner'] as $mealType)
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 {{ $nextMeal == $mealType ? 'border-primary' : '' }}">
                                        <div class="card-header {{ $nextMeal == $mealType ? 'bg-primary text-white' : 'bg-light' }}">
                                            <h5 class="mb-0">{{ ucfirst($mealType) }}</h5>
                                            <small>{{ $mealTimes[$mealType] ?? '' }}</small>
                                            @if($nextMeal == $mealType)
                                                <span class="badge bg-warning text-dark ms-2">Next Meal</span>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            @if(isset($todaysMenu[$mealType]) && count($todaysMenu[$mealType]) > 0)
                                                <ul class="list-group list-group-flush">
                                                    @foreach($todaysMenu[$mealType] as $menuItem)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-0">{{ $menuItem->name }}</h6>
                                                                <small class="text-muted">{{ $menuItem->description }}</small>
                                                            </div>
                                                            @if(isset($studentPreOrders[$mealType]) && $studentPreOrders[$mealType]->menu_id == $menuItem->id)
                                                                <span class="badge bg-success">Pre-ordered</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                
                                                <!-- Pre-order status -->
                                                <div class="mt-3">
                                                    @if(isset($studentPreOrders[$mealType]))
                                                        <div class="alert alert-success mb-0">
                                                            <i class="bi bi-check-circle me-2"></i>You've pre-ordered for this meal
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>You haven't pre-ordered for this meal
                                                        </div>
                                                        <a href="{{ route('student.pre-order') }}" class="btn btn-primary btn-sm mt-2">Pre-order Now</a>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="alert alert-info mb-0">
                                                    <i class="bi bi-info-circle me-2"></i>No menu items available for {{ $mealType }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>No menu items available for today
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('student.menu') }}" class="btn btn-outline-primary"><i class="bi bi-calendar-week me-2"></i>View Weekly Menu</a>
                        <a href="{{ route('student.pre-order') }}" class="btn btn-primary"><i class="bi bi-calendar-check me-2"></i>Manage Pre-orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Week Menu Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Weekly Meal Menu</h5>
                    <div>
                        <select id="weekCycleSelect" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="1">Current Week Menu</option>
                            <option value="2">Next Week Menu</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> Below is the menu for the week. Please check the meal options and pre-order your meals to help us reduce food waste.
                    </div>
                    
                    <!-- Week 1 Menu (Current Week) -->
                    <div id="week1Menu" class="week-menu">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th width="25%">Breakfast <span class="badge bg-danger">Deadline: {{ $cutoffTimes['breakfast'] }}</span></th>
                                        <th width="25%">Lunch <span class="badge bg-danger">Deadline: {{ $cutoffTimes['lunch'] }}</span></th>
                                        <th width="25%">Dinner <span class="badge bg-danger">Deadline: {{ $cutoffTimes['dinner'] }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daysOfWeek as $day)
                                        <tr>
                                            <td class="fw-bold">{{ $day }}</td>
                                            @foreach($mealTypes as $mealType)
                                                <td>
                                                    @if(isset($week1Menu[$day]) && $week1Menu[$day]->where('meal_type', $mealType)->count() > 0)
                                                        @foreach($week1Menu[$day]->where('meal_type', $mealType) as $menuItem)
                                                            <div class="meal-item">
                                                                <div class="fw-bold">{{ $menuItem->name }}</div>
                                                                <small class="text-muted">{{ $menuItem->description }}</small>
                                                                <div class="mt-2">
                                                                    <a href="{{ route('student.pre-order') }}" class="btn btn-sm btn-outline-primary">Pre-order</a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-muted">No menu available</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Week 2 Menu (Next Week) -->
                    <div id="week2Menu" class="week-menu" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th width="25%">Breakfast <span class="badge bg-danger">Deadline: {{ $cutoffTimes['breakfast'] }}</span></th>
                                        <th width="25%">Lunch <span class="badge bg-danger">Deadline: {{ $cutoffTimes['lunch'] }}</span></th>
                                        <th width="25%">Dinner <span class="badge bg-danger">Deadline: {{ $cutoffTimes['dinner'] }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daysOfWeek as $day)
                                        <tr>
                                            <td class="fw-bold">{{ $day }}</td>
                                            @foreach($mealTypes as $mealType)
                                                <td>
                                                    @if(isset($week2Menu[$day]) && $week2Menu[$day]->where('meal_type', $mealType)->count() > 0)
                                                        @foreach($week2Menu[$day]->where('meal_type', $mealType) as $menuItem)
                                                            <div class="meal-item">
                                                                <div class="fw-bold">{{ $menuItem->name }}</div>
                                                                <small class="text-muted">{{ $menuItem->description }}</small>
                                                                <div class="mt-2">
                                                                    <a href="{{ route('student.pre-order') }}" class="btn btn-sm btn-outline-primary">Pre-order</a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-muted">No menu available</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('student.menu') }}" class="btn btn-outline-primary"><i class="bi bi-calendar-week me-2"></i>View Full Menu</a>
                        <a href="{{ route('student.pre-order') }}" class="btn btn-primary"><i class="bi bi-calendar-check me-2"></i>Manage Pre-orders</a>
                    </div>
                </div>
            </div>
        </div>
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
</style>
@endpush

@push('scripts')
<script>
    // Update date/time every second
    function updateDateTime() {
        const now = new Date();
        
        // Format time
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds}`;
        
        // Format date
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateString = now.toLocaleDateString('en-US', options);
        
        // Update elements
        document.getElementById('currentTime').textContent = timeString;
        document.getElementById('currentDate').textContent = dateString;
    }
    
    // Call immediately and then every second
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
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
</script>
@endpush
