@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Weekly Menu Plan</h2>
            <div class="text-muted" id="current-time"></div>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary" data-cycle="1">Week 1 & 3</button>
                <button type="button" class="btn btn-primary" data-cycle="2">Week 2 & 4</button>
            </div>
        </div>
    </div>

    <!-- Week 1 & 3 Menu -->
    <div class="menu-cycle active" id="cycle-1">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Day</th>
                        <th>Breakfast</th>
                        <th>Lunch</th>
                        <th>Dinner</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <tr data-day="{{ $day }}">
                        <td><strong>{{ $day }}</strong></td>
                        @foreach(['breakfast', 'lunch', 'dinner'] as $mealType)
                        <td>
                            <div class="meal-item" data-day="{{ $day }}" data-meal="{{ $mealType }}" data-cycle="1">
                                <div class="meal-display">
                                    <span class="meal-name">{{ isset($menus[1][$day][$mealType]) ? $menus[1][$day][$mealType]['name'] : 'Not set' }}</span>
                                    <small class="d-block text-muted">{{ isset($menus[1][$day][$mealType]) ? $menus[1][$day][$mealType]['ingredients'] : '' }}</small>
                                </div>
                                <div class="meal-edit d-none">
                                    <input type="text" class="form-control mb-1" placeholder="Meal name">
                                    <input type="text" class="form-control mb-1" placeholder="Ingredients (comma-separated)">
                                </div>
                            </div>
                        </td>
                        @endforeach
                        <td>
                            <div class="d-flex flex-column gap-2">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-meal" data-meal-type="breakfast">Breakfast</button>
                                    <button class="btn btn-sm btn-outline-primary edit-meal" data-meal-type="lunch">Lunch</button>
                                    <button class="btn btn-sm btn-outline-primary edit-meal" data-meal-type="dinner">Dinner</button>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success save-meal d-none w-50" data-meal-type="">💾 Save</button>
                                    <button class="btn btn-sm btn-danger cancel-meal d-none w-50" data-meal-type="">❌ Cancel</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Week 2 & 4 Menu -->
    <div class="menu-cycle d-none" id="cycle-2">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Day</th>
                        <th>Breakfast</th>
                        <th>Lunch</th>
                        <th>Dinner</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <tr data-day="{{ $day }}">
                        <td><strong>{{ $day }}</strong></td>
                        @foreach(['breakfast', 'lunch', 'dinner'] as $mealType)
                        <td>
                            <div class="meal-item" data-day="{{ $day }}" data-meal="{{ $mealType }}" data-cycle="2">
                                <div class="meal-display">
                                    <span class="meal-name">{{ isset($menus[2][$day][$mealType]) ? $menus[2][$day][$mealType]['name'] : 'Not set' }}</span>
                                    <small class="d-block text-muted">{{ isset($menus[2][$day][$mealType]) ? $menus[2][$day][$mealType]['ingredients'] : '' }}</small>
                                </div>
                                <div class="meal-edit d-none">
                                    <input type="text" class="form-control mb-1" placeholder="Meal name">
                                    <input type="text" class="form-control mb-1" placeholder="Ingredients (comma-separated)">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary edit-meal">Edit</button>
                                        <button class="btn btn-sm btn-success save-meal d-none">Save</button>
                                        <button class="btn btn-sm btn-danger cancel-meal d-none">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        @endforeach
                        <td>
                            <div class="d-flex flex-column gap-2">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-meal" data-meal-type="breakfast">Breakfast</button>
                                    <button class="btn btn-sm btn-outline-primary edit-meal" data-meal-type="lunch">Lunch</button>
                                    <button class="btn btn-sm btn-outline-primary edit-meal" data-meal-type="dinner">Dinner</button>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success save-meal d-none w-50" data-meal-type="">💾 Save</button>
                                    <button class="btn btn-sm btn-danger cancel-meal d-none w-50" data-meal-type="">❌ Cancel</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .meal-item {
        min-height: 80px;
    }
    .table-warning {
        background-color: #fff3cd !important;
    }
</style>
@endpush

@push('scripts')
<style>
    .current-day {
        background-color: #ffc107 !important;
    }
    
    .menu-cycle {
        display: none;
    }
    
    .menu-cycle.active {
        display: block;
    }
</style>
<script>
    // Track if user has manually selected a cycle
    let userSelectedCycle = null;

    function updateDisplay() {
        const now = new Date();
        const weekNumber = Math.ceil((((now - new Date(now.getFullYear(), 0, 1)) / 86400000) + 1) / 7);
        const systemWeek = weekNumber % 2 === 1 ? 1 : 2; // Odd weeks -> cycle 1, Even weeks -> cycle 2
        const currentDay = now.toLocaleDateString('en-US', { weekday: 'long' });

        // Use user selection if available, otherwise use system calculation
        const currentWeek = userSelectedCycle || systemWeek;

        // Update time display
        document.getElementById('current-time').textContent = now.toLocaleString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        }) + ` (Week ${weekNumber}, System Cycle: ${systemWeek})`;

        // Only auto-update cycle if user hasn't selected one
        if (!userSelectedCycle) {
            document.getElementById('cycle-1').classList.toggle('active', currentWeek === 1);
            document.getElementById('cycle-2').classList.toggle('active', currentWeek === 2);

            // Update buttons
            document.querySelectorAll('[data-cycle]').forEach(btn => {
                btn.classList.toggle('active', parseInt(btn.dataset.cycle) === currentWeek);
            });
        }

        // Always update the day highlight
        document.querySelectorAll('tr').forEach(row => {
            row.classList.remove('current-day');
            const dayCell = row.querySelector('td strong');
            if (dayCell && dayCell.textContent === currentDay) {
                row.classList.add('current-day');
            }
        });
    }

    // Add click handlers for cycle buttons
    document.querySelectorAll('[data-cycle]').forEach(btn => {
        btn.addEventListener('click', function() {
            const cycle = parseInt(this.dataset.cycle);
            
            // Store user selection
            userSelectedCycle = cycle;
            
            // Update UI
            document.getElementById('cycle-1').classList.toggle('active', cycle === 1);
            document.getElementById('cycle-2').classList.toggle('active', cycle === 2);
            document.querySelectorAll('[data-cycle]').forEach(b => {
                b.classList.toggle('active', parseInt(b.dataset.cycle) === cycle);
            });
        });
    });

    // Update immediately and then every second
    updateDisplay();
    setInterval(updateDisplay, 1000);



    // Week cycle switching
    document.querySelectorAll('[data-cycle]').forEach(button => {
        button.addEventListener('click', function() {
            const cycle = this.dataset.cycle;
            document.querySelectorAll('.menu-cycle').forEach(div => div.classList.add('d-none'));
            document.getElementById(`cycle-${cycle}`).classList.remove('d-none');
            document.querySelectorAll('[data-cycle]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Meal editing functionality
    document.querySelectorAll('.edit-meal').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const mealType = this.dataset.mealType;
            const mealItem = row.querySelector(`[data-meal="${mealType}"]`);
            const display = mealItem.querySelector('.meal-display');
            const edit = mealItem.querySelector('.meal-edit');
            const nameInput = edit.querySelector('input:first-child');
            const ingredientsInput = edit.querySelector('input:nth-child(2)');
            
            // Hide all edit buttons
            row.querySelectorAll('.edit-meal').forEach(btn => btn.classList.add('d-none'));
            
            // Show save and cancel buttons and set their meal type
            const saveButton = row.querySelector('.save-meal');
            const cancelButton = row.querySelector('.cancel-meal');
            saveButton.dataset.mealType = mealType;
            cancelButton.dataset.mealType = mealType;
            saveButton.classList.remove('d-none');
            cancelButton.classList.remove('d-none');

            // Set input values
            nameInput.value = display.querySelector('.meal-name').textContent.trim();
            ingredientsInput.value = display.querySelector('small').textContent.trim();

            // Show edit form
            display.classList.add('d-none');
            edit.classList.remove('d-none');
        });
    });

    document.querySelectorAll('.save-meal').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const mealType = this.dataset.mealType;
            const mealItem = row.querySelector(`[data-meal="${mealType}"]`);
            const display = mealItem.querySelector('.meal-display');
            const edit = mealItem.querySelector('.meal-edit');
            const nameInput = edit.querySelector('input:first-child');
            const ingredientsInput = edit.querySelector('input:nth-child(2)');
            const editButtons = row.querySelectorAll('.edit-meal');

            // Save to database via AJAX
            fetch('/cook/menu/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    day: mealItem.dataset.day,
                    meal_type: mealItem.dataset.meal,
                    cycle: mealItem.dataset.cycle,
                    name: nameInput.value,
                    ingredients: ingredientsInput.value
                })
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).then(data => {
                if (data.success) {
                    display.querySelector('.meal-name').textContent = nameInput.value;
                    display.querySelector('small').textContent = ingredientsInput.value;
                    display.classList.remove('d-none');
                    edit.classList.add('d-none');
                    this.classList.add('d-none');
                    row.querySelector('.cancel-meal').classList.add('d-none');
                    editButtons.forEach(btn => btn.classList.remove('d-none'));
                    // Show success message
                    alert('Menu updated successfully!');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Failed to update menu. Please try again.');
            });
        });
    });

    document.querySelectorAll('.cancel-meal').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const mealType = this.dataset.mealType;
            const mealItem = row.querySelector(`[data-meal="${mealType}"]`);
            const display = mealItem.querySelector('.meal-display');
            const edit = mealItem.querySelector('.meal-edit');
            const editButtons = row.querySelectorAll('.edit-meal');

            display.classList.remove('d-none');
            edit.classList.add('d-none');
            this.classList.add('d-none');
            row.querySelector('.save-meal').classList.add('d-none');
            editButtons.forEach(btn => btn.classList.remove('d-none'));
        });
    });
</script>
@endpush

@endsection
