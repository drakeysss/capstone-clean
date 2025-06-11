@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="text-muted" style="color: white;">Here's an overview of your kitchen operations</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Key Features Overview Section -->
    <div class="row mb-4">
        <!-- Today's Menu Overview (Moved to appear first) -->
        <div class="col-md-6 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Today's Menu <span class="badge bg-primary ms-2" id="todayDayBadge"></span></h5>
                    <a href="{{ route('cook.menu.index') }}" class="btn btn-sm btn-outline-primary">View Weekly Plan</a>
                </div>
                <div class="card-body">
                    <!-- Week cycle indicator -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted">Week Cycle:</span>
                            <span class="badge bg-info ms-2" id="weekCycleBadge">Week 1 & 3</span>
                        </div>
                        <div>
                            <span class="text-muted">Date:</span>
                            <span class="ms-2" id="todayDateDisplay"></span>
                        </div>
                    </div>
                    
                    <!-- Today's meals -->
                    <div class="row">
                        <!-- Breakfast -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light text-center">
                                    <h6 class="mb-0">Breakfast</h6>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted">Loading...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lunch -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light text-center">
                                    <h6 class="mb-0">Lunch</h6>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted">Loading...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Dinner -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light text-center">
                                    <h6 class="mb-0">Dinner</h6>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted">Loading...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Feedback Overview -->
       

    <!-- Menu & Inventory Overview Section -->
   

        <!-- Inventory Overview -->
        <div class="col-xl-6 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Inventory Overview</h5>
                    <a href="{{ route('cook.inventory') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="row h-100">
                        <div class="col-md-4">
                            <div class="overview-stats">
                                <div class="stat">
                                    <span class="stat-value">{{ $lowStockItems ?? 0 }}</span>
                                    <span class="stat-label">Low Stock</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">{{ $totalItems ?? 0 }}</span>
                                    <span class="stat-label">Total Items</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lowStockItemsList->take(3) ?? [] as $item)
                                        <tr>
                                            <td data-label="Item Name">{{ $item->name }}</td>
                                            <td data-label="Quantity">{{ $item->quantity }}</td>
                                            <td data-label="Status">
                                                <span class="status-badge warning">
                                                    Low Stock
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No low stock items</td>
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

    /* Food Waste Prevention Styles */
    .meal-attendance {
        background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%);
        font-weight: 600;
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

    /* Feature Overview Cards */
    .feature-overview-card {
        border: none;
        overflow: hidden;
    }

    .feature-overview-card .card-header {
        border: none;
        padding: 1rem 1.25rem;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #ff9933 0%, #ff7700 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9500 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #22bbea 0%, #0099cc 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    .bg-gradient-dark {
        background: linear-gradient(135deg, #343a40 0%, #212529 100%);
    }

    .metric-item {
        padding: 0.5rem 0;
    }

    .metric-item h4, .metric-item h5 {
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .metric-item small {
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .feature-overview-card .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .feature-overview-card .btn-light:hover {
        background: white;
        transform: scale(1.1);
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

    .card-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Overview Stats */
    .overview-stats {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding: 1rem;
        background: #f8f9fc;
        border-radius: 0.5rem;
        height: 100%;
    }

    .stat {
        text-align: center;
    }

    .stat-value {
        display: block;
        font-size: 2rem;
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        display: block;
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Order Items */
    .order-items {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .order-items .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }

    /* Buttons */
    .btn-view-all {
        background: #22bbea;
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.35rem;
        text-decoration: none;
    }

    .btn-view-all:hover {
        background: #ff9933;
        color: white;
    }

    .btn-filter {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        color: #4e73df;
        padding: 0.375rem 0.75rem;
        border-radius: 0.35rem;
    }

    .btn-icon {
        background: none;
        border: none;
        color: #4e73df;
        padding: 0.25rem;
        margin: 0 0.25rem;
    }

    .btn-icon:hover {
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

    .status-badge.warning {
        background-color: #f6c23e;
        color: white;
    }

    .status-badge.active {
        background-color: #1cc88a;
        color: white;
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

        .overview-stats {
            flex-direction: row;
            justify-content: space-around;
            padding: 0.75rem;
        }

        .stat-value {
            font-size: 1.5rem;
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
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding: 0.5rem !important;
        }

        .welcome-card {
            padding: 10px;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .stat-label {
            font-size: 0.75rem;
        }

        .overview-stats {
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    {!! \App\Services\WeekCycleService::getJavaScriptFunction() !!}

    // UNIFIED: Real-time date and time display
    function updateDateTime() {
        const weekInfo = getCurrentWeekCycle();

        document.getElementById('currentDateTime').innerHTML = `${weekInfo.displayDate}<br><small>${weekInfo.timeString}</small>`;

        // Also update today's menu information in real-time
        updateTodayMenu();
    }

    updateDateTime();
    setInterval(updateDateTime, 1000); // Update every second for real-time display
    
    document.addEventListener('DOMContentLoaded', function() {
        // Leftover Chart
        const leftoverCtx = document.getElementById('leftoverChart').getContext('2d');
        
        // Sample data for leftover chart
        const leftoverData = {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Leftover (kg)',
                data: [{{ $dailyLeftoverKg[0] ?? 0 }}, {{ $dailyLeftoverKg[1] ?? 0 }}, {{ $dailyLeftoverKg[2] ?? 0 }}, {{ $dailyLeftoverKg[3] ?? 0 }}, {{ $dailyLeftoverKg[4] ?? 0 }}, {{ $dailyLeftoverKg[5] ?? 0 }}, {{ $dailyLeftoverKg[6] ?? 0 }}],
                backgroundColor: 'rgba(78, 115, 223, 0.2)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#fff',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#2e59d9',
                pointHoverBorderColor: '#fff',
                pointHitRadius: 10,
                fill: true
            }]
        };
        
        new Chart(leftoverCtx, {
            type: 'line',
            data: leftoverData,
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: {
                            callback: function(value) {
                                return value + ' kg';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
    
    // UNIFIED: Update today's menu based on current day
    function updateTodayMenu() {
        const weekInfo = getCurrentWeekCycle();

        // Set the day badge
        const todayDayBadge = document.getElementById('todayDayBadge');
        if (todayDayBadge) {
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const todayName = dayNames[new Date().getDay()];
            todayDayBadge.textContent = todayName;
        }

        // Format and set the date
        const todayDateDisplay = document.getElementById('todayDateDisplay');
        if (todayDateDisplay) {
            todayDateDisplay.textContent = weekInfo.displayDate.split(', ').slice(1).join(', '); // Remove day name
        }

        // ENHANCED: Set dynamic week cycle badge
        const weekCycleBadge = document.getElementById('weekCycleBadge');
        if (weekCycleBadge) {
            weekCycleBadge.textContent = `${weekInfo.weekName} - ${weekInfo.cycleDescription}`;
        }
        
        // Set the menu items based on day and week cycle
        let breakfast, lunch, dinner;
        
        if (isWeek1or3) {
            // Week 1 & 3 menu
            switch(dayOfWeek) {
                case 1: // Monday
                    breakfast = { name: 'Chicken Loaf with Energen', ingredients: 'Chicken Loaf, Energen, Water' };
                    lunch = { name: 'Fried Fish', ingredients: 'Fish, Oil, Salt' };
                    dinner = { name: 'Ginisang Cabbage', ingredients: 'Cabbage, Garlic, Onion, Oil, Salt' };
                    break;
                case 2: // Tuesday
                    breakfast = { name: 'Odong with Sardines', ingredients: 'Odong Noodles, Sardines, Water' };
                    lunch = { name: 'Fried Chicken', ingredients: 'Chicken, Oil, Salt, Pepper' };
                    dinner = { name: 'Baguio Beans', ingredients: 'Baguio Beans, Garlic, Onion, Oil, Salt' };
                    break;
                case 3: // Wednesday
                    breakfast = { name: 'Hotdogs', ingredients: 'Hotdogs, Oil' };
                    lunch = { name: 'Porkchop Guisado', ingredients: 'Porkchop, Garlic, Onion, Oil, Salt' };
                    dinner = { name: 'Eggplant with Eggs', ingredients: 'Eggplant, Eggs, Garlic, Onion, Oil, Salt' };
                    break;
                case 4: // Thursday
                    breakfast = { name: 'Boiled Eggs with Energen', ingredients: 'Eggs, Energen, Water' };
                    lunch = { name: 'Groundpork', ingredients: 'Ground Pork, Garlic, Onion, Oil, Salt' };
                    dinner = { name: 'Chopsuey', ingredients: 'Mixed Vegetables, Garlic, Onion, Oil, Salt' };
                    break;
                case 5: // Friday
                    breakfast = { name: 'Ham', ingredients: 'Ham, Oil' };
                    lunch = { name: 'Fried Chicken', ingredients: 'Chicken, Oil, Salt, Pepper' };
                    dinner = { name: 'Monggo Beans', ingredients: 'Monggo Beans, Garlic, Onion, Oil, Salt' };
                    break;
                case 6: // Saturday
                    breakfast = { name: 'Sardines with Eggs', ingredients: 'Sardines, Eggs, Oil' };
                    lunch = { name: 'Burger Steak', ingredients: 'Burger Patty, Garlic, Onion, Oil, Salt' };
                    dinner = { name: 'Utan Bisaya with Buwad', ingredients: 'Mixed Vegetables, Buwad, Garlic, Onion, Oil, Salt' };
                    break;
                case 0: // Sunday
                    breakfast = { name: 'Tomato with Eggs', ingredients: 'Tomatoes, Eggs, Garlic, Onion, Oil, Salt' };
                    lunch = { name: 'Fried Fish', ingredients: 'Fish, Oil, Salt' };
                    dinner = { name: 'Sari-Sari', ingredients: 'Mixed Vegetables, Garlic, Onion, Oil, Salt' };
                    break;
            }
        } else {
            // Week 2 & 4 menu
            switch(dayOfWeek) {
                case 1: // Monday
                    breakfast = { name: 'Chorizo', ingredients: 'Chorizo, Oil' };
                    lunch = { name: 'Chicken Adobo', ingredients: 'Chicken, Soy Sauce, Vinegar, Garlic, Onion' };
                    dinner = { name: 'String Beans Guisado', ingredients: 'String Beans, Garlic, Onion, Oil, Salt' };
                    break;
                case 2: // Tuesday
                    breakfast = { name: 'Scrambled Eggs with Energen', ingredients: 'Eggs, Energen, Water' };
                    lunch = { name: 'Fried Fish', ingredients: 'Fish, Oil, Salt' };
                    dinner = { name: 'Talong with Eggs', ingredients: 'Eggplant, Eggs, Garlic, Onion, Oil, Salt' };
                    break;
                case 3: // Wednesday
                    breakfast = { name: 'Sardines with Eggs', ingredients: 'Sardines, Eggs, Oil' };
                    lunch = { name: 'Groundpork', ingredients: 'Ground Pork, Garlic, Onion, Oil, Salt' };
                    dinner = { name: 'Tinun-ang Kalabasa with Buwad', ingredients: 'Kalabasa, Buwad, Garlic, Onion, Oil, Salt' };
                    break;
                case 4: // Thursday
                    breakfast = { name: 'Luncheon Meat', ingredients: 'Luncheon Meat, Oil' };
                    lunch = { name: 'Fried Chicken', ingredients: 'Chicken, Oil, Salt, Pepper' };
                    dinner = { name: 'Chopsuey', ingredients: 'Mixed Vegetables, Garlic, Onion, Oil, Salt' };
                    break;
                case 5: // Friday
                    breakfast = { name: 'Sotanghon Guisado', ingredients: 'Sotanghon, Garlic, Onion, Oil, Salt' };
                    lunch = { name: 'Pork Menudo', ingredients: 'Pork, Carrots, Potatoes, Garlic, Onion, Oil, Salt' };
                    dinner = { name: 'Monggo Beans', ingredients: 'Monggo Beans, Garlic, Onion, Oil, Salt' };
                    break;
                case 6: // Saturday
                    breakfast = { name: 'Hotdogs', ingredients: 'Hotdogs, Oil' };
                    lunch = { name: 'Meatballs', ingredients: 'Meatballs, Garlic, Onion, Oil, Salt' };
                    dinner = { name: 'Utan Bisaya with Buwad', ingredients: 'Mixed Vegetables, Buwad, Garlic, Onion, Oil, Salt' };
                    break;
                case 0: // Sunday
                    breakfast = { name: 'Ampalaya with Eggs with Energen', ingredients: 'Ampalaya, Eggs, Energen, Water' };
                    lunch = { name: 'Fried Fish', ingredients: 'Fish, Oil, Salt' };
                    dinner = { name: 'Pakbit', ingredients: 'Pakbit, Garlic, Onion, Oil, Salt' };
                    break;
            }
        }
        
        // Update the menu display
        document.getElementById('breakfastName').textContent = breakfast.name;
        document.getElementById('breakfastIngredients').textContent = breakfast.ingredients;
        
        document.getElementById('lunchName').textContent = lunch.name;
        document.getElementById('lunchIngredients').textContent = lunch.ingredients;
        
        document.getElementById('dinnerName').textContent = dinner.name;
        document.getElementById('dinnerIngredients').textContent = dinner.ingredients;
    }
    
    // Call the function to update the menu when the page loads
    updateTodayMenu();
    
    // Order filtering
    document.querySelectorAll('.dropdown-item[data-filter]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            
            rows.forEach(row => {
                const status = row.querySelector('.status-badge').textContent.toLowerCase();
                row.style.display = filter === 'all' || status === filter ? '' : 'none';
            });
        });
    });

    // Order actions
    function viewOrder(orderId) {
        // Implement view order functionality
        console.log('Viewing order:', orderId);
    }

    function completeOrder(orderId) {
        // Implement complete order functionality
        if (confirm('Are you sure you want to mark this order as completed?')) {
            console.log('Completing order:', orderId);
        }
    }
</script>
@endpush