@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Enhanced Header Section - Match Cook Menu -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <div>
                        <h3 class="mb-1 fw-bold">
                            <i class="bi bi-calendar-week me-2"></i>Weekly Menu
                        </h3>
                        <p class="mb-0 opacity-75">View this week's meal plan</p>
                    </div>
                    <div class="text-end">
                        <div class="current-time" id="currentTime">
                            <i class="bi bi-clock me-2"></i>
                            <span id="timeDisplay">Loading...</span>
                        </div>
                        <div class="current-date" id="currentDate">
                            <span id="dateDisplay">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Weekly Menu Section - Match Cook Menu -->
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="bi bi-journal-text me-2"></i>
                            Weekly Menu Plan
                        </h4>
                        <p class="mb-0 text-muted">
                            Current week: <span id="currentWeekInfo">Loading...</span>
                        </p>
                    </div>
                    @if(!isset($waitingForCook) || !$waitingForCook)
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted me-2">View Menu for:</span>
                        <select id="weekCycleSelect" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="1">Week 1 & 3</option>
                            <option value="2">Week 2 & 4</option>
                        </select>
                        <small class="text-info ms-2" id="currentWeekIndicator">
                            <i class="bi bi-calendar-check"></i> Current: Week <span id="currentWeekNumber">1</span>
                        </small>
                    </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if(isset($waitingForCook) && $waitingForCook)
                        <!-- No Menu Available Message -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-calendar-x display-1 text-muted"></i>
                            </div>
                            <h4 class="text-muted">No Menu Available</h4>
                            <p class="text-muted">
                                The cook hasn't created a menu yet.<br>
                                Please wait for the cook to plan and send the weekly menu.
                            </p>
                            <div class="mt-4">
                                <div class="spinner-border text-primary me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="text-muted">Waiting for cook to create menu...</span>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-outline-primary" onclick="window.location.reload()">
                                    <i class="bi bi-arrow-clockwise"></i> Check Again
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <!-- Week 1 & 3 Table -->
                            <table class="table table-bordered week-table" id="week1Table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th width="28%">Breakfast</th>
                                        <th width="28%">Lunch</th>
                                        <th width="28%">Dinner</th>
                                    </tr>
                                </thead>
                                <tbody id="menuTableBody">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>

                            <!-- Week 2 & 4 Table -->
                            <table class="table table-bordered week-table" id="week2Table" style="display:none;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th width="28%">Breakfast</th>
                                        <th width="28%">Lunch</th>
                                        <th width="28%">Dinner</th>
                                    </tr>
                                </thead>
                                <tbody id="menuTableBody2">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(!isset($waitingForCook) || !$waitingForCook)
<script>
{!! \App\Services\WeekCycleService::getJavaScriptFunction() !!}

// Simple week cycle switching
document.getElementById('weekCycleSelect').addEventListener('change', function() {
    var week1Table = document.getElementById('week1Table');
    var week2Table = document.getElementById('week2Table');

    if (this.value == '1') {
        week1Table.style.display = '';
        week2Table.style.display = 'none';
    } else {
        week1Table.style.display = 'none';
        week2Table.style.display = '';
    }

    updateCurrentWeekInfo();
    loadMenuData(); // Reload menu data when week changes
});

// Load menu data from cook
function loadMenuData() {
    const weekCycle = document.getElementById('weekCycleSelect').value;

    fetch(`/student/menu/${weekCycle}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMenuTable(data.menu);
            }
        })
        .catch(error => {
            console.error('Error loading menu:', error);
        });
}

// Update menu table with data from cook
function updateMenuTable(menuData) {
    const tableBody = document.getElementById('menuTableBody');
    const tableBody2 = document.getElementById('menuTableBody2');

    // Clear existing content
    tableBody.innerHTML = '';
    tableBody2.innerHTML = '';

    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    // ENHANCED: Get current week and day info with dynamic highlighting
    const weekInfo = getCurrentWeekCycle();
    const currentWeekCycle = weekInfo.weekCycle;
    const selectedWeekCycle = parseInt(document.getElementById('weekCycleSelect').value);
    const currentDayName = weekInfo.currentDayName;
    const isCurrentWeek = selectedWeekCycle === currentWeekCycle;

    days.forEach((day, index) => {
        const dayMeals = menuData[day] || {};

        // Use consistent highlighting system like cook menu
        const isToday = day === currentDayName.toLowerCase() && isCurrentWeek;
        const todayClass = isToday ? 'today table-warning current-day' : (isCurrentWeek ? 'current-week-row' : '');

        const row = `
            <tr class="${todayClass}" data-day="${day}">
                <td class="day-cell">
                    ${dayNames[index]}
                </td>
                <td class="meal-cell">
                    <div class="meal-item">
                        <div class="meal-name">${dayMeals.breakfast?.name || 'No breakfast planned'}</div>
                        <div class="meal-ingredients">${dayMeals.breakfast?.ingredients || 'No ingredients listed'}</div>
                    </div>
                </td>
                <td class="meal-cell">
                    <div class="meal-item">
                        <div class="meal-name">${dayMeals.lunch?.name || 'No lunch planned'}</div>
                        <div class="meal-ingredients">${dayMeals.lunch?.ingredients || 'No ingredients listed'}</div>
                    </div>
                </td>
                <td class="meal-cell">
                    <div class="meal-item">
                        <div class="meal-name">${dayMeals.dinner?.name || 'No dinner planned'}</div>
                        <div class="meal-ingredients">${dayMeals.dinner?.ingredients || 'No ingredients listed'}</div>
                    </div>
                </td>
            </tr>
        `;

        tableBody.innerHTML += row;
        tableBody2.innerHTML += row;
    });
}

// Note: Date display removed - menu is cycle-based, not date-based
// The menu repeats every 2 weeks (Week 1 & 3, Week 2 & 4)

// UNIFIED: Update current time and date display
function updateTimeDisplay() {
    const weekInfo = getCurrentWeekCycle();

    // Update time
    document.getElementById('timeDisplay').textContent = weekInfo.timeString;

    // Update date
    document.getElementById('dateDisplay').textContent = weekInfo.displayDate;

    // Update current week info
    updateCurrentWeekInfo();
}

// ENHANCED: Update current week info with dynamic naming
function updateCurrentWeekInfo() {
    const weekInfo = getCurrentWeekCycle();
    const selectedWeekCycle = parseInt(document.getElementById('weekCycleSelect').value);
    const isCurrentWeek = selectedWeekCycle === weekInfo.weekCycle;

    // Dynamic week info text
    const weekInfoText = selectedWeekCycle === 1 ? 'Week 1 & 3 (Odd weeks)' : 'Week 2 & 4 (Even weeks)';

    // Enhanced status with dynamic naming
    let statusText = '';
    if (isCurrentWeek) {
        statusText = ` - ${weekInfo.weekName} (Current Week)`;
    } else {
        statusText = ` - Viewing Week ${selectedWeekCycle}`;
    }

    const currentWeekInfoElement = document.getElementById('currentWeekInfo');
    if (currentWeekInfoElement) {
        currentWeekInfoElement.innerHTML = `${weekInfoText}${statusText}`;

        // Add visual indicator
        if (isCurrentWeek) {
            currentWeekInfoElement.className = 'text-success fw-bold';
        } else {
            currentWeekInfoElement.className = 'text-muted';
        }
    }
}

// Initialize everything
document.addEventListener('DOMContentLoaded', function() {
    // UNIFIED: Set current week cycle as default
    const weekInfo = getCurrentWeekCycle();
    document.getElementById('weekCycleSelect').value = weekInfo.weekCycle;

    // Start time display
    updateTimeDisplay();
    setInterval(updateTimeDisplay, 1000); // Update every second

    // Initialize week info
    updateCurrentWeekInfo();

    // Load menu data
    loadMenuData();

    // Reload when week cycle changes and update week info
    document.getElementById('weekCycleSelect').addEventListener('change', function() {
        updateCurrentWeekInfo();
        loadMenuData();
    });
});
</script>
@endif

@push('styles')
<style>
/* Enhanced Header Styles - Match Cook Menu */
.card-header {
    background: linear-gradient(135deg, #22bbea, #1a9bd1) !important;
    border-bottom: none;
}

.card-header h3 {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.card-header p {
    color: white;
    font-size: 0.9rem;
    margin-bottom: 0;
    opacity: 0.85;
}

/* Time Display Styles */
.current-time {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
    color: white;
}

.current-date {
    font-size: 0.8rem;
    opacity: 0.85;
    color: white;
}

/* Main Card Styles - Match Cook Menu */
.main-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.main-card .card-header {
    background: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.25rem;
}

.main-card .card-header h4 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.main-card .card-header p {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.875rem;
}

.form-select {
    background: white;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    color: #495057;
}

.form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Table Styles - Match Cook Menu */
.week-table {
    margin: 0;
    border: 1px solid #dee2e6;
}

.week-table thead th {
    background-color: #f8f9fa;
    color: #495057;
    padding: 0.75rem;
    text-align: center;
    font-weight: 600;
    border: 1px solid #dee2e6;
    font-size: 0.875rem;
}

.week-table tbody td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    vertical-align: top;
}

.week-table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Current Day and Week Highlighting - Match Cook Menu */
.today,
.current-day {
    background-color: #fff3cd !important;
    border-left: 3px solid #6c757d !important;
}

.today:hover,
.current-day:hover {
    background-color: #e9ecef !important;
}

.current-week-row {
    background-color: #f8f9fa !important;
}

.current-week-row:hover {
    background-color: #e9ecef !important;
}

/* Day Cell Styles - Match Cook Menu */
.day-cell {
    padding: 0.75rem;
    text-align: center;
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    vertical-align: middle;
    font-weight: 600;
    color: #495057;
}

/* Meal Cell Styles - Match Cook Menu */
.meal-cell {
    padding: 0.75rem;
    vertical-align: top;
}

.meal-item {
    position: relative;
    padding: 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.meal-item:hover {
    background-color: #f8f9fa;
    border-radius: 4px;
}

.meal-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}

.meal-ingredients {
    font-size: 0.75rem;
    color: #6c757d;
    line-height: 1.3;
}

/* Current Day Meal Items - Match Cook Menu */
.today .meal-item,
.current-day .meal-item {
    border: 1px solid #dee2e6;
    background-color: white;
}

.today .meal-item:hover,
.current-day .meal-item:hover {
    border-color: #6c757d;
    background-color: #f8f9fa;
}

/* Responsive Design - Match Cook Menu */
@media (max-width: 768px) {
    .card-header h3 {
        font-size: 1.25rem;
    }

    .current-time {
        font-size: 0.875rem;
    }

    .week-table thead th {
        padding: 0.5rem 0.25rem;
        font-size: 0.75rem;
    }

    .meal-cell,
    .day-cell {
        padding: 0.5rem 0.25rem;
    }

    .meal-name {
        font-size: 0.75rem;
    }

    .meal-ingredients {
        font-size: 0.7rem;
    }

    .card-header .d-flex {
        flex-direction: column !important;
        gap: 0.75rem !important;
        text-align: center !important;
    }

    .form-select {
        width: 100% !important;
        max-width: 200px !important;
        margin: 0 auto !important;
    }
}

@media (max-width: 576px) {
    .card-header h3 {
        font-size: 1.125rem;
    }

    .current-time {
        font-size: 0.75rem;
    }

    .week-table thead th {
        padding: 0.375rem 0.125rem;
        font-size: 0.7rem;
    }

    .day-cell,
    .meal-cell {
        padding: 0.375rem 0.125rem;
    }

    .meal-name {
        font-size: 0.7rem;
    }

    .meal-ingredients {
        font-size: 0.65rem;
        display: none; /* Hide ingredients on very small screens */
    }

    .week-table {
        font-size: 0.75rem;
    }
}
</style>
@endpush

@endsection
