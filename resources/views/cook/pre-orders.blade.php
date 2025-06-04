@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Meal Poll Responses</h2>
                    <p class="text-muted" style="color: white;">View student meal attendance submissions</p>
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
                    <h3>{{ $todayAttendance['total'] ?? 0 }}</h3>
                    <p>Today's Attendance</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-egg-fried"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $todayAttendance['breakfast'] ?? 0 }}</h3>
                    <p>Breakfast</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-cup-hot"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $todayAttendance['lunch'] ?? 0 }}</h3>
                    <p>Lunch</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-moon-stars"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $todayAttendance['dinner'] ?? 0 }}</h3>
                    <p>Dinner</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Meal Poll Responses Section -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Student Meal Poll Responses</h5>
                    <div>
                        
                        <select id="dateFilter" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="week">This Week</option>
                        </select>
                        <select id="mealFilter" class="form-select form-select-sm d-inline-block w-auto ms-2">
                            <option value="all">All Meals</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Date</th>
                                    <th>Meal</th>
                                    <th>Attendance Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($preOrders ?? [] as $preOrder)
                                <tr class="pre-order-item" 
                                    data-date="{{ $preOrder->date }}" 
                                    data-meal="{{ $preOrder->meal_type }}"
                                    data-status="{{ $preOrder->is_attending ? 'attending' : 'not-attending' }}">
                                    <td>{{ $preOrder->user->name ?? 'N/A' }}</td>
                                    <td>{{ date('M d, Y', strtotime($preOrder->date)) }}</td>
                                    <td>{{ ucfirst($preOrder->meal_type) }}</td>
                                    <td>
                                        <span class="badge {{ $preOrder->is_attending ? 'bg-success' : 'bg-danger' }}">
                                            {{ $preOrder->is_attending ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No meal poll responses found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title">Meal Poll Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="metric">
                                <div class="metric-value">{{ $todayAttendance['breakfast'] ?? 0 }}</div>
                                <div class="metric-label">Students Attending Breakfast</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric">
                                <div class="metric-value">{{ $todayAttendance['lunch'] ?? 0 }}</div>
                                <div class="metric-label">Students Attending Lunch</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric">
                                <div class="metric-value">{{ $todayAttendance['dinner'] ?? 0 }}</div>
                                <div class="metric-label">Students Attending Dinner</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="waste-tips mt-4">
                        <h6><i class="bi bi-lightbulb"></i> Preparation Recommendations</h6>
                        <ul>
                            <li>Based on poll responses, prepare for <strong>{{ $todayAttendance['breakfast'] ?? 0 }}</strong> students for breakfast.</li>
                            <li>Based on poll responses, prepare for <strong>{{ $todayAttendance['lunch'] ?? 0 }}</strong> students for lunch.</li>
                            <li>Based on poll responses, prepare for <strong>{{ $todayAttendance['dinner'] ?? 0 }}</strong> students for dinner.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@push('styles')
<style>
    .pre-order-item.hidden {
        display: none;
    }
    
    .waste-tips {
        background-color: #e8f5e9;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #43a047;
    }
    
    .chart-container {
        height: 250px;
        position: relative;
    }
    
    .waste-metrics {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    
    .metric {
        text-align: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        flex: 1;
        margin: 0 5px;
    }
    
    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 5px;
    }
    
    .metric-label {
        font-size: 0.85rem;
        color: #555;
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
    
    // Date and meal filtering
    function filterPreOrders() {
        const dateFilter = document.getElementById('dateFilter').value;
        const mealFilter = document.getElementById('mealFilter').value;
        const preOrderItems = document.querySelectorAll('.pre-order-item');
        
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        
        preOrderItems.forEach(item => {
            const itemDate = item.dataset.date;
            const itemMeal = item.dataset.meal;
            
            let dateMatch = true;
            if (dateFilter === 'today') {
                dateMatch = itemDate === today;
            } else if (dateFilter === 'tomorrow') {
                dateMatch = itemDate === tomorrowStr;
            }
            // For 'week', show all dates
            
            const mealMatch = mealFilter === 'all' || itemMeal === mealFilter;
            
            if (dateMatch && mealMatch) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }
    
    document.getElementById('dateFilter').addEventListener('change', filterPreOrders);
    document.getElementById('mealFilter').addEventListener('change', filterPreOrders);
    
    // Export data
    document.getElementById('exportBtn').addEventListener('click', function() {
        // In a real application, you would implement proper CSV export
        alert('Exporting data... This feature would download a CSV file with the filtered data.');
    });
    
    // No chart needed as we've removed the meal attendance trends section
</script>
@endpush
