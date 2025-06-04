@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Weekly Menu</h2>
                    <p class="text-muted" style="color: white;">View the menu for the entire week</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Menu Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Weekly Menu Plan</h5>
                    <div>
                        <span class="badge bg-primary">{{ $weekCycle == 1 ? 'Week 1 & 3' : 'Week 2 & 4' }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="15%">Day</th>
                                    <th width="28%">Breakfast</th>
                                    <th width="28%">Lunch</th>
                                    <th width="28%">Dinner</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    <tr class="{{ strtolower(date('l')) == $day ? 'table-primary' : '' }}">
                                        <td class="fw-bold">{{ ucfirst($day) }}</td>
                                        <td>
                                            @if(isset($weeklyMenus[$day]['breakfast']))
                                                <div class="meal-item">
                                                    <div class="fw-bold">{{ $weeklyMenus[$day]['breakfast']->name }}</div>
                                                    <div class="small text-muted">{{ $weeklyMenus[$day]['breakfast']->description }}</div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success">₱{{ number_format($weeklyMenus[$day]['breakfast']->price, 2) }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-muted">No menu available</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($weeklyMenus[$day]['lunch']))
                                                <div class="meal-item">
                                                    <div class="fw-bold">{{ $weeklyMenus[$day]['lunch']->name }}</div>
                                                    <div class="small text-muted">{{ $weeklyMenus[$day]['lunch']->description }}</div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success">₱{{ number_format($weeklyMenus[$day]['lunch']->price, 2) }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-muted">No menu available</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($weeklyMenus[$day]['dinner']))
                                                <div class="meal-item">
                                                    <div class="fw-bold">{{ $weeklyMenus[$day]['dinner']->name }}</div>
                                                    <div class="small text-muted">{{ $weeklyMenus[$day]['dinner']->description }}</div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success">₱{{ number_format($weeklyMenus[$day]['dinner']->price, 2) }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-muted">No menu available</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Menu Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title">Today's Menu</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($todayMenu)
                            @if(isset($todayMenu['breakfast']))
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Breakfast</h6>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $todayMenu['breakfast']->name }}</h5>
                                            <p class="card-text">{{ $todayMenu['breakfast']->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-success">₱{{ number_format($todayMenu['breakfast']->price, 2) }}</span>
                                                <a href="{{ route('student.pre-order') }}" class="btn btn-sm btn-primary">Pre-order</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($todayMenu['lunch']))
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Lunch</h6>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $todayMenu['lunch']->name }}</h5>
                                            <p class="card-text">{{ $todayMenu['lunch']->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-success">₱{{ number_format($todayMenu['lunch']->price, 2) }}</span>
                                                <a href="{{ route('student.pre-order') }}" class="btn btn-sm btn-primary">Pre-order</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($todayMenu['dinner']))
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Dinner</h6>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $todayMenu['dinner']->name }}</h5>
                                            <p class="card-text">{{ $todayMenu['dinner']->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-success">₱{{ number_format($todayMenu['dinner']->price, 2) }}</span>
                                                <a href="{{ route('student.pre-order') }}" class="btn btn-sm btn-primary">Pre-order</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No menu available for today. Please check back later.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .meal-item {
        padding: 10px;
        border-radius: 5px;
    }
    
    .table-primary .meal-item {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>
@endpush

@push('scripts')
<script>
    // Real-time date and time display
    function updateDateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
    }
    
    // Update the time every second
    updateDateTime();
    setInterval(updateDateTime, 60000);
</script>
@endpush
