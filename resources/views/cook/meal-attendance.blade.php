@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Food Waste Prevention Dashboard</h2>
                    <p class="text-muted" style="color: white;">Track meal attendance and reduce food waste</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card meal-attendance">
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $mealAttendance['total'] ?? 0 }}</h3>
                    <p>Weekly Attendance</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card waste-reduction">
                <div class="stat-icon">
                    <i class="bi bi-recycle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $wasteReduction['percentage'] ?? 0 }}%</h3>
                    <p>Waste Reduction</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card inventory">
                <div class="stat-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-info">
                    <h3>${{ $wasteReduction['cost_saved'] ?? 0 }}</h3>
                    <p>Cost Savings</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card completed">
                <div class="stat-icon">
                    <i class="bi bi-basket"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $wasteReduction['meals_saved'] ?? 0 }}</h3>
                    <p>Meals Saved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Attendance Section -->
    <div class="row mb-4">
        <div class="col-xl-8 mb-4">
            <div class="card main-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Meal Attendance Trends</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card main-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Today's Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="meal-stats">
                        <div class="meal-stat-item">
                            <h6>Breakfast</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $mealAttendance['breakfast_percentage'] ?? 0 }}%" aria-valuenow="{{ $mealAttendance['breakfast_percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ $mealAttendance['breakfast_percentage'] ?? 0 }}%</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>{{ $mealAttendance['breakfast'] ?? 0 }} attending</small>
                                <small>{{ $mealAttendance['breakfast_not'] ?? 0 }} not attending</small>
                                <small>{{ $mealAttendance['breakfast_undecided'] ?? 0 }} undecided</small>
                            </div>
                        </div>
                        
                        <div class="meal-stat-item mt-4">
                            <h6>Lunch</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $mealAttendance['lunch_percentage'] ?? 0 }}%" aria-valuenow="{{ $mealAttendance['lunch_percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ $mealAttendance['lunch_percentage'] ?? 0 }}%</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>{{ $mealAttendance['lunch'] ?? 0 }} attending</small>
                                <small>{{ $mealAttendance['lunch_not'] ?? 0 }} not attending</small>
                                <small>{{ $mealAttendance['lunch_undecided'] ?? 0 }} undecided</small>
                            </div>
                        </div>
                        
                        <div class="meal-stat-item mt-4">
                            <h6>Dinner</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $mealAttendance['dinner_percentage'] ?? 0 }}%" aria-valuenow="{{ $mealAttendance['dinner_percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ $mealAttendance['dinner_percentage'] ?? 0 }}%</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>{{ $mealAttendance['dinner'] ?? 0 }} attending</small>
                                <small>{{ $mealAttendance['dinner_not'] ?? 0 }} not attending</small>
                                <small>{{ $mealAttendance['dinner_undecided'] ?? 0 }} undecided</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Waste Reduction and Poll Responses -->
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card main-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Waste Reduction Impact</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="wasteReductionChart"></canvas>
                    </div>
                    <div class="waste-tips mt-3">
                        <h6><i class="bi bi-lightbulb"></i> Recommendations</h6>
                        <ul>
                            <li>Based on attendance trends, consider preparing <strong>{{ $wasteReduction['recommendation'] ?? '15%' }}</strong> less food for tomorrow's {{ $wasteReduction['meal_type'] ?? 'dinner' }}.</li>
                            <li>Encourage more students to respond to meal polls by offering incentives.</li>
                            <li>Consider adjusting portion sizes based on historical consumption data.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card main-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Active Meal Polls</h5>
                    <a href="{{ route('cook.announcements') }}" class="btn btn-view-all">Create New Poll</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Poll</th>
                                    <th>Responses</th>
                                    <th>Will Attend</th>
                                    <th>Will Not Attend</th>
                                    <th>Undecided</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mealPolls as $poll)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $poll->title }}</span>
                                            <small class="text-muted">Expires: {{ date('M d, Y', strtotime($poll->expiry_date)) }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $pollResponses[$poll->id]['total'] ?? 0 }}</td>
                                    <td>{{ $pollResponses[$poll->id]['will_attend'] ?? 0 }}</td>
                                    <td>{{ $pollResponses[$poll->id]['will_not_attend'] ?? 0 }}</td>
                                    <td>{{ $pollResponses[$poll->id]['undecided'] ?? 0 }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No active meal polls found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .meal-stat-item {
        padding: 10px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    
    .waste-tips {
        background-color: #e8f5e9;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #43a047;
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Real-time date and time display
    function updateDateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit'
        };
        
        document.getElementById('currentDateTime').innerHTML = `${now.toLocaleDateString('en-US', options)} ${now.toLocaleTimeString('en-US', timeOptions)}`;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 60000);
    
    // Attendance Trend Chart
    document.addEventListener('DOMContentLoaded', function() {
        const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        
        // Sample data - in production, this would come from the backend
        const weeks = {!! json_encode($historicalData['weeks'] ?? ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Current Week']) !!};
        const attendancePercentage = {!! json_encode($historicalData['attendance_percentage'] ?? [65, 70, 75, 82, 88]) !!};
        
        const attendanceTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: weeks,
                datasets: [
                    {
                        label: 'Attendance Percentage',
                        data: attendancePercentage,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    }
                }
            }
        });
        
        // Waste Reduction Chart
        const wasteCtx = document.getElementById('wasteReductionChart').getContext('2d');
        
        // Sample data - in production, this would come from the backend
        const wasteData = {!! json_encode($historicalData['waste_kg'] ?? [120, 105, 95, 80, 65]) !!};
        
        const wasteReductionChart = new Chart(wasteCtx, {
            type: 'bar',
            data: {
                labels: weeks,
                datasets: [
                    {
                        label: 'Food Waste (kg)',
                        data: wasteData,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Kilograms (kg)'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
