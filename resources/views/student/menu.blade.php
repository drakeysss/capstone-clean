@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Simple Header Section with Time -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="simple-header-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="header-title">
                            <i class="bi bi-calendar-week me-2"></i>Weekly Menu
                        </h2>
                        <p class="header-subtitle">View this week's meal plan</p>
                    </div>
                    <div class="time-display">
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



    <!-- Simple Weekly Menu Section -->
    <div class="row">
        <div class="col-12">
            <div class="simple-menu-card">
                <div class="menu-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="menu-title">
                                <i class="bi bi-journal-text me-2"></i>
                                Weekly Menu Plan
                            </h4>
                            <p class="menu-subtitle">
                                Current week: <span id="currentWeekInfo">Loading...</span>
                            </p>
                        </div>
                        @if(!isset($waitingForCook) || !$waitingForCook)
                        <div>
                            <label for="weekCycleSelect" class="form-label fw-bold mb-1">Week Cycle:</label>
                            <select id="weekCycleSelect" class="simple-select">
                                <option value="1">Week 1 & 3</option>
                                <option value="2">Week 2 & 4</option>
                            </select>
                        </div>
                        @endif
                    </div>
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
                            <table class="table table-bordered simple-menu-table" id="week1Table">
                                <thead class="simple-table-header">
                                    <tr>
                                        <th width="15%">
                                            <i class="bi bi-calendar-day me-2"></i>Day
                                        </th>
                                        <th width="28%" class="breakfast-header">
                                            <i class="bi bi-sunrise me-2"></i>Breakfast
                                            <div class="meal-time">7:00 - 9:00 AM</div>
                                        </th>
                                        <th width="28%" class="lunch-header">
                                            <i class="bi bi-sun me-2"></i>Lunch
                                            <div class="meal-time">12:00 - 2:00 PM</div>
                                        </th>
                                        <th width="28%" class="dinner-header">
                                            <i class="bi bi-moon me-2"></i>Dinner
                                            <div class="meal-time">6:00 - 8:00 PM</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="menuTableBody">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>

                            <!-- Week 2 & 4 Table -->
                            <table class="table table-bordered simple-menu-table" id="week2Table" style="display:none;">
                                <thead class="simple-table-header">
                                    <tr>
                                        <th width="15%">
                                            <i class="bi bi-calendar-day me-2"></i>Day
                                        </th>
                                        <th width="28%" class="breakfast-header">
                                            <i class="bi bi-sunrise me-2"></i>Breakfast
                                            <div class="meal-time">7:00 - 9:00 AM</div>
                                        </th>
                                        <th width="28%" class="lunch-header">
                                            <i class="bi bi-sun me-2"></i>Lunch
                                            <div class="meal-time">12:00 - 2:00 PM</div>
                                        </th>
                                        <th width="28%" class="dinner-header">
                                            <i class="bi bi-moon me-2"></i>Dinner
                                            <div class="meal-time">6:00 - 8:00 PM</div>
                                        </th>
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

        // UNIFIED: Use consistent highlighting system
        const highlighting = getMenuHighlighting(day, selectedWeekCycle);
        const todayClass = highlighting.todayClass;
        const todayBadge = highlighting.todayBadge;

        const row = `
            <tr class="simple-menu-row ${todayClass}" data-day="${day}">
                <td class="day-cell">
                    <div class="day-name">${dayNames[index]}</div>
                    ${todayBadge}
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
/* Simple Header Styles */
.simple-header-card {
    background: #ff9933;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 1rem;
}

/* Responsive Header Styles */
@media (max-width: 768px) {
    .simple-header-card {
        padding: 0.75rem !important;
        margin: 0 !important;
    }

    .simple-header-card .d-flex {
        flex-direction: column !important;
        gap: 0.75rem !important;
        text-align: center !important;
    }

    .time-display {
        text-align: center !important;
        width: 100% !important;
    }

    .menu-card-header .d-flex {
        flex-direction: column !important;
        gap: 0.75rem !important;
        text-align: center !important;
    }

    .simple-select {
        width: 100% !important;
        max-width: 200px !important;
        margin: 0 auto !important;
    }
}

.header-title {
    color: white;
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.header-subtitle {
    color: white;
    font-size: 1rem;
    margin-bottom: 0;
    opacity: 0.9;
}

/* Time Display Styles */
.time-display {
    text-align: right;
    color: white;
}

.current-time {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.current-date {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Simple Menu Card Styles */
.simple-menu-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: none;
}

.menu-card-header {
    background: linear-gradient(135deg, #22bbea, #1a9bd1);
    padding: 1.5rem;
    border-bottom: 3px solid #ff9933;
}

.menu-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.menu-subtitle {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.simple-select {
    background: white;
    border: 2px solid #22bbea;
    border-radius: 5px;
    padding: 0.5rem;
    font-weight: 500;
    color: #2c3e50;
}

.simple-select:focus {
    outline: none;
    border-color: #ff9933;
}

/* Simple Table Styles */
.simple-menu-table {
    margin: 0;
}

.simple-table-header th {
    background: #2c3e50;
    color: white;
    padding: 1rem;
    text-align: center;
    font-weight: 600;
    border: 1px solid #dee2e6;
}

.meal-time {
    display: block;
    font-size: 0.75rem;
    opacity: 0.8;
    margin-top: 0.25rem;
}

.breakfast-header {
    background: #ff9933 !important;
}

.lunch-header {
    background: #22bbea !important;
}

.dinner-header {
    background: #6c757d !important;
}

/* Simple Table Body */
.simple-menu-row {
    border-bottom: 1px solid #e9ecef;
}

.simple-menu-row:hover {
    background-color: #f8f9fa;
}

/* UNIFIED: Current Day and Week Highlighting */
.current-day-row {
    background: linear-gradient(90deg, rgba(255, 153, 51, 0.15) 0%, rgba(34, 187, 234, 0.15) 100%) !important;
    border-left: 4px solid #ff9933;
    animation: currentDayPulse 2s ease-in-out infinite;
}

.current-day-row:hover {
    background: linear-gradient(90deg, rgba(255, 153, 51, 0.25) 0%, rgba(34, 187, 234, 0.25) 100%) !important;
}

/* Current Week Highlighting */
.current-week-row {
    background: linear-gradient(90deg, rgba(34, 187, 234, 0.08) 0%, rgba(255, 153, 51, 0.08) 100%) !important;
    border-left: 2px solid #22bbea;
}

.current-week-row:hover {
    background: linear-gradient(90deg, rgba(34, 187, 234, 0.15) 0%, rgba(255, 153, 51, 0.15) 100%) !important;
}

@keyframes currentDayPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(255, 153, 51, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(255, 153, 51, 0);
    }
}

/* Day Cell Styles */
.day-cell {
    padding: 1rem;
    text-align: center;
    background: #f8f9fa;
    border-right: 1px solid #e9ecef;
    vertical-align: middle;
}

.day-name {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

/* Date display removed - menu is cycle-based */

/* UNIFIED: Badge System */
.today-badge {
    background: #ff9933;
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    margin: 0.25rem 0;
    display: inline-block;
    animation: badgePulse 2s ease-in-out infinite;
}

.week-badge {
    background: #22bbea;
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    margin: 0.25rem 0;
    display: inline-block;
}

@keyframes badgePulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Meal Cell Styles */
.meal-cell {
    padding: 1rem;
    vertical-align: top;
}

.meal-item {
    text-align: center;
}

.meal-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.meal-ingredients {
    font-size: 0.8rem;
    color: #6c757d;
    line-height: 1.3;
}

/* Simple Responsive Design */
@media (max-width: 768px) {
    .header-title {
        font-size: 1.5rem;
    }

    .current-time {
        font-size: 1.2rem;
    }

    .simple-table-header th {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }

    .meal-cell {
        padding: 0.75rem 0.5rem;
    }

    .meal-name {
        font-size: 0.85rem;
    }

    .meal-ingredients {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .simple-header-card {
        padding: 0.75rem;
        margin: 0 -0.25rem 1rem -0.25rem;
    }

    .header-title {
        font-size: 1.25rem;
    }

    .current-time {
        font-size: 1rem;
    }

    .menu-card-header {
        padding: 0.75rem;
    }

    .simple-table-header th {
        padding: 0.5rem 0.25rem;
        font-size: 0.8rem;
    }

    .day-cell,
    .meal-cell {
        padding: 0.5rem 0.25rem;
    }

    .day-name {
        font-size: 0.8rem;
    }

    .meal-name {
        font-size: 0.8rem;
    }

    .meal-ingredients {
        font-size: 0.7rem;
        display: none; /* Hide ingredients on very small screens */
    }

    .today-badge,
    .week-badge {
        font-size: 0.6rem;
        padding: 0.15rem 0.3rem;
    }

    /* Stack table on very small screens */
    .simple-menu-table {
        font-size: 0.8rem;
    }

    .simple-menu-table th:not(:first-child),
    .simple-menu-table td:not(:first-child) {
        min-width: 80px;
    }
}
</style>
@endpush

@endsection
