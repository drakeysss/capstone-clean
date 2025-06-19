@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Enhanced Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22bbea, #1a9bd1);">
                    <div>
                        <h3 class="mb-1 fw-bold">
                            <i class="bi bi-calendar-week me-2"></i>Daily & Weekly Menu
                        </h3>
                        <p class="mb-0 opacity-75">View today's menu and upcoming meals for the week</p>
                    </div>
                    <div class="text-end">
                        <span id="currentDateTime" class="fs-6"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Menu Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Today's Menu <span class="badge bg-primary ms-2" id="todayDayBadge">{{ now()->format('l') }}</span></h5>
                    <div>
                        <span class="text-muted me-2" id="weekOfMonthDisplay">Week {{ $weekOfMonth ?? now()->weekOfMonth }} of {{ now()->format('F') }}:</span>
                        <span class="badge bg-info" id="weekCycleBadge">Week {{ $weekCycle ?? 1 }}</span>
                        <span class="badge bg-success ms-2" id="currentWeekIndicator">
                            <i class="bi bi-calendar-check"></i> Current Week
                        </span>
                     
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($waitingForCook) && $waitingForCook)
                        <!-- No Menu Available Message -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-calendar-x display-1 text-muted"></i>
                            </div>
                            <h4 class="text-muted">No Menu Available</h4>
                            <p class="text-muted">
                                The cook/admin hasn't created a menu for today yet.<br>
                                Kitchen team is waiting for the cook to plan and send the menu.
                            </p>
                            <div class="mt-4">
                                <div class="spinner-border text-warning me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="text-muted">Waiting for cook to create menu...</span>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-outline-primary" onclick="window.location.reload()">
                                    <i class="bi bi-arrow-clockwise"></i> Check Again
                                </button>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i>
                                    This page will automatically check for updates every 30 seconds
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <!-- Breakfast -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light text-center">
                                        <h6 class="mb-0">Breakfast</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="meal-item">
                                            <div class="fw-bold mb-2" id="breakfastName">Loading...</div>
                                            <small class="text-muted" id="breakfastIngredients">Loading ingredients...</small>
                                        </div>
                                        <div class="meal-time mt-2">
                                            <span class="badge bg-secondary">6:00 AM - 8:00 AM</span>
                                        </div>
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
                                    <div class="meal-item">
                                        <div class="fw-bold mb-2" id="lunchName">Loading...</div>
                                        <small class="text-muted" id="lunchIngredients">Loading ingredients...</small>
                                    </div>
                                    <div class="meal-time mt-2">
                                        <span class="badge bg-secondary">11:30 AM - 1:30 PM</span>
                                    </div>
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
                                    <div class="meal-item">
                                        <div class="fw-bold mb-2" id="dinnerName">Loading...</div>
                                        <small class="text-muted" id="dinnerIngredients">Loading ingredients...</small>
                                    </div>
                                    <div class="meal-time mt-2">
                                        <span class="badge bg-secondary">5:30 PM - 7:30 PM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Menu Section -->
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Weekly Menu</h5>
                    @if(!isset($waitingForCook) || !$waitingForCook)
                    <div>
                        <span class="text-muted me-2">View Menu for:</span>
                        <select id="weekCycleSelect" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="1">Week 1 & 3</option>
                            <option value="2">Week 2 & 4</option>
                        </select>
                        <small class="text-info ms-2" id="currentWeekIndicator">
                            <i class="bi bi-calendar-check"></i> Current: Week {{ $weekOfMonth ?? now()->weekOfMonth }}
                        </small>
                    </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if(isset($waitingForCook) && $waitingForCook)
                        <!-- No Weekly Menu Available -->
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-calendar-week display-4 text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Weekly Menu Available</h5>
                            <p class="text-muted">
                                Kitchen team is waiting for cook/admin to create the weekly menu plan.<br>
                                Once the menu is ready, you'll be able to see meal preparation tasks.
                            </p>
                            <div class="mt-3">
                                <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <small class="text-muted">Checking for menu updates...</small>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th width="28%">Breakfast</th>
                                        <th width="28%">Lunch</th>
                                        <th width="28%">Dinner</th>
                                    </tr>
                                </thead>
                                <tbody id="weeklyMenuTable">
                                    <!-- Weekly menu will be loaded dynamically -->
                                    @php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                    @endphp
                                    @foreach($days as $day)
                                    <tr data-day="{{ strtolower($day) }}" class="menu-row">
                                        <td class="fw-bold">{{ $day }}</td>
                                        <td>
                                            <div class="meal-item" data-meal-type="breakfast">
                                                <div class="fw-bold meal-name">Loading...</div>
                                                <small class="text-muted meal-ingredients">Loading ingredients...</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="meal-item" data-meal-type="lunch">
                                                <div class="fw-bold meal-name">Loading...</div>
                                                <small class="text-muted meal-ingredients">Loading ingredients...</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="meal-item" data-meal-type="dinner">
                                                <div class="fw-bold meal-name">Loading...</div>
                                                <small class="text-muted meal-ingredients">Loading ingredients...</small>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

   
@endsection

@push('styles')
<style>
    .meal-item {
        margin-bottom: 10px;
    }
    
    .meal-time {
        margin-top: 10px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    /* UNIFIED: Current Day and Week Highlighting */
    .menu-row.today,
    tr.today {
        background: rgba(255, 153, 51, 0.15) !important;
        border-left: 4px solid #ff9933;
        animation: currentDayPulse 2s ease-in-out infinite;
    }

    .menu-row.today:hover,
    tr.today:hover {
        background: rgba(255, 153, 51, 0.25) !important;
    }

    .current-week-row {
        background: rgba(34, 187, 234, 0.08) !important;
        border-left: 2px solid #22bbea;
    }

    .current-week-row:hover {
        background: rgba(34, 187, 234, 0.15) !important;
    }

    /* UNIFIED: Badge System */
    .today-indicator,
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

    @keyframes currentDayPulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 153, 51, 0.4);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(255, 153, 51, 0);
        }
    }

    .card-header.bg-light {
        background-color: #f8f9fa !important;
    }

    #refreshIcon {
        transition: transform 0.3s ease;
    }

    #refreshIcon.fa-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .meal-item {
        transition: all 0.3s ease;
    }

    .meal-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
        border-radius: 4px;
        padding: 4px;
    }
    
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    {!! \App\Services\WeekCycleService::getJavaScriptFunction() !!}

    // Real-time date and time display
    function updateDateTime() {
        const now = new Date();

        // Format date and time separately for better control
        const dateOptions = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };

        const dateString = now.toLocaleDateString('en-US', dateOptions);
        const timeString = now.toLocaleTimeString('en-US', timeOptions);

        // Update main date/time display
        const currentDateTimeElement = document.getElementById('currentDateTime');
        if (currentDateTimeElement) {
            currentDateTimeElement.innerHTML = `${dateString}<br><small>${timeString}</small>`;
        }

        // Update today's day badge in real-time
        const todayDayBadge = document.getElementById('todayDayBadge');
        if (todayDayBadge) {
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            todayDayBadge.textContent = dayNames[now.getDay()];
        }

        // Update week information
        updateWeekInfo();
    }

    // ENHANCED: Update week cycle information with dynamic naming
    function updateWeekInfo() {
        try {
            const weekInfo = getCurrentWeekCycle();
            const weekOfMonth = weekInfo.weekOfMonth;
            const currentWeekCycle = weekInfo.weekCycle;
            const weekCycleSelect = document.getElementById('weekCycleSelect');
            const selectedWeekCycle = weekCycleSelect ? parseInt(weekCycleSelect.value) : currentWeekCycle;

            console.log('Updating week info:', {
                weekOfMonth,
                currentWeekCycle,
                selectedWeekCycle,
                weekName: weekInfo.weekName,
                cycleShort: weekInfo.cycleShort
            });

            // Update week cycle badge with dynamic naming
            const weekCycleBadge = document.getElementById('weekCycleBadge');
            if (weekCycleBadge) {
                weekCycleBadge.textContent = weekInfo.cycleShort;
            }

            // Update week of month display with dynamic naming
            const weekOfMonthDisplay = document.getElementById('weekOfMonthDisplay');
            if (weekOfMonthDisplay) {
                weekOfMonthDisplay.textContent = `${weekInfo.weekName}:`;
            }

            // Update current week indicator - show only when viewing current week
            const currentWeekIndicator = document.getElementById('currentWeekIndicator');
            if (currentWeekIndicator) {
                if (selectedWeekCycle === currentWeekCycle) {
                    currentWeekIndicator.innerHTML = `<i class="bi bi-calendar-check"></i> Current Week`;
                    currentWeekIndicator.style.display = 'inline-block';
                    currentWeekIndicator.className = 'badge bg-success ms-2';
                } else {
                    currentWeekIndicator.innerHTML = `<i class="bi bi-calendar"></i> Viewing Week ${selectedWeekCycle}`;
                    currentWeekIndicator.style.display = 'inline-block';
                    currentWeekIndicator.className = 'badge bg-secondary ms-2';
                }
            }

            // Auto-select current week cycle if not manually changed
            if (weekCycleSelect && !weekCycleSelect.hasAttribute('data-manually-changed')) {
                weekCycleSelect.value = currentWeekCycle;
            }
        } catch (error) {
            console.error('Error updating week info:', error);
        }
    }

    updateDateTime();
    setInterval(updateDateTime, 1000); // Update every second for real-time display
    
    // FIXED: Load menu data with proper authentication checks
    function loadMenuData() {
        // Check if we have a week cycle selector (only available when not waiting for cook)
        const weekCycleSelect = document.getElementById('weekCycleSelect');
        if (!weekCycleSelect) {
            // We're waiting for cook, no need to load menu data
            return;
        }

        // Check if CSRF token exists (indicates user is authenticated)
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('No CSRF token found, user may not be authenticated');
            setFallbackContent();
            return;
        }

        const weekCycle = weekCycleSelect.value;

        // Show loading indicator
        const refreshIcon = document.getElementById('refreshIcon');
        if (refreshIcon) {
            refreshIcon.classList.add('fa-spin');
        }

        console.log(`Fetching menu data for week cycle: ${weekCycle}`);

        // Add timeout to prevent hanging and stop loading immediately
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
            console.log('Request timed out, setting fallback content');
            setFallbackContent();
        }, 5000); // 5 second timeout

        fetch(`/kitchen/menu/${weekCycle}`, {
            signal: controller.signal,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => {
                clearTimeout(timeoutId);
                console.log('API Response status:', response.status);

                // Handle authentication errors
                if (response.status === 401 || response.status === 403) {
                    console.error('Authentication error, redirecting to login');
                    window.location.href = '/login';
                    return;
                }

                // Handle other HTTP errors
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Response is not JSON, likely authentication redirect');
                    setFallbackContent();
                    return Promise.reject(new Error('Invalid response format'));
                }

                return response.json();
            })
            .then(data => {
                console.log('Kitchen menu data received:', data);

                if (data.success && data.menu) {
                    console.log('Processing menu data...');
                    console.log('Menu has', Object.keys(data.menu).length, 'days');

                    if (Object.keys(data.menu).length === 0) {
                        console.log('Menu is empty, setting fallback content');
                        setFallbackContent();
                    } else {
                        updateMenuTable(data.menu);
                        updateTodayMenu(data.menu);
                        console.log('Menu update completed successfully');
                    }
                } else if (data.waitingForCook) {
                    console.log('Cook hasn\'t created menu yet, reloading...');
                    window.location.reload();
                } else {
                    console.error('API returned unsuccessful response:', data);
                    showToast('Failed to load menu data: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error loading menu:', error);

                // Don't show toast for authentication errors
                if (!error.message.includes('Invalid response format')) {
                    showToast('An error occurred while loading menu: ' + error.message, 'error');
                }

                // Set fallback content for all loading elements
                setFallbackContent();
            })
            .finally(() => {
                // Hide loading indicator
                const refreshIcon = document.getElementById('refreshIcon');
                if (refreshIcon) {
                    refreshIcon.classList.remove('fa-spin');
                }
            });
    }
    
    // FIXED: Update menu table with proper data handling
    function updateMenuTable(menuData) {
        console.log('=== UPDATING MENU TABLE ===');
        console.log('Menu data received:', menuData);

        // Stop loading indicators immediately
        document.querySelectorAll('.meal-name').forEach(el => {
            if (el.textContent === 'Loading...') {
                el.textContent = 'No meal planned';
            }
        });
        document.querySelectorAll('.meal-ingredients').forEach(el => {
            if (el.textContent === 'Loading ingredients...') {
                el.textContent = 'Waiting for cook to plan';
            }
        });

        // Process each day in the menu data
        Object.keys(menuData).forEach(day => {
            console.log(`Processing day: ${day}`);
            const dayData = menuData[day];

            ['breakfast', 'lunch', 'dinner'].forEach(mealType => {
                const meal = dayData[mealType];
                const selector = `tr[data-day="${day}"] .meal-item[data-meal-type="${mealType}"]`;
                const mealElement = document.querySelector(selector);

                if (mealElement) {
                    const nameElement = mealElement.querySelector('.meal-name');
                    const ingredientsElement = mealElement.querySelector('.meal-ingredients');

                    if (nameElement && ingredientsElement) {
                        if (meal && meal.name) {
                            nameElement.textContent = meal.name;
                            // Handle ingredients properly - could be string or array
                            let ingredients = meal.ingredients;
                            if (Array.isArray(ingredients)) {
                                ingredients = ingredients.join(', ');
                            }
                            ingredientsElement.textContent = ingredients || 'No ingredients listed';
                        } else {
                            nameElement.textContent = 'No meal planned';
                            ingredientsElement.textContent = 'Waiting for cook to plan';
                        }
                    }
                }
            });
        });

        // Also update days that don't have data
        const allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        allDays.forEach(day => {
            if (!menuData[day]) {
                console.log(`\n--- No data for ${day}, setting empty state ---`);
                ['breakfast', 'lunch', 'dinner'].forEach(mealType => {
                    const selector = `tr[data-day="${day}"] .meal-item[data-meal-type="${mealType}"]`;
                    const mealElement = document.querySelector(selector);

                    if (mealElement) {
                        const nameElement = mealElement.querySelector('.meal-name');
                        const ingredientsElement = mealElement.querySelector('.meal-ingredients');

                        if (nameElement && ingredientsElement) {
                            nameElement.textContent = 'No meal planned';
                            ingredientsElement.textContent = 'Waiting for cook to plan';
                        }
                    }
                });
            }
        });
        console.log('=== MENU TABLE UPDATE COMPLETE ===');
    }
    
    // FIXED: Update today's menu with proper data handling
    function updateTodayMenu(menuData) {
        console.log('=== UPDATING TODAY\'S MENU ===');

        // Stop loading indicators immediately
        ['breakfast', 'lunch', 'dinner'].forEach(mealType => {
            const nameElement = document.getElementById(`${mealType}Name`);
            const ingredientsElement = document.getElementById(`${mealType}Ingredients`);

            if (nameElement && nameElement.textContent === 'Loading...') {
                nameElement.textContent = 'No meal planned';
            }
            if (ingredientsElement && ingredientsElement.textContent === 'Loading ingredients...') {
                ingredientsElement.textContent = 'Waiting for cook to plan';
            }
        });

        const now = new Date();
        const dayOfWeek = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
        const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        const today = dayNames[dayOfWeek];

        console.log('Today is:', today);

        if (menuData && menuData[today]) {
            console.log(`Found data for ${today}`);

            ['breakfast', 'lunch', 'dinner'].forEach(mealType => {
                const meal = menuData[today][mealType];
                const nameElement = document.getElementById(`${mealType}Name`);
                const ingredientsElement = document.getElementById(`${mealType}Ingredients`);

                if (nameElement && ingredientsElement) {
                    if (meal && meal.name) {
                        nameElement.textContent = meal.name;
                        // Handle ingredients properly - could be string or array
                        let ingredients = meal.ingredients;
                        if (Array.isArray(ingredients)) {
                            ingredients = ingredients.join(', ');
                        }
                        ingredientsElement.textContent = ingredients || 'No ingredients listed';
                    } else {
                        nameElement.textContent = 'No meal planned';
                        ingredientsElement.textContent = 'Waiting for cook to plan';
                    }
                }
            });
        } else {
            console.log(`No menu data for today (${today})`);
            // No data for today, ensure we show proper messages
            ['breakfast', 'lunch', 'dinner'].forEach(mealType => {
                const nameElement = document.getElementById(`${mealType}Name`);
                const ingredientsElement = document.getElementById(`${mealType}Ingredients`);

                if (nameElement && ingredientsElement) {
                    nameElement.textContent = 'No meal planned';
                    ingredientsElement.textContent = 'Waiting for cook to plan';
                }
            });
        }
        console.log('=== TODAY\'S MENU UPDATE COMPLETE ===');
    }
    
    // Update meal status
    function updateMealStatus(mealType) {
        const statuses = ['Not Started', 'In Progress', 'Completed'];
        const currentStatus = document.getElementById(`${mealType}Status`).textContent;
        const currentIndex = statuses.indexOf(currentStatus);
        const nextIndex = (currentIndex + 1) % statuses.length;
        
        // Update UI
        document.getElementById(`${mealType}Status`).textContent = statuses[nextIndex];
        document.getElementById(`${mealType}Progress`).style.width = `${(nextIndex / 2) * 100}%`;
        
        // Update progress bar color
        const progressBar = document.getElementById(`${mealType}Progress`);
        if (nextIndex === 0) {
            progressBar.className = 'progress-bar';
        } else if (nextIndex === 1) {
            progressBar.className = 'progress-bar bg-warning';
        } else {
            progressBar.className = 'progress-bar bg-success';
        }

        // Send status update to server
        fetch('/kitchen/menu/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                meal_type: mealType,
                status: statuses[nextIndex]
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Status updated successfully', 'success');
            } else {
                showToast('Failed to update status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.appendChild(toast);
        document.body.appendChild(container);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            container.remove();
        });
    }

    // FIXED: Set fallback content and stop all loading states
    function setFallbackContent() {
        console.log('Setting fallback content and stopping all loading states');

        // Stop ALL loading indicators immediately
        document.querySelectorAll('[id$="Name"]').forEach(el => {
            if (el.textContent === 'Loading...') {
                el.textContent = 'No meal planned';
            }
        });

        document.querySelectorAll('[id$="Ingredients"]').forEach(el => {
            if (el.textContent === 'Loading ingredients...') {
                el.textContent = 'Waiting for cook to plan';
            }
        });

        // Set fallback for today's menu
        ['breakfast', 'lunch', 'dinner'].forEach(mealType => {
            const nameElement = document.getElementById(`${mealType}Name`);
            const ingredientsElement = document.getElementById(`${mealType}Ingredients`);

            if (nameElement) {
                nameElement.textContent = 'No meal planned';
            }
            if (ingredientsElement) {
                ingredientsElement.textContent = 'Waiting for cook to plan';
            }
        });

        // Set fallback for weekly menu
        document.querySelectorAll('.meal-name').forEach(el => {
            if (el.textContent === 'Loading...') {
                el.textContent = 'No meal planned';
            }
        });

        document.querySelectorAll('.meal-ingredients').forEach(el => {
            if (el.textContent === 'Loading ingredients...') {
                el.textContent = 'Waiting for cook to plan';
            }
        });

        console.log('All loading states stopped');
    }

    // DEBUGGING: Test API function
    function testAPI() {
        const testBtn = document.getElementById('testAPIBtn');
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Testing...';

        console.log('=== TESTING API ===');

        // Test debug endpoint first
        fetch('/kitchen/debug/menu/1', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Debug API Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Debug API Response:', data);
            showToast('Debug API working! User: ' + data.user + ', Role: ' + data.role, 'success');

            // Now test the actual menu API
            return fetch('/kitchen/menu/1', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        })
        .then(response => {
            console.log('Menu API Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Menu API Response:', data);
            if (data.success) {
                showToast('Menu API working! Found ' + Object.keys(data.menu).length + ' days', 'success');
            } else {
                showToast('Menu API returned: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('API Test Error:', error);
            showToast('API Test Failed: ' + error.message, 'error');
        })
        .finally(() => {
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="bi bi-bug"></i> Test API';
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Define today variable for this scope
        const today = new Date();

        // Only initialize if we're not waiting for cook
        const weekCycleSelect = document.getElementById('weekCycleSelect');
        if (weekCycleSelect) {
            // UNIFIED: Calculate current week cycle and date info
            const weekInfo = getCurrentWeekCycle();
            const weekOfMonth = weekInfo.weekOfMonth;
            const currentWeekCycle = weekInfo.weekCycle;
            const todayDay = weekInfo.currentDayName;

            console.log('=== UNIFIED WEEK CALCULATION ===');
            console.log('Week calculation:', {
                today: today.toDateString(),
                dayOfWeek: today.getDay(),
                dayName: todayDay,
                dayOfMonth: today.getDate(),
                weekOfMonth: weekOfMonth,
                calculatedCycle: currentWeekCycle,
                serverCycle: {{ $weekCycle ?? 1 }}
            });
            console.log('=== END WEEK CALCULATION ===');

            // FIXED: Set current week cycle and only highlight if it's the current week
            weekCycleSelect.value = currentWeekCycle;

            // UNIFIED: Dynamic today highlighting with week awareness
            function updateTodayHighlight() {
                const currentWeekInfo = getCurrentWeekCycle();
                const selectedWeekCycle = parseInt(weekCycleSelect.value);

                // Remove all existing today highlights and week indicators
                document.querySelectorAll('tr.today').forEach(row => {
                    row.classList.remove('today');
                });
                document.querySelectorAll('.today-indicator').forEach(indicator => {
                    indicator.remove();
                });

                // UNIFIED: Add week highlighting - highlight current week differently (NO GRADIENT)
                const weekCycleHeader = document.querySelector('.card-header');
                if (weekCycleHeader) {
                    const highlighting = getMenuHighlighting('monday', selectedWeekCycle); // Use any day to get week status
                    if (highlighting.isCurrentWeek) {
                        weekCycleHeader.style.background = '#22bbea';
                        weekCycleHeader.style.color = 'white';
                    } else {
                        weekCycleHeader.style.background = '#f8f9fa';
                        weekCycleHeader.style.color = '#333';
                    }
                }

                // UNIFIED: Apply highlighting to all rows
                const allRows = document.querySelectorAll('tr[data-day]');
                allRows.forEach(row => {
                    const day = row.getAttribute('data-day');
                    const highlighting = getMenuHighlighting(day, selectedWeekCycle);

                    // Remove existing classes
                    row.classList.remove('today', 'current-week-row');

                    // Apply unified highlighting
                    if (highlighting.isToday) {
                        row.classList.add('today');

                        // Add today indicator
                        const dayCell = row.querySelector('td:first-child');
                        if (dayCell && !dayCell.querySelector('.today-indicator')) {
                            const todayIndicator = document.createElement('span');
                            todayIndicator.className = 'badge bg-warning ms-2 today-indicator';
                            todayIndicator.innerHTML = '<i class="bi bi-star-fill"></i> Today';
                            dayCell.appendChild(todayIndicator);
                        }
                    } else if (highlighting.isCurrentWeek) {
                        row.classList.add('current-week-row');
                    }
                });

                // Update week info display
                updateWeekInfo();
            }

            // Apply initial highlighting
            updateTodayHighlight();

            // Stop loading states immediately and then load menu data
            setTimeout(() => {
                console.log('Stopping loading states and loading initial menu data...');
                setFallbackContent(); // Stop loading states immediately
                loadMenuData();
            }, 100);

            // Handle week cycle changes
            weekCycleSelect.addEventListener('change', function() {
                // Mark as manually changed to prevent auto-updates
                weekCycleSelect.setAttribute('data-manually-changed', 'true');

                // Update highlighting when week cycle changes
                updateTodayHighlight();

                loadMenuData();
            });

            // Refresh menu data every 5 minutes
            setInterval(loadMenuData, 300000);
        } else {
            // We're waiting for cook - check every 30 seconds if menu is available
            setInterval(function() {
                fetch('/kitchen/menu/1')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Menu is now available, reload page
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        // Still waiting, do nothing
                    });
            }, 30000);
        }
    });

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
</script>
@endpush
