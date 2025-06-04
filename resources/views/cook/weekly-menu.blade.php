@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Weekly Menu Management</h2>
                    <p class="text-muted" style="color: white;">Create and manage weekly meal plans</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Menu Plan Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Weekly Menu Plan</h5>
                    <div>
                        <select id="weekCycleFilter" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="1">Week 1 & 3</option>
                            <option value="2">Week 2 & 4</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="week1" class="week-cycle">
                        <h5 class="mb-3">Week 1 & 3 Menu</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th width="28%">Breakfast</th>
                                        <th width="28%">Lunch</th>
                                        <th width="28%">Dinner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                        <tr>
                                            <td class="fw-bold">{{ ucfirst($day) }}</td>
                                            <td>
                                                @if(isset($week1Menus[$day]['breakfast']))
                                                    <div class="meal-item">
                                                        <div class="fw-bold">{{ $week1Menus[$day]['breakfast']->name }}</div>
                                                        <div class="small text-muted">{{ $week1Menus[$day]['breakfast']->description }}</div>
                                                        <div class="mt-1">
                                                            <span class="badge bg-success">₱{{ number_format($week1Menus[$day]['breakfast']->price, 2) }}</span>
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-meal-btn" 
                                                                data-id="{{ $week1Menus[$day]['breakfast']->id }}"
                                                                data-name="{{ $week1Menus[$day]['breakfast']->name }}"
                                                                data-description="{{ $week1Menus[$day]['breakfast']->description }}"
                                                                data-price="{{ $week1Menus[$day]['breakfast']->price }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted">No menu set</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($week1Menus[$day]['lunch']))
                                                    <div class="meal-item">
                                                        <div class="fw-bold">{{ $week1Menus[$day]['lunch']->name }}</div>
                                                        <div class="small text-muted">{{ $week1Menus[$day]['lunch']->description }}</div>
                                                        <div class="mt-1">
                                                            <span class="badge bg-success">₱{{ number_format($week1Menus[$day]['lunch']->price, 2) }}</span>
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-meal-btn"
                                                                data-id="{{ $week1Menus[$day]['lunch']->id }}"
                                                                data-name="{{ $week1Menus[$day]['lunch']->name }}"
                                                                data-description="{{ $week1Menus[$day]['lunch']->description }}"
                                                                data-price="{{ $week1Menus[$day]['lunch']->price }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted">No menu set</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($week1Menus[$day]['dinner']))
                                                    <div class="meal-item">
                                                        <div class="fw-bold">{{ $week1Menus[$day]['dinner']->name }}</div>
                                                        <div class="small text-muted">{{ $week1Menus[$day]['dinner']->description }}</div>
                                                        <div class="mt-1">
                                                            <span class="badge bg-success">₱{{ number_format($week1Menus[$day]['dinner']->price, 2) }}</span>
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-meal-btn"
                                                                data-id="{{ $week1Menus[$day]['dinner']->id }}"
                                                                data-name="{{ $week1Menus[$day]['dinner']->name }}"
                                                                data-description="{{ $week1Menus[$day]['dinner']->description }}"
                                                                data-price="{{ $week1Menus[$day]['dinner']->price }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted">No menu set</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="week2" class="week-cycle" style="display: none;">
                        <h5 class="mb-3">Week 2 & 4 Menu</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th width="28%">Breakfast</th>
                                        <th width="28%">Lunch</th>
                                        <th width="28%">Dinner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                        <tr>
                                            <td class="fw-bold">{{ ucfirst($day) }}</td>
                                            <td>
                                                @if(isset($week2Menus[$day]['breakfast']))
                                                    <div class="meal-item">
                                                        <div class="fw-bold">{{ $week2Menus[$day]['breakfast']->name }}</div>
                                                        <div class="small text-muted">{{ $week2Menus[$day]['breakfast']->description }}</div>
                                                        <div class="mt-1">
                                                            <span class="badge bg-success">₱{{ number_format($week2Menus[$day]['breakfast']->price, 2) }}</span>
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-meal-btn"
                                                                data-id="{{ $week2Menus[$day]['breakfast']->id }}"
                                                                data-name="{{ $week2Menus[$day]['breakfast']->name }}"
                                                                data-description="{{ $week2Menus[$day]['breakfast']->description }}"
                                                                data-price="{{ $week2Menus[$day]['breakfast']->price }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted">No menu set</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($week2Menus[$day]['lunch']))
                                                    <div class="meal-item">
                                                        <div class="fw-bold">{{ $week2Menus[$day]['lunch']->name }}</div>
                                                        <div class="small text-muted">{{ $week2Menus[$day]['lunch']->description }}</div>
                                                        <div class="mt-1">
                                                            <span class="badge bg-success">₱{{ number_format($week2Menus[$day]['lunch']->price, 2) }}</span>
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-meal-btn"
                                                                data-id="{{ $week2Menus[$day]['lunch']->id }}"
                                                                data-name="{{ $week2Menus[$day]['lunch']->name }}"
                                                                data-description="{{ $week2Menus[$day]['lunch']->description }}"
                                                                data-price="{{ $week2Menus[$day]['lunch']->price }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted">No menu set</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($week2Menus[$day]['dinner']))
                                                    <div class="meal-item">
                                                        <div class="fw-bold">{{ $week2Menus[$day]['dinner']->name }}</div>
                                                        <div class="small text-muted">{{ $week2Menus[$day]['dinner']->description }}</div>
                                                        <div class="mt-1">
                                                            <span class="badge bg-success">₱{{ number_format($week2Menus[$day]['dinner']->price, 2) }}</span>
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-meal-btn"
                                                                data-id="{{ $week2Menus[$day]['dinner']->id }}"
                                                                data-name="{{ $week2Menus[$day]['dinner']->name }}"
                                                                data-description="{{ $week2Menus[$day]['dinner']->description }}"
                                                                data-price="{{ $week2Menus[$day]['dinner']->price }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted">No menu set</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editDayMenuModal">
                            <i class="bi bi-pencil me-1"></i> Edit Day Menu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Meal Modal -->
<div class="modal fade" id="editMealModal" tabindex="-1" aria-labelledby="editMealModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMealModalLabel">Edit Meal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMealForm">
                    @csrf
                    <input type="hidden" id="mealId" name="id">
                    <div class="mb-3">
                        <label for="mealName" class="form-label">Meal Name</label>
                        <input type="text" class="form-control" id="mealName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="mealDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="mealDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="mealPrice" class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" id="mealPrice" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="mealAvailable" name="is_available" checked>
                        <label class="form-check-label" for="mealAvailable">Available</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveMealBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Day Menu Modal -->
<div class="modal fade" id="editDayMenuModal" tabindex="-1" aria-labelledby="editDayMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDayMenuModalLabel">Edit Day Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDayMenuForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editWeekCycle" class="form-label">Week Cycle</label>
                            <select class="form-select" id="editWeekCycle" name="week_cycle" required>
                                <option value="1">Week 1 & 3</option>
                                <option value="2">Week 2 & 4</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editDay" class="form-label">Day</label>
                            <select class="form-select" id="editDay" name="day_of_week" required>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Breakfast</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="breakfastName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="breakfastName" name="breakfast[name]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="breakfastPrice" class="form-label">Price (₱)</label>
                                <input type="number" class="form-control" id="breakfastPrice" name="breakfast[price]" step="0.01" min="0" required>
                            </div>
                            <div class="col-12">
                                <label for="breakfastDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="breakfastDescription" name="breakfast[description]" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Lunch</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="lunchName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="lunchName" name="lunch[name]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="lunchPrice" class="form-label">Price (₱)</label>
                                <input type="number" class="form-control" id="lunchPrice" name="lunch[price]" step="0.01" min="0" required>
                            </div>
                            <div class="col-12">
                                <label for="lunchDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="lunchDescription" name="lunch[description]" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Dinner</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="dinnerName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="dinnerName" name="dinner[name]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="dinnerPrice" class="form-label">Price (₱)</label>
                                <input type="number" class="form-control" id="dinnerPrice" name="dinner[price]" step="0.01" min="0" required>
                            </div>
                            <div class="col-12">
                                <label for="dinnerDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="dinnerDescription" name="dinner[description]" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDayMenuBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

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
    
    // Update the time every minute
    updateDateTime();
    setInterval(updateDateTime, 60000);
    
    // Week cycle filter
    document.getElementById('weekCycleFilter').addEventListener('change', function() {
        const weekCycle = this.value;
        if (weekCycle === '1') {
            document.getElementById('week1').style.display = 'block';
            document.getElementById('week2').style.display = 'none';
        } else {
            document.getElementById('week1').style.display = 'none';
            document.getElementById('week2').style.display = 'block';
        }
    });
    
    // Edit meal button click
    document.querySelectorAll('.edit-meal-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            const price = this.getAttribute('data-price');
            
            document.getElementById('mealId').value = id;
            document.getElementById('mealName').value = name;
            document.getElementById('mealDescription').value = description;
            document.getElementById('mealPrice').value = price;
            
            const editMealModal = new bootstrap.Modal(document.getElementById('editMealModal'));
            editMealModal.show();
        });
    });
    
    // Save meal changes
    document.getElementById('saveMealBtn').addEventListener('click', function() {
        const form = document.getElementById('editMealForm');
        const formData = new FormData(form);
        
        fetch('{{ route("cook.weekly-menu.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const editMealModal = bootstrap.Modal.getInstance(document.getElementById('editMealModal'));
                editMealModal.hide();
                
                // Show success message
                alert('Meal updated successfully');
                
                // Reload the page to show updated data
                window.location.reload();
            } else {
                alert('Error updating meal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the meal');
        });
    });
    
    // Save day menu changes
    document.getElementById('saveDayMenuBtn').addEventListener('click', function() {
        const form = document.getElementById('editDayMenuForm');
        const formData = new FormData(form);
        
        fetch('{{ route("cook.weekly-menu.update-day") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const editDayMenuModal = bootstrap.Modal.getInstance(document.getElementById('editDayMenuModal'));
                editDayMenuModal.hide();
                
                // Show success message
                alert('Day menu updated successfully');
                
                // Reload the page to show updated data
                window.location.reload();
            } else {
                alert('Error updating day menu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the day menu');
        });
    });
</script>
@endpush
