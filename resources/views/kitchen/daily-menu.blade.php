@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Daily & Weekly Menu</h2>
                    <p class="text-muted" style="color: white;">View today's menu and upcoming meals for the week</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Menu Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Today's Menu <span class="badge bg-primary ms-2" id="todayDayBadge">Monday</span></h5>
                    <div>
                        <span class="text-muted me-2">Week Cycle:</span>
                        <span class="badge bg-info" id="weekCycleBadge">Week 1 & 3</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Breakfast -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light text-center">
                                    <h6 class="mb-0">Breakfast</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="meal-item">
                                        <div class="fw-bold mb-2" id="breakfastName">Chicken Loaf with Energen</div>
                                        <small class="text-muted" id="breakfastIngredients">Chicken Loaf, Energen, Water</small>
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
                                        <div class="fw-bold mb-2" id="lunchName">Fried Fish</div>
                                        <small class="text-muted" id="lunchIngredients">Fish, Oil, Salt</small>
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
                                        <div class="fw-bold mb-2" id="dinnerName">Ginisang Cabbage</div>
                                        <small class="text-muted" id="dinnerIngredients">Cabbage, Garlic, Onion, Oil, Salt</small>
                                    </div>
                                    <div class="meal-time mt-2">
                                        <span class="badge bg-secondary">5:30 PM - 7:30 PM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <div>
                        <select id="weekCycleSelect" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="1">Week 1 & 3</option>
                            <option value="2">Week 2 & 4</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
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
                                <!-- Monday -->
                                <tr data-day="monday" class="menu-row">
                                    <td class="fw-bold">Monday</td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Chicken Loaf with Energen</div>
                                            <small class="text-muted">Chicken Loaf, Energen, Water</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Fried Fish</div>
                                            <small class="text-muted">Fish, Oil, Salt</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Ginisang Cabbage</div>
                                            <small class="text-muted">Cabbage, Garlic, Onion, Oil, Salt</small>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Tuesday -->
                                <tr data-day="tuesday" class="menu-row">
                                    <td class="fw-bold">Tuesday</td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Odong with Sardines</div>
                                            <small class="text-muted">Odong Noodles, Sardines, Water</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Fried Chicken</div>
                                            <small class="text-muted">Chicken, Oil, Salt, Pepper</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Monggo</div>
                                            <small class="text-muted">Mung Beans, Spinach, Pork, Garlic, Onion</small>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Wednesday -->
                                <tr data-day="wednesday" class="menu-row">
                                    <td class="fw-bold">Wednesday</td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Pancakes</div>
                                            <small class="text-muted">Flour, Egg, Milk, Sugar, Butter</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Pork Adobo</div>
                                            <small class="text-muted">Pork, Vinegar, Soy Sauce, Garlic, Bay Leaves</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Pinakbet</div>
                                            <small class="text-muted">Squash, Eggplant, Okra, String Beans, Bitter Gourd</small>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Thursday -->
                                <tr data-day="thursday" class="menu-row">
                                    <td class="fw-bold">Thursday</td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Hotdog with Rice</div>
                                            <small class="text-muted">Hotdog, Rice, Egg</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Beef Nilaga</div>
                                            <small class="text-muted">Beef, Cabbage, Potato, Carrot, Corn</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Ginisang Sayote</div>
                                            <small class="text-muted">Sayote, Ground Pork, Garlic, Onion, Oil</small>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Friday -->
                                <tr data-day="friday" class="menu-row">
                                    <td class="fw-bold">Friday</td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Oatmeal</div>
                                            <small class="text-muted">Oats, Milk, Sugar, Fruit</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Fish Fillet</div>
                                            <small class="text-muted">Fish Fillet, Flour, Egg, Breadcrumbs</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Vegetable Soup</div>
                                            <small class="text-muted">Mixed Vegetables, Chicken Stock, Garlic, Onion</small>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Saturday -->
                                <tr data-day="saturday" class="menu-row">
                                    <td class="fw-bold">Saturday</td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Champorado</div>
                                            <small class="text-muted">Rice, Cocoa, Sugar, Milk</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Pork Sinigang</div>
                                            <small class="text-muted">Pork, Tamarind, Vegetables</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Fried Eggplant</div>
                                            <small class="text-muted">Eggplant, Egg, Flour, Oil</small>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Sunday -->
                                <tr data-day="sunday" class="menu-row">
                                    <td class="fw-bold">Sunday</td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Tocino with Egg</div>
                                            <small class="text-muted">Tocino, Egg, Rice</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Chicken Tinola</div>
                                            <small class="text-muted">Chicken, Papaya, Chili Leaves, Ginger</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meal-item">
                                            <div class="fw-bold">Mixed Vegetables</div>
                                            <small class="text-muted">Carrots, Cabbage, String Beans, Bell Pepper</small>
                                        </div>
                                    </td>
                                </tr>
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
    .meal-item {
        margin-bottom: 10px;
    }
    
    .meal-time {
        margin-top: 10px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .menu-row.today {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .card-header.bg-light {
        background-color: #f8f9fa !important;
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
    
    updateDateTime();
    setInterval(updateDateTime, 60000);
    
    // Highlight today's menu in the weekly menu table
    function highlightToday() {
        const now = new Date();
        const dayOfWeek = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
        const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        
        if (dayOfWeek >= 0 && dayOfWeek <= 6) {
            const todayName = dayNames[dayOfWeek];
            const todayRow = document.querySelector(`tr[data-day="${todayName}"]`);
            if (todayRow) {
                todayRow.classList.add('today');
            }
            
            // Update today badge
            document.getElementById('todayDayBadge').textContent = todayName.charAt(0).toUpperCase() + todayName.slice(1);
        }
    }
    
    highlightToday();
    
    // Handle week cycle selection
    document.getElementById('weekCycleSelect').addEventListener('change', function() {
        const weekCycle = this.value;
        
        // In a real application, this would fetch the menu data for the selected week cycle
        // For now, just update the badge
        if (weekCycle === '1') {
            document.getElementById('weekCycleBadge').textContent = 'Week 1 & 3';
        } else {
            document.getElementById('weekCycleBadge').textContent = 'Week 2 & 4';
            
            // Update the table with Week 2 & 4 data (simplified for demo)
            // In a real application, this would be dynamically populated from the database
            updateWeek2Menu();
        }
    });
    
    // Function to update the table with Week 2 & 4 menu data
    function updateWeek2Menu() {
        // This is a simplified example - in a real application, this would fetch data from the server
        const week2Data = {
            'monday': {
                breakfast: { name: 'Chorizo', ingredients: 'Chorizo, Oil' },
                lunch: { name: 'Chicken Curry', ingredients: 'Chicken, Potato, Carrots, Curry Powder' },
                dinner: { name: 'Chopsuey', ingredients: 'Mixed Vegetables, Chicken, Oyster Sauce' }
            },
            'tuesday': {
                breakfast: { name: 'Spam with Egg', ingredients: 'Spam, Egg, Rice' },
                lunch: { name: 'Beef Caldereta', ingredients: 'Beef, Potato, Bell Pepper, Tomato Sauce' },
                dinner: { name: 'Ginisang Upo', ingredients: 'Upo Squash, Ground Pork, Garlic, Onion' }
            },
            // Add more days as needed
        };
        
        // Update the table cells with Week 2 & 4 data
        Object.keys(week2Data).forEach(day => {
            const row = document.querySelector(`tr[data-day="${day}"]`);
            if (row) {
                const mealCells = row.querySelectorAll('td:not(:first-child)');
                const mealTypes = ['breakfast', 'lunch', 'dinner'];
                
                mealTypes.forEach((mealType, index) => {
                    if (week2Data[day][mealType]) {
                        const meal = week2Data[day][mealType];
                        mealCells[index].innerHTML = `
                            <div class="meal-item">
                                <div class="fw-bold">${meal.name}</div>
                                <small class="text-muted">${meal.ingredients}</small>
                            </div>
                        `;
                    }
                });
            }
        });
    }
</script>
@endpush
