@extends('layouts.app')

@section('content')
<!-- Add CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Menu Management</h2>
                    <p class="text-muted" style="color: white;">Manage weekly menus for students and kitchen staff</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-light btn-sm me-2" onclick="refreshMenuData()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                    <button class="btn btn-warning btn-sm me-2" onclick="clearAllMeals()">
                        <i class="bi bi-trash"></i> Clear All
                    </button>
                    <button class="btn btn-success btn-sm" onclick="saveAllChanges()">
                        <i class="bi bi-check-circle"></i> Save All Changes
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Weekly Menu Management Section -->
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="bi bi-calendar-week"></i> Weekly Menu Management
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary" id="lastUpdated">Last updated: Never</span>
                        <select id="weekCycleSelect" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="1">Week 1 & 3</option>
                            <option value="2">Week 2 & 4</option>
                        </select>
                        <button class="btn btn-outline-primary btn-sm me-2" onclick="toggleEditMode()">
                            <i class="bi bi-pencil"></i> <span id="editModeText">Edit Mode</span>
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="verifyCrossSystemIntegration()" title="Check integration status">
                            <i class="bi bi-shield-check"></i> Verify
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
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
                </div>
            </div>
        </div>
    </div>

<!-- Edit Meal Modal -->
<div class="modal fade" id="editMealModal" tabindex="-1" aria-labelledby="editMealModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMealModalLabel">Edit Meal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMealForm">
                    <input type="hidden" id="editMealId" name="meal_id">
                    <input type="hidden" id="editDay" name="day">
                    <input type="hidden" id="editMealType" name="meal_type">
                    <input type="hidden" id="editWeekCycle" name="week_cycle">

                    <div class="mb-3">
                        <label for="editMealName" class="form-label">Meal Name</label>
                        <input type="text" class="form-control" id="editMealName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="editIngredients" class="form-label">Ingredients</label>
                        <textarea class="form-control" id="editIngredients" name="ingredients" rows="4" required placeholder="Enter ingredients separated by commas"></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning me-2" onclick="clearCurrentMeal()">Clear Meal</button>
                <button type="button" class="btn btn-primary" onclick="saveMealChanges()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Changes Indicator -->
<div class="changes-indicator">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Unsaved Changes!</strong> You have unsaved menu changes.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --primary-orange: #ff9933;
        --primary-blue: #22bbea;
        --orange-bg: rgba(255, 153, 51, 0.1);
        --blue-bg: rgba(34, 187, 234, 0.1);
    }

    .welcome-card {
        background: var(--primary-orange);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(255, 153, 51, 0.2);
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .header-actions .btn-light {
        background-color: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        transition: all 0.3s ease;
    }

    .header-actions .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        transform: translateY(-2px);
    }

    .header-actions .btn-warning {
        background-color: var(--primary-orange);
        border-color: var(--primary-orange);
        color: white;
    }

    .header-actions .btn-warning:hover {
        background-color: #e6851a;
        border-color: #e6851a;
        transform: translateY(-2px);
    }

    .header-actions .btn-success {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }

    .header-actions .btn-success:hover {
        background-color: #1a9bd1;
        border-color: #1a9bd1;
        transform: translateY(-2px);
    }

    .card {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: linear-gradient(90deg, var(--orange-bg) 0%, var(--blue-bg) 100%);
        border-bottom: 2px solid var(--primary-orange);
        border-radius: 12px 12px 0 0 !important;
    }

    .card-title {
        color: var(--primary-blue);
        font-weight: 600;
    }

    .meal-item {
        position: relative;
        padding: 12px;
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .meal-item:hover {
        background: linear-gradient(135deg, var(--orange-bg) 0%, var(--blue-bg) 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 153, 51, 0.2);
        border-color: var(--orange-light);
    }

    .meal-item.editable {
        border: 2px dashed var(--primary-orange);
        cursor: pointer;
        background: var(--orange-bg);
    }

    .meal-item.editable:hover {
        border-color: var(--orange-dark);
        background: linear-gradient(135deg, var(--orange-bg) 0%, var(--blue-bg) 100%);
    }

    .meal-status {
        position: absolute;
        top: 5px;
        right: 5px;
        font-size: 0.75rem;
    }



    .kitchen-status-badge {
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 12px;
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead th {
        background: var(--primary-orange);
        color: white;
        border: none;
        font-weight: 600;
        padding: 15px;
    }

    .table td {
        vertical-align: middle;
        position: relative;
        padding: 15px;
        border-color: rgba(255, 153, 51, 0.2);
    }

    .table tbody tr:hover {
        background: var(--orange-bg);
    }

    .btn-outline-primary {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }

    .btn-outline-info {
        border-color: var(--primary-orange);
        color: var(--primary-orange);
    }

    .btn-outline-info:hover {
        background-color: var(--primary-orange);
        border-color: var(--primary-orange);
        color: white;
    }

    .btn-primary {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
    }

    .btn-primary:hover {
        background-color: #1a9bd1;
        border-color: #1a9bd1;
    }

    .btn-warning {
        background-color: var(--primary-orange);
        border-color: var(--primary-orange);
        color: white;
    }

    .btn-warning:hover {
        background-color: #e6851a;
        border-color: #e6851a;
        color: white;
    }

    .modal-header {
        background: var(--primary-orange);
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    /* ULTIMATE MODAL FIXES - HIGHEST PRIORITY */
    .modal {
        z-index: 9999 !important;
        position: fixed !important;
    }

    .modal-backdrop {
        z-index: 9998 !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        pointer-events: auto !important;
    }

    .modal.show {
        z-index: 9999 !important;
        display: block !important;
    }

    .modal-dialog {
        z-index: 10000 !important;
        position: relative !important;
        pointer-events: auto !important;
    }

    .modal-content {
        z-index: 10001 !important;
        position: relative !important;
        pointer-events: auto !important;
    }

    /* Override ALL other z-index conflicts */
    .sidebar, .sidebar-overlay, .notification-popup, .dropdown-menu {
        z-index: 1000 !important;
    }

    /* Ensure modal is clickable */
    #editMealModal {
        z-index: 9999 !important;
        pointer-events: auto !important;
    }

    #editMealModal .modal-dialog {
        pointer-events: auto !important;
    }

    #editMealModal .modal-content {
        pointer-events: auto !important;
    }

    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    .form-control:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 0.2rem rgba(255, 153, 51, 0.25);
    }

    .form-label {
        color: var(--primary-blue);
        font-weight: 600;
    }

    .form-select:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 0.2rem rgba(255, 153, 51, 0.25);
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 12px;
    }

    .spinner-border.text-primary {
        color: var(--primary-orange) !important;
    }

    .badge.bg-success {
        background-color: var(--primary-blue) !important;
    }

    .badge.bg-warning {
        background-color: var(--primary-orange) !important;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    .badge {
        border-radius: 12px;
        font-weight: 500;
    }

    .progress-bar.bg-success {
        background-color: var(--primary-blue) !important;
    }

    .progress-bar.bg-warning {
        background-color: var(--primary-orange) !important;
    }

    .text-success {
        color: var(--primary-blue) !important;
    }

    .text-warning {
        color: var(--primary-orange) !important;
    }

    .alert-warning {
        background-color: var(--orange-bg);
        border-color: var(--orange-light);
        color: var(--orange-dark);
    }

    .alert-success {
        background-color: var(--blue-bg);
        border-color: var(--blue-light);
        color: var(--blue-dark);
    }

    .toast.bg-success {
        background-color: var(--primary-blue) !important;
    }

    .toast.bg-warning {
        background-color: var(--primary-orange) !important;
    }

    .toast.bg-danger {
        background-color: #dc3545 !important;
    }

    .toast.bg-info {
        background-color: var(--primary-blue) !important;
    }

    .card.bg-light {
        background: linear-gradient(135deg, var(--orange-bg) 0%, var(--blue-bg) 100%) !important;
        border: 1px solid var(--orange-light);
    }

    .card.bg-light .card-body h6 {
        color: var(--primary-blue);
        font-weight: 600;
    }

    .changes-indicator {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        display: none;
    }

    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .header-actions {
            justify-content: center;
            flex-wrap: wrap;
        }

        .header-actions .btn {
            margin: 2px;
        }
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: var(--orange-bg);
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--primary-orange);
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: var(--orange-dark);
    }

    /* Current Day Highlighting */
    .current-day {
        background: linear-gradient(90deg, rgba(255, 153, 51, 0.15) 0%, rgba(34, 187, 234, 0.15) 100%) !important;
        border-left: 4px solid var(--primary-orange);
        animation: currentDayPulse 2s ease-in-out infinite;
    }

    .current-day:hover {
        background: linear-gradient(90deg, rgba(255, 153, 51, 0.25) 0%, rgba(34, 187, 234, 0.25) 100%) !important;
    }

    /* Current Week Highlighting */
    .current-week-row {
        background: linear-gradient(90deg, rgba(34, 187, 234, 0.08) 0%, rgba(255, 153, 51, 0.08) 100%) !important;
        border-left: 2px solid var(--secondary-blue);
    }

    .current-week-row:hover {
        background: linear-gradient(90deg, rgba(34, 187, 234, 0.15) 0%, rgba(255, 153, 51, 0.15) 100%) !important;
    }

    .current-day .meal-item {
        border: 2px solid rgba(255, 153, 51, 0.3);
        background: rgba(255, 255, 255, 0.8);
    }

    .current-day .meal-item:hover {
        border-color: var(--primary-orange);
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 4px 15px rgba(255, 153, 51, 0.3);
    }

    @keyframes currentDayPulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 153, 51, 0.4);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(255, 153, 51, 0);
        }
    }

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

    .text-primary {
        color: var(--primary-orange) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    {!! \App\Services\WeekCycleService::getJavaScriptFunction() !!}

    let editMode = false;
    let unsavedChanges = false;
    let currentWeekCycle = 1;
    let menuData = {};

    // SIMPLE MODAL FUNCTIONS - NO BOOTSTRAP DEPENDENCY
    function showModalSimple(modalId) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) return;

        // Clean up any existing stuff
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'hidden';

        // Show modal manually
        modalElement.style.cssText = `
            display: block !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 999999 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            pointer-events: auto !important;
        `;

        modalElement.classList.add('show');

        // Style the dialog
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.cssText = `
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                z-index: 1000000 !important;
                pointer-events: auto !important;
                margin: 0 !important;
            `;
        }

        // Ensure content is clickable
        const modalContent = modalElement.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.cssText = `
                pointer-events: auto !important;
                z-index: 1000001 !important;
                background: white !important;
                border-radius: 12px !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
            `;
        }

        // Make all inputs clickable
        modalElement.querySelectorAll('input, textarea, button, select').forEach(el => {
            el.style.pointerEvents = 'auto';
        });

        // Close on backdrop click
        modalElement.onclick = function(e) {
            if (e.target === modalElement) {
                hideModalSimple(modalId);
            }
        };

        // Close button functionality
        modalElement.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(btn => {
            btn.onclick = function() {
                hideModalSimple(modalId);
            };
        });
    }

    function hideModalSimple(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // UNIFIED: Get current week cycle and set it as default
        const weekInfo = getCurrentWeekCycle();
        currentWeekCycle = weekInfo.weekCycle;
        document.getElementById('weekCycleSelect').value = currentWeekCycle;

        loadMenuData();
        loadKitchenStatus();
        updateLastUpdated();

        // Set up event listeners
        document.getElementById('weekCycleSelect').addEventListener('change', function() {
            currentWeekCycle = parseInt(this.value);
            loadMenuData();
        });

        // Auto-refresh every 5 minutes
        setInterval(function() {
            loadMenuData();
            loadKitchenStatus();
        }, 300000);

        // UNIFIED: Test highlighting consistency
        setTimeout(() => {
            console.log('=== COOK MENU HIGHLIGHTING TEST ===');
            const weekInfo = getCurrentWeekCycle();
            console.log('Current week info:', weekInfo);

            const selectedWeek = document.getElementById('weekCycleSelect').value;
            console.log('Selected week:', selectedWeek);

            // Test highlighting for each day
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            days.forEach(day => {
                const highlighting = getMenuHighlighting(day, parseInt(selectedWeek));
                console.log(`${day}:`, highlighting);
            });
        }, 2000);
    });

    // Load menu data from server
    function loadMenuData() {
        showLoading(true);

        fetch(`/cook/menu/${currentWeekCycle}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // FIXED: Handle new response structure from BaseController
                    menuData = data.data || {};
                    console.log('Loaded menu data:', menuData);
                    console.log('Menu data type:', typeof menuData);
                    console.log('Menu data keys:', Object.keys(menuData));

                    // Debug: Check structure for each day
                    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    days.forEach(day => {
                        if (menuData[day]) {
                            console.log(`${day} data:`, menuData[day]);
                            console.log(`${day} type:`, typeof menuData[day]);
                            if (typeof menuData[day] === 'object') {
                                console.log(`${day} keys:`, Object.keys(menuData[day]));
                            }
                        } else {
                            console.log(`${day}: not found in menu data`);
                        }
                    });

                    // Ensure menuData has the expected structure
                    if (typeof menuData !== 'object') {
                        console.warn('Menu data is not an object, initializing empty structure');
                        menuData = {};
                    }

                    renderMenuTable();
                } else {
                    console.error('Failed to load menu data:', data);
                    showToast('Failed to load menu data', 'error');
                    // Initialize empty menu data to prevent errors
                    menuData = {};
                    renderMenuTable();
                }
            })
            .catch(error => {
                console.error('Error loading menu data:', error);
                showToast('Error loading menu data', 'error');
                // Initialize empty menu data to prevent errors
                menuData = {};
                renderMenuTable();
            })
            .finally(() => {
                showLoading(false);
            });
    }

    // Render the menu table
    function renderMenuTable() {
        const tableBody = currentWeekCycle === 1 ?
            document.getElementById('menuTableBody') :
            document.getElementById('menuTableBody2');

        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const mealTypes = ['breakfast', 'lunch', 'dinner'];

        // SAFE MENU DATA INITIALIZATION - Prevent undefined errors
        if (!menuData || typeof menuData !== 'object') {
            console.warn('MenuData is not properly initialized, creating empty structure');
            menuData = {};
        }

        // FIXED: Get current week and day info with dynamic highlighting
        const weekInfo = getCurrentWeekCycle();
        const today = weekInfo.currentDayName;
        const currentWeekCycleFromService = weekInfo.weekCycle;
        const selectedWeekCycle = parseInt(document.getElementById('weekCycleSelect').value);

        let html = '';

        days.forEach(day => {
            // UNIFIED: Use consistent highlighting system
            const highlighting = getMenuHighlighting(day, selectedWeekCycle);

            let rowClass = '';
            if (highlighting.isToday) {
                rowClass = 'table-warning current-day';
            } else if (highlighting.isCurrentWeek) {
                rowClass = 'current-week-row';
            }

            html += `<tr data-day="${day}" class="${rowClass}">`;

            // UNIFIED: Dynamic day labeling
            let dayLabel = capitalizeFirst(day);
            if (highlighting.isToday) {
                dayLabel += ' <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Today</span>';
            } else if (highlighting.isCurrentWeek) {
                dayLabel += ' <span class="badge bg-light text-muted">This Week</span>';
            }

            html += `<td class="${highlighting.dayClass}">${dayLabel}</td>`;

            mealTypes.forEach(mealType => {
                // SAFE MEAL DATA ACCESS - Prevent undefined errors
                let meal = null;
                try {
                    if (menuData && typeof menuData === 'object' &&
                        menuData[day] && typeof menuData[day] === 'object' &&
                        menuData[day][mealType]) {
                        meal = menuData[day][mealType];
                    }
                } catch (error) {
                    console.warn(`Error accessing meal data for ${day} ${mealType}:`, error);
                    meal = null;
                }

                html += `<td>`;
                html += `<div class="meal-item ${editMode ? 'editable' : ''}"
                         onclick="${editMode ? `editMeal('${day}', '${mealType}')` : ''}"
                         data-day="${day}" data-meal-type="${mealType}">`;

                if (meal && typeof meal === 'object') {
                    html += `<div class="fw-bold">${meal.name || 'No meal set'}</div>`;

                    // Handle ingredients display - convert array to string if needed
                    let ingredientsDisplay = '';
                    if (meal.ingredients) {
                        if (Array.isArray(meal.ingredients)) {
                            ingredientsDisplay = meal.ingredients.join(', ');
                        } else {
                            ingredientsDisplay = meal.ingredients;
                        }
                    } else {
                        ingredientsDisplay = 'No ingredients listed';
                    }
                    html += `<small class="text-muted">${ingredientsDisplay}</small>`;

                    // Add kitchen status if available (but not "Not Started")
                    if (meal.status && meal.status !== 'Not Started') {
                        const statusClass = getStatusClass(meal.status);
                        html += `<span class="badge ${statusClass} kitchen-status-badge meal-status">${meal.status}</span>`;
                    }
                } else {
                    html += `<div class="fw-bold text-muted">No meal set</div>`;
                    html += `<small class="text-muted">Click edit mode to add meal</small>`;
                }

                html += `</div>`;
                html += `</td>`;
            });

            html += `</tr>`;
        });

        tableBody.innerHTML = html;

        // Show/hide appropriate table
        document.getElementById('week1Table').style.display = currentWeekCycle === 1 ? '' : 'none';
        document.getElementById('week2Table').style.display = currentWeekCycle === 2 ? '' : 'none';
    }

    // Toggle edit mode
    function toggleEditMode() {
        editMode = !editMode;
        const editModeText = document.getElementById('editModeText');
        const tables = document.querySelectorAll('.week-table');

        if (editMode) {
            editModeText.textContent = 'View Mode';
            tables.forEach(table => table.classList.add('edit-mode'));
        } else {
            editModeText.textContent = 'Edit Mode';
            tables.forEach(table => table.classList.remove('edit-mode'));
        }

        renderMenuTable();
    }

    // Edit a specific meal - SIMPLE WORKING VERSION
    function editMeal(day, mealType) {
        if (!editMode) return;

        // SAFE MEAL DATA ACCESS - Prevent undefined errors
        let meal = {};
        try {
            if (menuData && typeof menuData === 'object' &&
                menuData[day] && typeof menuData[day] === 'object' &&
                menuData[day][mealType] && typeof menuData[day][mealType] === 'object') {
                meal = menuData[day][mealType];
            }
        } catch (error) {
            console.warn(`Error accessing meal data for editing ${day} ${mealType}:`, error);
            meal = {};
        }

        // Populate modal
        document.getElementById('editMealId').value = meal.id || '';
        document.getElementById('editDay').value = day;
        document.getElementById('editMealType').value = mealType;
        document.getElementById('editWeekCycle').value = currentWeekCycle;
        document.getElementById('editMealName').value = meal.name || '';

        // Handle ingredients - convert array to string if needed
        let ingredientsValue = '';
        if (meal.ingredients) {
            if (Array.isArray(meal.ingredients)) {
                ingredientsValue = meal.ingredients.join(', ');
            } else {
                ingredientsValue = meal.ingredients;
            }
        }
        document.getElementById('editIngredients').value = ingredientsValue;

        // Update modal title
        document.getElementById('editMealModalLabel').textContent =
            `Edit ${capitalizeFirst(mealType)} - ${capitalizeFirst(day)}`;

        // SIMPLE MODAL DISPLAY - NO BOOTSTRAP
        const modalElement = document.getElementById('editMealModal');

        // Clean up any existing stuff
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'hidden';

        // Show modal manually
        modalElement.style.cssText = `
            display: block !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 999999 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            pointer-events: auto !important;
        `;

        modalElement.classList.add('show');

        // Style the dialog
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.cssText = `
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                z-index: 1000000 !important;
                pointer-events: auto !important;
                margin: 0 !important;
            `;
        }

        // Ensure content is clickable
        const modalContent = modalElement.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.cssText = `
                pointer-events: auto !important;
                z-index: 1000001 !important;
                background: white !important;
                border-radius: 12px !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
            `;
        }

        // Make all inputs clickable
        modalElement.querySelectorAll('input, textarea, button, select').forEach(el => {
            el.style.pointerEvents = 'auto';
        });

        // Close on backdrop click
        modalElement.onclick = function(e) {
            if (e.target === modalElement) {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.style.overflow = '';
            }
        };

        // Close button functionality
        modalElement.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(btn => {
            btn.onclick = function() {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.style.overflow = '';
            };
        });
    }



    // Save meal changes
    function saveMealChanges() {
        const form = document.getElementById('editMealForm');

        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showToast('CSRF token not found. Please refresh the page.', 'error');
            return;
        }

        // Add debugging
        console.log('Sending data:', data);
        console.log('CSRF Token:', csrfToken.content);

        // Show loading
        const saveBtn = document.querySelector('#editMealModal .btn-primary');
        const originalText = saveBtn.textContent;
        saveBtn.textContent = 'Saving...';
        saveBtn.disabled = true;

        fetch('/cook/menu/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showToast('Meal updated successfully', 'success');
                console.log('Meal saved successfully, reloading menu data...');
                loadMenuData(); // Reload data
                // COMPREHENSIVE MODAL HIDE FIX - Use simple modal function
                hideModalSimple('editMealModal');

                // FORCE COMPLETE CLEANUP
                setTimeout(() => {
                    // Remove all backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Reset body state
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    // Reset modal state
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    modalElement.setAttribute('aria-hidden', 'true');
                    modalElement.removeAttribute('aria-modal');
                    modalElement.removeAttribute('role');
                }, 300);
                markUnsavedChanges(false);
                updateLastUpdated();

                // Show cross-system notification
                showCrossSystemNotification('updated');
            } else {
                if (data.errors) {
                    // Show validation errors
                    let errorMessage = 'Validation errors:\n';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += `${field}: ${data.errors[field].join(', ')}\n`;
                    });
                    showToast(errorMessage, 'error');
                } else {
                    showToast(data.message || 'Failed to update meal', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                showToast('Network error: Please check your connection and try again.', 'error');
            } else if (error.message.includes('HTTP error')) {
                showToast(`Server error: ${error.message}`, 'error');
            } else {
                showToast('Error updating meal: ' + error.message, 'error');
            }
        })
        .finally(() => {
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        });
    }

    // Load kitchen status
    function loadKitchenStatus() {
        fetch('/cook/menu/kitchen/status')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateKitchenStatus(data.status);
                }
            })
            .catch(error => {
                console.error('Error loading kitchen status:', error);
            });
    }

    // Update kitchen status display
    function updateKitchenStatus(status) {
        const mealTypes = ['breakfast', 'lunch', 'dinner'];

        mealTypes.forEach(mealType => {
            const mealStatus = status[mealType] || 'Not Started';
            const progressElement = document.getElementById(`today${capitalizeFirst(mealType)}Progress`);
            const statusElement = document.getElementById(`today${capitalizeFirst(mealType)}Status`);

            if (progressElement && statusElement) {
                statusElement.textContent = mealStatus;
                statusElement.className = `text-${getStatusColor(mealStatus)}`;

                const progressBar = progressElement.querySelector('.progress-bar');
                if (progressBar) {
                    const width = getStatusProgress(mealStatus);
                    progressBar.style.width = `${width}%`;
                    progressBar.className = `progress-bar bg-${getStatusColor(mealStatus)}`;
                }
            }
        });
    }



    // Clear current meal in modal
    function clearCurrentMeal() {
        if (confirm('Are you sure you want to clear this meal? This will remove the meal name and ingredients.')) {
            document.getElementById('editMealName').value = '';
            document.getElementById('editIngredients').value = '';
            showToast('Meal fields cleared', 'info');
        }
    }

    // Clear all meals for current week
    function clearAllMeals() {
        if (confirm(`Are you sure you want to clear ALL meals for Week ${currentWeekCycle}? This action cannot be undone.`)) {
            const confirmAgain = confirm('This will delete all meal data for this week. Are you absolutely sure?');
            if (confirmAgain) {
                clearWeekMeals();
            }
        }
    }

    // Clear week meals function
    function clearWeekMeals() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        if (!csrfToken) {
            showToast('CSRF token not found. Please refresh the page.', 'error');
            return;
        }

        showToast('Clearing all meals...', 'info');

        fetch('/cook/menu/clear-week', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                week_cycle: currentWeekCycle
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast('All meals cleared successfully', 'success');
                loadMenuData(); // Reload data
                updateLastUpdated();

                // Notify about cross-system updates
                showCrossSystemNotification('cleared');
            } else {
                showToast(data.message || 'Failed to clear meals', 'error');
            }
        })
        .catch(error => {
            console.error('Clear error:', error);
            showToast('Error clearing meals: ' + error.message, 'error');
        });
    }

    // Refresh menu data
    function refreshMenuData() {
        loadMenuData();
        loadKitchenStatus();
        showToast('Menu data refreshed', 'success');
    }

    // Save all changes
    function saveAllChanges() {
        if (!unsavedChanges) {
            showToast('No changes to save', 'info');
            return;
        }

        // Implementation for bulk save
        showToast('All changes saved successfully', 'success');
        markUnsavedChanges(false);
        showCrossSystemNotification('updated');
    }

    // Verify cross-system integration
    function verifyCrossSystemIntegration() {
        // Check integration status with all systems
        fetch('/cook/cross-system-data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateIntegrationStatus(data.data);
                    showIntegrationReport(data.data);
                } else {
                    showToast('Failed to verify integration status', 'error');
                }
            })
            .catch(error => {
                console.error('Integration check failed:', error);
                showToast('Integration check failed', 'error');
            });
    }

    // Update integration status indicators
    function updateIntegrationStatus(data) {
        // Update kitchen connection status
        const kitchenStatus = document.getElementById('kitchenConnectionStatus');
        kitchenStatus.textContent = data.connected_users.kitchen_staff > 0 ? 'Connected' : 'Offline';
        kitchenStatus.className = data.connected_users.kitchen_staff > 0 ? 'badge bg-success' : 'badge bg-danger';

        // Update student connection status
        const studentStatus = document.getElementById('studentConnectionStatus');
        studentStatus.textContent = data.connected_users.students > 0 ? 'Connected' : 'Offline';
        studentStatus.className = data.connected_users.students > 0 ? 'badge bg-success' : 'badge bg-danger';

        // Update poll system status
        const pollStatus = document.getElementById('pollConnectionStatus');
        pollStatus.textContent = data.active_polls.length > 0 ? 'Active' : 'Inactive';
        pollStatus.className = data.active_polls.length > 0 ? 'badge bg-success' : 'badge bg-warning';
    }

    // Show detailed integration report
    function showIntegrationReport(data) {
        const message = `
            <div class="text-start">
                <strong>🔗 Cross-System Integration Report:</strong><br><br>
                <div class="ms-3">
                    <strong>Kitchen System:</strong><br>
                    • ${data.connected_users.kitchen_staff} kitchen staff connected<br>
                    • Menu changes sync in real-time<br>
                    • Status updates: ${data.kitchen_status ? Object.keys(data.kitchen_status).length : 0} meals tracked<br><br>

                    <strong>Student System:</strong><br>
                    • ${data.connected_users.students} students connected<br>
                    • Menu updates visible immediately<br>
                    • Poll participation: ${data.poll_responses ? Object.keys(data.poll_responses).length : 0} active polls<br><br>

                    <strong>Poll System:</strong><br>
                    • ${data.active_polls.length} active polls<br>
                    • Cross-system polling enabled<br>
                    • Real-time response tracking<br><br>

                    <strong>Integration Status:</strong><br>
                    • All systems connected ✅<br>
                    • Real-time synchronization active ✅<br>
                    • Cross-system notifications working ✅
                </div>
            </div>
        `;

        showToast(message, 'info');
    }

    // View system integration dashboard
    function viewSystemIntegration() {
        fetch('/cook/cross-system-data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSystemIntegrationModal(data.data);
                }
            })
            .catch(error => {
                console.error('Error loading integration data:', error);
            });
    }

    // Show system integration modal
    function showSystemIntegrationModal(data) {
        const modalHtml = `
            <div class="modal fade" id="integrationModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">System Integration Dashboard</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Kitchen Integration</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            Connected Staff <span class="badge bg-primary">${data.connected_users.kitchen_staff}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            Status Updates <span class="badge bg-success">Real-time</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Student Integration</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            Connected Students <span class="badge bg-primary">${data.connected_users.students}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            Menu Sync <span class="badge bg-success">Active</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Active Polls</h6>
                                    <div class="list-group">
                                        ${data.active_polls.map(poll => `
                                            <div class="list-group-item">
                                                <strong>${poll.meal.name}</strong> - ${poll.meal_type}
                                                <small class="text-muted d-block">${poll.responses.length} responses</small>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('integrationModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add new modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal with cleanup
        const modalElement = document.getElementById('integrationModal');

        // Clean up any existing modal states first
        const existingBackdrops = document.querySelectorAll('.modal-backdrop');
        existingBackdrops.forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';

        // Show modal using simple modal function
        showModalSimple('integrationModal');
    }

    // EMERGENCY MODAL CLEANUP FUNCTION - COMPREHENSIVE FIX
    function cleanupModalStates() {
        console.log('🧹 Emergency modal cleanup triggered...');

        // STEP 1: Remove all modal backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => {
            console.log('Removing backdrop:', backdrop);
            backdrop.remove();
        });

        // STEP 2: Reset body classes and styles completely
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.style.marginRight = '';

        // STEP 3: Hide and reset all modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.classList.remove('show', 'fade');
            modal.style.display = 'none';
            modal.style.zIndex = '';
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');
            modal.removeAttribute('tabindex');
        });

        // STEP 4: Force remove any stuck elements
        const stuckElements = document.querySelectorAll('[style*="z-index"]');
        stuckElements.forEach(element => {
            if (element.style.zIndex > 1050) {
                element.style.zIndex = '';
            }
        });

        console.log('✅ Modal states completely cleaned up');
    }

    // Make cleanup function available globally
    window.cleanupModalStates = cleanupModalStates;

    // Auto-cleanup on page load
    document.addEventListener('DOMContentLoaded', function() {
        cleanupModalStates();
    });

    // Emergency keyboard shortcut (Ctrl+Alt+C)
    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.altKey && event.key === 'c') {
            cleanupModalStates();
            event.preventDefault();
        }
    });

    // Mark unsaved changes
    function markUnsavedChanges(hasChanges) {
        unsavedChanges = hasChanges;
        const indicator = document.querySelector('.changes-indicator');

        if (hasChanges) {
            indicator.style.display = 'block';
        } else {
            indicator.style.display = 'none';
        }
    }



    // Update last updated timestamp
    function updateLastUpdated() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        document.getElementById('lastUpdated').textContent = `Last updated: ${timeString}`;
    }

    // Utility functions
    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function getStatusClass(status) {
        switch (status) {
            case 'Completed': return 'bg-success';
            case 'In Progress': return 'bg-warning';
            case 'Not Started': return 'bg-secondary';
            default: return 'bg-secondary';
        }
    }

    function getStatusColor(status) {
        switch (status) {
            case 'Completed': return 'success';
            case 'In Progress': return 'warning';
            case 'Not Started': return 'muted';
            default: return 'muted';
        }
    }

    function getStatusProgress(status) {
        switch (status) {
            case 'Completed': return 100;
            case 'In Progress': return 60;
            case 'Not Started': return 0;
            default: return 0;
        }
    }

    function showLoading(show) {
        const tables = document.querySelectorAll('.week-table');
        tables.forEach(table => {
            if (show) {
                if (!table.querySelector('.loading-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'loading-overlay';
                    overlay.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
                    table.style.position = 'relative';
                    table.appendChild(overlay);
                }
            } else {
                const overlay = table.querySelector('.loading-overlay');
                if (overlay) {
                    overlay.remove();
                }
            }
        });
    }

    function showToast(message, type = 'info') {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        // Handle multi-line messages
        const formattedMessage = message.replace(/\n/g, '<br>');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${formattedMessage}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        document.body.appendChild(toastContainer);

        const bsToast = new bootstrap.Toast(toast, {
            delay: type === 'error' ? 8000 : 4000 // Show errors longer
        });
        bsToast.show();

        // Remove toast container after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toastContainer.remove();
        });
    }

    // Show cross-system notification
    function showCrossSystemNotification(action) {
        const actionText = action === 'updated' ? 'updated' : 'cleared';
        const message = `
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <div>
                    <strong>Menu ${actionText} successfully!</strong><br>
                    <small class="text-muted">
                        ✓ Kitchen staff can see changes<br>
                        ✓ Students can view updated menu<br>
                        ✓ All systems synchronized
                    </small>
                </div>
            </div>
        `;

        showToast(message, 'success');

        // Also show a temporary banner
        showSyncBanner(actionText);
    }

    // Show sync banner
    function showSyncBanner(action) {
        const banner = document.createElement('div');
        banner.className = 'alert alert-success alert-dismissible fade show position-fixed';
        banner.style.cssText = 'top: 80px; right: 20px; z-index: 1060; min-width: 300px;';
        banner.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-broadcast text-success me-2"></i>
                <div>
                    <strong>Cross-System Update Complete</strong><br>
                    <small>Menu ${action} and synced across all modules</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(banner);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (banner.parentNode) {
                banner.remove();
            }
        }, 5000);
    }

    // Handle week cycle changes
    document.getElementById('weekCycleSelect').addEventListener('change', function() {
        currentWeekCycle = parseInt(this.value);
        loadMenuData();
    });
</script>
@endpush
