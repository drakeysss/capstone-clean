@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Weekly Meal Pre-Order</h2>
                    <p class="text-muted" style="color: white;">Select which meals you will eat this week to help reduce food waste</p>
                     
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- No meal details modal needed -->
    
    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMenuModalLabel">Edit Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editMenuForm">
                        <input type="hidden" id="editDay" name="day">
                        <input type="hidden" id="editMealType" name="meal_type">
                        <input type="hidden" id="editWeekCycle" name="week_cycle" value="1">
                        
                        <div class="mb-3">
                            <label for="editMealName" class="form-label">Meal Name</label>
                            <input type="text" class="form-control" id="editMealName" name="meal_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editIngredients" class="form-label">Ingredients</label>
                            <input type="text" class="form-control" id="editIngredients" name="ingredients" placeholder="Ingredient 1, Ingredient 2, etc." required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editCutoffTime" class="form-label">Cutoff Time</label>
                            <select class="form-select" id="editCutoffTime" name="cutoff_time">
                                <option value="6:00 AM">10:00 PM (Breakfast)</option>
                                <option value="10:00 AM">10:00 PM (Lunch)</option>
                                <option value="3:00 PM">3:00 PM (Dinner)</option>
                                <option value="custom">Custom Time</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="customTimeContainer" style="display: none;">
                            <label for="customCutoffTime" class="form-label">Custom Cutoff Time</label>
                            <input type="time" class="form-control" id="customCutoffTime" name="custom_cutoff_time">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveMenuChangesBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Meal Selection Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Weekly Meal Pre-Order</h5>
                    <div>
                        <select id="weekCycleSelect" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="1">Week 1 & 3</option>
                            <option value="2">Week 2 & 4</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> Please select which meals you will eat this week. This helps us reduce food waste by preparing the right amount of food.
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> Note that the deadline may vary on students class schedule.
                    </div>
                    
                    <form id="mealPreOrderForm" action="{{ route('student.meals.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" id="week_cycle" name="week_cycle" value="1">
                        
                        <!-- Week 1 & 3 Menu -->
                        <div id="week1Menu" class="week-menu">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="15%">Day</th>
                                            <th width="25%">Breakfast <span class="badge bg-danger">Deadline: 10:00 PM</span></th>
                                            <th width="25%">Lunch <span class="badge bg-danger">Deadline: 10:00 PM</span></th>
                                            <th width="25%">Dinner <span class="badge bg-danger">Deadline: 3:00 PM</span></th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Monday -->                                        
                                        <tr>
                                            <td class="fw-bold">Monday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Chicken Loaf with Energen</div>
                                                    <small class="text-muted">Chicken Loaf, Energen, Water</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_breakfast" id="monday_breakfast_yes" value="yes">
                                                            <label class="form-check-label text-success" for="monday_breakfast_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_breakfast" id="monday_breakfast_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="monday_breakfast_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Fried Fish</div>
                                                    <small class="text-muted">Fish, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_lunch" id="monday_lunch_yes" value="yes">
                                                            <label class="form-check-label text-success" for="monday_lunch_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_lunch" id="monday_lunch_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="monday_lunch_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Ginisang Cabbage</div>
                                                    <small class="text-muted">Cabbage, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_dinner" id="monday_dinner_yes" value="yes">
                                                            <label class="form-check-label text-success" for="monday_dinner_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_dinner" id="monday_dinner_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="monday_dinner_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="monday" data-week="1">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Tuesday -->                                        
                                        <tr>
                                            <td class="fw-bold">Tuesday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Odong with Sardines</div>
                                                    <small class="text-muted">Odong Noodles, Sardines, Water</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_breakfast" id="tuesday_breakfast_yes" value="yes">
                                                            <label class="form-check-label text-success" for="tuesday_breakfast_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_breakfast" id="tuesday_breakfast_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="tuesday_breakfast_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Fried Chicken</div>
                                                    <small class="text-muted">Chicken, Oil, Salt, Pepper</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_lunch" id="tuesday_lunch_yes" value="yes">
                                                            <label class="form-check-label text-success" for="tuesday_lunch_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_lunch" id="tuesday_lunch_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="tuesday_lunch_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Baguio Beans</div>
                                                    <small class="text-muted">Baguio Beans, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_dinner" id="tuesday_dinner_yes" value="yes">
                                                            <label class="form-check-label text-success" for="tuesday_dinner_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_dinner" id="tuesday_dinner_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="tuesday_dinner_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="tuesday" data-week="1">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Wednesday -->                                        
                                        <tr>
                                            <td class="fw-bold">Wednesday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Hotdogs</div>
                                                    <small class="text-muted">Hotdogs, Oil</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_breakfast" id="wednesday_breakfast_yes" value="yes">
                                                            <label class="form-check-label text-success" for="wednesday_breakfast_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_breakfast" id="wednesday_breakfast_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="wednesday_breakfast_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Porkchop Guisado</div>
                                                    <small class="text-muted">Porkchop, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_lunch" id="wednesday_lunch_yes" value="yes">
                                                            <label class="form-check-label text-success" for="wednesday_lunch_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_lunch" id="wednesday_lunch_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="wednesday_lunch_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Pinakbet</div>
                                                    <small class="text-muted">Squash, Eggplant, Okra, String Beans, Bitter Gourd</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_dinner" id="wednesday_dinner_yes" value="yes">
                                                            <label class="form-check-label text-success" for="wednesday_dinner_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_dinner" id="wednesday_dinner_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="wednesday_dinner_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="wednesday" data-week="1">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Thursday -->                                        
                                        <tr>
                                            <td class="fw-bold">Thursday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Boiled Eggs with Energen</div>
                                                    <small class="text-muted">Eggs, Energen, Water</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_breakfast" id="thursday_breakfast_yes" value="yes">
                                                            <label class="form-check-label text-success" for="thursday_breakfast_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_breakfast" id="thursday_breakfast_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="thursday_breakfast_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Groundpork</div>
                                                    <small class="text-muted">Ground Pork, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_lunch" id="thursday_lunch_yes" value="yes">
                                                            <label class="form-check-label text-success" for="thursday_lunch_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_lunch" id="thursday_lunch_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="thursday_lunch_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Chopsuey</div>
                                                    <small class="text-muted">Mixed Vegetables, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_dinner" id="thursday_dinner_yes" value="yes">
                                                            <label class="form-check-label text-success" for="thursday_dinner_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_dinner" id="thursday_dinner_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="thursday_dinner_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="thursday" data-week="1">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Friday -->                                        
                                        <tr>
                                            <td class="fw-bold">Friday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Ham</div>
                                                    <small class="text-muted">Ham, Oil</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_breakfast" id="friday_breakfast_yes" value="yes">
                                                            <label class="form-check-label text-success" for="friday_breakfast_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_breakfast" id="friday_breakfast_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="friday_breakfast_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Fried Chicken</div>
                                                    <small class="text-muted">Chicken, Oil, Salt, Pepper</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_lunch" id="friday_lunch_yes" value="yes">
                                                            <label class="form-check-label text-success" for="friday_lunch_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_lunch" id="friday_lunch_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="friday_lunch_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Monggo Beans</div>
                                                    <small class="text-muted">Monggo Beans, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_dinner" id="friday_dinner_yes" value="yes">
                                                            <label class="form-check-label text-success" for="friday_dinner_yes"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_dinner" id="friday_dinner_no" value="no" checked>
                                                            <label class="form-check-label text-danger" for="friday_dinner_no"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="friday" data-week="1">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Send to Students
                                </button>
                            </div>
                        </div>
                        
                        <!-- Week 2 & 4 Menu -->
                        <div id="week2Menu" class="week-menu" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="15%">Day</th>
                                            <th width="25%">Breakfast <span class="badge bg-danger">Deadline: 10:00 PM</span></th>
                                            <th width="25%">Lunch <span class="badge bg-danger">Deadline: 10:00 PM</span></th>
                                            <th width="25%">Dinner <span class="badge bg-danger">Deadline: 3:00 PM</span></th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Monday Week 2 -->                                        
                                        <tr>
                                            <td class="fw-bold">Monday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Chorizo</div>
                                                    <small class="text-muted">Chorizo, Oil</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_breakfast_w2" id="monday_breakfast_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="monday_breakfast_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_breakfast_w2" id="monday_breakfast_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="monday_breakfast_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Chicken Adobo</div>
                                                    <small class="text-muted">Chicken, Soy Sauce, Vinegar, Garlic, Onion</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_lunch_w2" id="monday_lunch_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="monday_lunch_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_lunch_w2" id="monday_lunch_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="monday_lunch_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">String Beans Guisado</div>
                                                    <small class="text-muted">String Beans, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_dinner_w2" id="monday_dinner_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="monday_dinner_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="monday_dinner_w2" id="monday_dinner_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="monday_dinner_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="monday" data-week="2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Tuesday Week 2 -->                                        
                                        <tr>
                                            <td class="fw-bold">Tuesday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Scrambled Eggs with Energen</div>
                                                    <small class="text-muted">Eggs, Energen, Water</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_breakfast_w2" id="tuesday_breakfast_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="tuesday_breakfast_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_breakfast_w2" id="tuesday_breakfast_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="tuesday_breakfast_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Fried Fish</div>
                                                    <small class="text-muted">Fish, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_lunch_w2" id="tuesday_lunch_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="tuesday_lunch_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_lunch_w2" id="tuesday_lunch_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="tuesday_lunch_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Talong with Eggs</div>
                                                    <small class="text-muted">Eggplant, Eggs, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_dinner_w2" id="tuesday_dinner_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="tuesday_dinner_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tuesday_dinner_w2" id="tuesday_dinner_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="tuesday_dinner_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="tuesday" data-week="2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Wednesday Week 2 -->                                        
                                        <tr>
                                            <td class="fw-bold">Wednesday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Sardines with Eggs</div>
                                                    <small class="text-muted">Sardines, Eggs, Oil</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_breakfast_w2" id="wednesday_breakfast_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="wednesday_breakfast_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_breakfast_w2" id="wednesday_breakfast_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="wednesday_breakfast_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Groundpork</div>
                                                    <small class="text-muted">Ground Pork, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_lunch_w2" id="wednesday_lunch_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="wednesday_lunch_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_lunch_w2" id="wednesday_lunch_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="wednesday_lunch_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Tinun-ang Kalabasa with Buwad</div>
                                                    <small class="text-muted">Kalabasa, Buwad, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_dinner_w2" id="wednesday_dinner_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="wednesday_dinner_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="wednesday_dinner_w2" id="wednesday_dinner_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="wednesday_dinner_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="wednesday" data-week="2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Thursday Week 2 -->                                        
                                        <tr>
                                            <td class="fw-bold">Thursday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Luncheon Meat</div>
                                                    <small class="text-muted">Luncheon Meat, Oil</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_breakfast_w2" id="thursday_breakfast_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="thursday_breakfast_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_breakfast_w2" id="thursday_breakfast_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="thursday_breakfast_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Fried Chicken</div>
                                                    <small class="text-muted">Chicken, Oil, Salt, Pepper</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_lunch_w2" id="thursday_lunch_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="thursday_lunch_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_lunch_w2" id="thursday_lunch_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="thursday_lunch_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Chopsuey</div>
                                                    <small class="text-muted">Mixed Vegetables, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_dinner_w2" id="thursday_dinner_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="thursday_dinner_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="thursday_dinner_w2" id="thursday_dinner_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="thursday_dinner_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="thursday" data-week="2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Friday Week 2 -->                                        
                                        <tr>
                                            <td class="fw-bold">Friday</td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Sotanghon Guisado</div>
                                                    <small class="text-muted">Sotanghon, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_breakfast_w2" id="friday_breakfast_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="friday_breakfast_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_breakfast_w2" id="friday_breakfast_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="friday_breakfast_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Pork Menudo</div>
                                                    <small class="text-muted">Pork, Carrots, Potatoes, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_lunch_w2" id="friday_lunch_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="friday_lunch_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_lunch_w2" id="friday_lunch_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="friday_lunch_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meal-item">
                                                    <div class="fw-bold">Monggo Beans</div>
                                                    <small class="text-muted">Monggo Beans, Garlic, Onion, Oil, Salt</small>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_dinner_w2" id="friday_dinner_yes_w2" value="yes">
                                                            <label class="form-check-label text-success" for="friday_dinner_yes_w2"><i class="bi bi-check-circle-fill"></i> Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="friday_dinner_w2" id="friday_dinner_no_w2" value="no" checked>
                                                            <label class="form-check-label text-danger" for="friday_dinner_no_w2"><i class="bi bi-x-circle-fill"></i> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-day="friday" data-week="2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Send to Students
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Meal Tally -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Current Meal Tally <span class="badge bg-primary">Week 4</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <input type="date" id="customDate" class="form-control form-control-sm" value="2025-05-30">
                        </div>
                      
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> <strong>Selected Date: <span id="selectedDateDisplay">Thursday, May 30, 2025</span></strong> - Showing meal counts for the selected date. Numbers indicate how many students selected each meal.
                    
                    <div id="week1Tally" class="week-tally">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Day</th>
                                        <th>Breakfast</th>
                                        <th>Lunch</th>
                                        <th>Dinner</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Monday</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                
                                                <span class="badge bg-success">64 students</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                
                                                <span class="badge bg-success">62 students</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                
                                                <span class="badge bg-success">68 students</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-success">done</span>
                                        </td>
                                    </tr>
                                <tr>
                                    <td class="fw-bold">Tuesday</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                           
                                            <span class="badge bg-success">60 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                          
                                            <span class="badge bg-success">68 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                           
                                            <span class="badge bg-success">65 students</span>
                                        </div>
                                    </td>
                                     <td class="align-middle text-center">
                                            <span class="badge bg-success">done</span>
                                        </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Wednesday</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                           
                                            <span class="badge bg-success">68 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                           
                                            <span class="badge bg-success">65 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                           
                                            <span class="badge bg-success">62 students</span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                            <span class="badge bg-success">done</span>
                                        </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Thursday</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                         
                                            <span class="badge bg-success">62 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            
                                            <span class="badge bg-success">66 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                           
                                            <span class="badge bg-success">59 students</span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-success">done</span>
                                    </td>
                                </tr>
                                <trclass="table-primary">
                                    <td class="fw-bold">Friday<span class="badge bg-warning">Today</span></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            
                                            <span class="badge bg-success">59 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            
                                            <span class="badge bg-success">63 students</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                           
                                            <span class="badge bg-success">61 students</span>
                                        </div>
                                    </td>
                                     <td class="align-middle text-center">
                                            <span class="badge bg-warning">Ready to Cook</span>
                                        </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="week2Tally" class="week-tally" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Day</th>
                                        <th>Breakfast</th>
                                        <th>Lunch</th>
                                        <th>Dinner</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Monday</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Fried Rice with Egg</span>
                                                    <span class="badge bg-success">26 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 26 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Pork Menudo</span>
                                                    <span class="badge bg-success">30 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 30 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Chop Suey</span>
                                                    <span class="badge bg-success">22 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 22 students</small>
                                            </div>
                                        </td>
                                      
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Tuesday</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Oatmeal</span>
                                                    <span class="badge bg-success">18 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 18 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Beef Caldereta</span>
                                                    <span class="badge bg-success">34 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 34 students</small>
                                            </div>
                                        </td>
                                        <td>
                                           
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Wednesday</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Hotdogs</span>
                                                    <span class="badge bg-success">28 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 28 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Porkchop Guisado</span>
                                                    <span class="badge bg-success">30 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 30 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Pinakbet</span>
                                                    <span class="badge bg-success">12 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 12 students</small>
                                            </div>
                                        </td>
                                      
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Thursday</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>French Toast</span>
                                                    <span class="badge bg-success">20 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 20 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Chicken Afritada</span>
                                                    <span class="badge bg-success">32 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 32 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Beef Salpicao</span>
                                                    <span class="badge bg-success">27 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 27 students</small>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-warning">Ready to Cook</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Friday</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Lugaw</span>
                                                    <span class="badge bg-success">16 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 16 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Beef Nilaga</span>
                                                    <span class="badge bg-success">29 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 29 students</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Monggo</span>
                                                    <span class="badge bg-success">24 students</span>
                                                </div>
                                                <small class="text-muted mt-1"><i class="bi bi-info-circle"></i> Prepare for 24 students</small>
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
</div>

<!-- Deadline Edit Modal -->
<div class="modal fade" id="editDeadlineModal" tabindex="-1" aria-labelledby="editDeadlineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDeadlineModalLabel">Edit Deadline Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDeadlineForm">
                    <input type="hidden" id="editDeadlineMealType" name="editDeadlineMealType">
                    
                    <div class="mb-3">
                        <label for="editDeadlineTime" class="form-label">Deadline Time</label>
                        <input type="time" class="form-control" id="editDeadlineTime" name="editDeadlineTime" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDeadlineBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle between Week 1 & 3 and Week 2 & 4 menus
    document.addEventListener('DOMContentLoaded', function() {
        const weekCycleSelect = document.getElementById('weekCycleSelect');
        const week1Menu = document.getElementById('week1Menu');
        const week2Menu = document.getElementById('week2Menu');
        const weekCycleInput = document.getElementById('week_cycle');
        const editCutoffTime = document.getElementById('editCutoffTime');
        const customTimeContainer = document.getElementById('customTimeContainer');
        const customCutoffTime = document.getElementById('customCutoffTime');
        
        // Handle the date selector and update display
        const customDateInput = document.getElementById('customDate');
        const selectedDateDisplay = document.getElementById('selectedDateDisplay');
        const timeElement = document.getElementById('realTimeTime');
        
        // Initialize with current date
        function initializeDate() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            
            // Set the date input value
            if (customDateInput) {
                customDateInput.value = `${year}-${month}-${day}`;
                updateSelectedDateDisplay(now);
            }
        }
        
        // Update the selected date display
        function updateSelectedDateDisplay(date) {
            if (selectedDateDisplay) {
                const options = { 
                    weekday: 'long',
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric'
                };
                selectedDateDisplay.textContent = date.toLocaleDateString('en-US', options);
            }
        }
        
        // Update real-time time display
        function updateTimeDisplay() {
            const now = new Date();
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const timeString = now.toLocaleTimeString('en-US', timeOptions);
            
            if (timeElement) {
                timeElement.textContent = timeString;
            }
            
            // Update date/time in edit modal
            const modalDateTimeElement = document.getElementById('currentDateTime');
            if (modalDateTimeElement) {
                modalDateTimeElement.textContent = 'Current Date: ' + selectedDateDisplay.textContent + ' ' + timeString;
            }
        }
        
        // Initialize date and set up event listener
        initializeDate();
        
        if (customDateInput) {
            customDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                updateSelectedDateDisplay(selectedDate);
            });
        }
        
        // Update time display immediately and then every second
        updateTimeDisplay();
        setInterval(updateTimeDisplay, 1000);
        
        // Add edit deadline buttons to meal headers
        function addDeadlineEditButtons() {
            // Find all table headers in the week menus
            const weekMenus = document.querySelectorAll('.week-menu');
            
            weekMenus.forEach(menu => {
                const headers = menu.querySelectorAll('th');
                
                // Add edit buttons to meal type headers (breakfast, lunch, dinner)
                for (let i = 1; i <= 3; i++) {
                    if (headers[i]) {
                        const mealType = i === 1 ? 'breakfast' : i === 2 ? 'lunch' : 'dinner';
                        const deadlineBadge = headers[i].querySelector('.badge');
                        
                        if (deadlineBadge) {
                            // Create a container for the badge and edit button
                            const container = document.createElement('div');
                            container.className = 'd-flex align-items-center mt-1';
                            
                            // Create the edit button
                            const editButton = document.createElement('button');
                            editButton.className = 'btn btn-sm btn-outline-secondary ms-1 edit-deadline-btn';
                            editButton.setAttribute('data-bs-toggle', 'modal');
                            editButton.setAttribute('data-bs-target', '#editDeadlineModal');
                            editButton.setAttribute('data-meal-type', mealType);
                            editButton.innerHTML = '<i class="bi bi-pencil-square"></i>';
                            
                            // Add click event to the edit button
                            editButton.addEventListener('click', function() {
                                openDeadlineEditModal(mealType, deadlineBadge.textContent);
                            });
                            
                            // Replace the badge with the container
                            const badgeParent = deadlineBadge.parentNode;
                            badgeParent.replaceChild(container, deadlineBadge);
                            
                            // Add the badge and edit button to the container
                            container.appendChild(deadlineBadge);
                            container.appendChild(editButton);
                        }
                    }
                }
            });
        }
        
        // Function to open the deadline edit modal
        function openDeadlineEditModal(mealType, currentDeadline) {
            const mealTypeInput = document.getElementById('editDeadlineMealType');
            const timeInput = document.getElementById('editDeadlineTime');
            
            // Set the meal type
            mealTypeInput.value = mealType;
            
            // Extract the time from the deadline text (e.g., "Deadline: 6:00 AM" -> "6:00 AM")
            let timeStr = currentDeadline.replace('Deadline: ', '').trim();
            
            // Convert to 24-hour format for the time input
            const timeParts = timeStr.match(/([0-9]+):([0-9]+)\s*(AM|PM)/i);
            if (timeParts) {
                let hours = parseInt(timeParts[1]);
                const minutes = timeParts[2];
                const period = timeParts[3].toUpperCase();
                
                if (period === 'PM' && hours < 12) hours += 12;
                if (period === 'AM' && hours === 12) hours = 0;
                
                timeInput.value = `${hours.toString().padStart(2, '0')}:${minutes}`;
            }
        }
        
        // Handle saving the deadline changes
        const saveDeadlineBtn = document.getElementById('saveDeadlineBtn');
        if (saveDeadlineBtn) {
            saveDeadlineBtn.addEventListener('click', function() {
                const mealType = document.getElementById('editDeadlineMealType').value;
                const timeInput = document.getElementById('editDeadlineTime').value;
                
                // Convert time to 12-hour format with AM/PM
                const timeParts = timeInput.split(':');
                let hours = parseInt(timeParts[0]);
                const minutes = timeParts[1];
                let period = 'AM';
                
                if (hours >= 12) {
                    period = 'PM';
                    if (hours > 12) hours -= 12;
                }
                if (hours === 0) hours = 12;
                
                const formattedTime = `${hours}:${minutes} ${period}`;
                
                // Update all deadline badges for this meal type
                const deadlineBadges = document.querySelectorAll(`th:nth-child(${mealType === 'breakfast' ? 2 : mealType === 'lunch' ? 3 : 4}) .badge`);
                deadlineBadges.forEach(badge => {
                    badge.textContent = `Deadline: ${formattedTime}`;
                });
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editDeadlineModal'));
                modal.hide();
                
                // Show success message
                alert(`Deadline for ${mealType.charAt(0).toUpperCase() + mealType.slice(1)} updated to ${formattedTime}`);
            });
        }
        
        // Call the function to add edit buttons
        addDeadlineEditButtons();
        
        // Only show Week 1 tally (current week)
        const week1Tally = document.getElementById('week1Tally');
        const week2Tally = document.getElementById('week2Tally');
        
        if (week1Tally && week2Tally) {
            week1Tally.style.display = 'block';
            week2Tally.style.display = 'none';
        }
        const editMenuForm = document.getElementById('editMenuForm');
        const saveMenuChangesBtn = document.getElementById('saveMenuChangesBtn');
        
        // Week cycle toggle functionality
        weekCycleSelect.addEventListener('change', function() {
            const selectedCycle = this.value;
            
            if (selectedCycle === '1') {
                week1Menu.style.display = 'block';
                week2Menu.style.display = 'none';
                weekCycleInput.value = '1';
            } else {
                week1Menu.style.display = 'none';
                week2Menu.style.display = 'block';
                weekCycleInput.value = '2';
            }
        });
        
        // Custom cutoff time toggle
        if (editCutoffTime) {
            editCutoffTime.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customTimeContainer.style.display = 'block';
                } else {
                    customTimeContainer.style.display = 'none';
                }
            });
        }
        
        // Edit menu button click handlers
        const editMenuButtons = document.querySelectorAll('.edit-menu-btn');
        editMenuButtons.forEach(button => {
            button.addEventListener('click', function() {
                const day = this.getAttribute('data-day');
                const weekCycle = this.getAttribute('data-week');
                const mealType = this.getAttribute('data-meal-type') || getMealTypeFromButton(this);
                
                // Set dropdown values for day, week, and meal type
                const daySelect = document.getElementById('editDay');
                const weekSelect = document.getElementById('editWeekCycle');
                const mealTypeSelect = document.getElementById('editMealType');
                
                // Set the selected values
                daySelect.value = day;
                weekSelect.value = weekCycle;
                mealTypeSelect.value = mealType;
                
                // Get current meal data
                const mealItem = getMealItemFromButton(this, mealType);
                
                // Make sure we have a meal item before trying to access its properties
                if (mealItem) {
                    // Get the meal name (could be in different formats based on the UI structure)
                    let mealName = '';
                    const mealNameElement = mealItem.querySelector('.fw-bold');
                    if (mealNameElement) {
                        mealName = mealNameElement.textContent.trim();
                    }
                    
                    // Get the ingredients (could be in different formats)
                    let ingredients = '';
                    const ingredientsElement = mealItem.querySelector('small.text-muted');
                    if (ingredientsElement) {
                        ingredients = ingredientsElement.textContent.trim();
                    }
                    
                    // Set form fields with existing values
                    document.getElementById('editMealName').value = mealName;
                    document.getElementById('editIngredients').value = ingredients;
                }
                
                // Set cutoff time based on meal type
                let cutoffTime = '10:00 PM';
                if (mealType === 'lunch') {
                    cutoffTime = '10:00 PM';
                } else if (mealType === 'dinner') {
                    cutoffTime = '3:00 PM';
                }
                document.getElementById('editCutoffTime').value = cutoffTime;
                
                // Update modal title
                const dayFormatted = day.charAt(0).toUpperCase() + day.slice(1);
                const mealTypeFormatted = mealType.charAt(0).toUpperCase() + mealType.slice(1);
                document.getElementById('editMenuModalLabel').textContent = 
                    `Edit ${dayFormatted} ${mealTypeFormatted} (Week ${weekCycle})`;
            });
        });
        
        // Save menu changes
        if (saveMenuChangesBtn) {
            saveMenuChangesBtn.addEventListener('click', function() {
                const day = document.getElementById('editDay').value;
                const mealType = document.getElementById('editMealType').value;
                const weekCycle = document.getElementById('editWeekCycle').value;
                const mealName = document.getElementById('editMealName').value;
                const ingredients = document.getElementById('editIngredients').value;
                
                // Get cutoff time
                let cutoffTime = document.getElementById('editCutoffTime').value;
                if (cutoffTime === 'custom') {
                    cutoffTime = document.getElementById('customCutoffTime').value;
                }
                
                // Find the meal item to update
                const suffix = weekCycle === '1' ? '' : '_w2';
                const mealSelector = `[name="${day}_${mealType}${suffix}"]`;
                const radioInput = document.querySelector(mealSelector);
                
                if (radioInput) {
                    const mealItem = radioInput.closest('.meal-item');
                    
                    // Only update if we have valid data
                    if (mealName && mealItem) {
                        // Find the elements to update
                        const mealNameElement = mealItem.querySelector('.fw-bold');
                        const ingredientsElement = mealItem.querySelector('small.text-muted');
                        
                        // Update meal name and ingredients if elements exist
                        if (mealNameElement) {
                            mealNameElement.textContent = mealName;
                        }
                        
                        if (ingredientsElement) {
                            ingredientsElement.textContent = ingredients;
                        }
                        
                        // Update cutoff time in the table header
                        updateCutoffTime(mealType, cutoffTime, weekCycle);
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editMenuModal'));
                    modal.hide();
                    
                    // Get current date and time for the success message
                    const now = new Date();
                    const options = { 
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    };
                    const dateTimeString = now.toLocaleDateString('en-US', options);
                    
                    // Show success message with real-time date
                    alert(`${day.charAt(0).toUpperCase() + day.slice(1)} ${mealType} updated successfully on ${dateTimeString}!`);
                } else {
                    // If meal not found, show error message
                    alert(`Could not find the meal to update. Please check your selections and try again.`);
                }
            });
        }
        
        // Helper function to get meal type from button
        function getMealTypeFromButton(button) {
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');
            const buttonCell = button.closest('td');
            const cellIndex = Array.from(cells).indexOf(buttonCell);
            
            // Determine meal type based on cell index
            // Cell indexes: 0=Day, 1=Breakfast, 2=Lunch, 3=Dinner, 4=Actions
            switch (cellIndex) {
                case 1: return 'breakfast';
                case 2: return 'lunch';
                case 3: return 'dinner';
                default: return 'breakfast'; // Default fallback
            }
        }
        
        // Helper function to get meal item from button and meal type
        function getMealItemFromButton(button, mealType) {
            // If the button is inside a meal item, return that meal item directly
            const mealItem = button.closest('.meal-item');
            if (mealItem) {
                return mealItem;
            }
            
            // Otherwise, find the meal item based on row and meal type
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');
            
            // Get meal cell based on meal type
            let cellIndex = 1; // Default to breakfast
            if (mealType === 'lunch') cellIndex = 2;
            if (mealType === 'dinner') cellIndex = 3;
            
            return cells[cellIndex].querySelector('.meal-item');
        }
        
        // Helper function to update cutoff time in table header
        function updateCutoffTime(mealType, cutoffTime, weekCycle) {
            const weekMenuId = weekCycle === '1' ? 'week1Menu' : 'week2Menu';
            const weekMenu = document.getElementById(weekMenuId);
            if (!weekMenu) return;
            
            const headers = weekMenu.querySelectorAll('th');
            let headerIndex = 1; // Default to breakfast
            if (mealType === 'lunch') headerIndex = 2;
            if (mealType === 'dinner') headerIndex = 3;
            
            if (headers[headerIndex]) {
                const badge = headers[headerIndex].querySelector('.badge');
                if (badge) {
                    badge.textContent = `Deadline: ${cutoffTime}`;
                }
            }
        }
    });
</script>
@endpush

<!-- Edit Menu Modal -->
<div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMenuModalLabel">Edit Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMenuForm">
                    <div class="alert alert-info">
                        <i class="bi bi-calendar-check me-2"></i> <span id="currentDateTime">Current Date: May 30, 2025 (08:10 AM)</span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editDay" class="form-label">Day</label>
                            <select class="form-select" id="editDay" name="editDay" required>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editWeekCycle" class="form-label">Week</label>
                            <select class="form-select" id="editWeekCycle" name="editWeekCycle" required>
                                <option value="1">Week 1</option>
                                <option value="2">Week 2</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editMealType" class="form-label">Meal Type</label>
                        <select class="form-select" id="editMealType" name="editMealType" required>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editMealName" class="form-label">Meal Name</label>
                        <input type="text" class="form-control" id="editMealName" name="editMealName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editIngredients" class="form-label">Ingredients</label>
                        <textarea class="form-control" id="editIngredients" name="editIngredients" rows="3" required></textarea>
                        <small class="text-muted">Separate ingredients with commas</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editCutoffTime" class="form-label">Cutoff Time</label>
                        <select class="form-select" id="editCutoffTime" name="editCutoffTime">
                            <option value="6:00 AM">10:00 PM (Breakfast Default)</option>
                            <option value="10:00 AM">10:00 PM (Lunch Default)</option>
                             <option value="10:00 AM">8:00 AM (Lunch Default)</option>
                            <option value="3:00 PM">10:00 PM (Dinner Default)</option>
                            <option value="custom">Custom Time</option>
                        </select>
                    </div>
                    
                    <div id="customTimeContainer" class="mb-3" style="display: none;">
                        <label for="customCutoffTime" class="form-label">Custom Cutoff Time</label>
                        <input type="time" class="form-control" id="customCutoffTime" name="customCutoffTime">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveMenuChangesBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>
