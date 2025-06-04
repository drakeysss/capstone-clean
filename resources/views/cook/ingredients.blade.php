@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Ingredients Management</h2>
                    <p class="text-muted" style="color: white;">Manage ingredients for your meals</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ingredients Management Section -->
    <div class="row">
        <div class="col-xl-4 mb-4">
            <div class="card main-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Add New Ingredient</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cook.ingredients.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Ingredient Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select category</option>
                                <option value="vegetable">Vegetable</option>
                                <option value="fruit">Fruit</option>
                                <option value="meat">Meat</option>
                                <option value="dairy">Dairy</option>
                                <option value="grain">Grain</option>
                                <option value="spice">Spice</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit of Measurement</label>
                            <select class="form-select" id="unit" name="unit" required>
                                <option value="">Select unit</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="g">Gram (g)</option>
                                <option value="l">Liter (l)</option>
                                <option value="ml">Milliliter (ml)</option>
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="bunch">Bunch</option>
                                <option value="pack">Pack</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Ingredient</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Ingredients List</h5>
                    <div>
                        <select id="categoryFilter" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="all">All Categories</option>
                            <option value="vegetable">Vegetable</option>
                            <option value="fruit">Fruit</option>
                            <option value="meat">Meat</option>
                            <option value="dairy">Dairy</option>
                            <option value="grain">Grain</option>
                            <option value="spice">Spice</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ingredients ?? [] as $ingredient)
                                <tr class="ingredient-item" data-category="{{ $ingredient->category }}">
                                    <td>{{ $ingredient->name }}</td>
                                    <td>{{ ucfirst($ingredient->category) }}</td>
                                    <td>{{ $ingredient->unit }}</td>
                                    <td>{{ Str::limit($ingredient->description, 30) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-ingredient-btn" 
                                                data-id="{{ $ingredient->id }}"
                                                data-name="{{ $ingredient->name }}"
                                                data-category="{{ $ingredient->category }}"
                                                data-unit="{{ $ingredient->unit }}"
                                                data-description="{{ $ingredient->description }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('cook.ingredients.delete', $ingredient->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this ingredient?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No ingredients found</td>
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

<!-- Edit Ingredient Modal -->
<div class="modal fade" id="editIngredientModal" tabindex="-1" aria-labelledby="editIngredientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIngredientModalLabel">Edit Ingredient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editIngredientForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Ingredient Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Category</label>
                        <select class="form-select" id="edit_category" name="category" required>
                            <option value="vegetable">Vegetable</option>
                            <option value="fruit">Fruit</option>
                            <option value="meat">Meat</option>
                            <option value="dairy">Dairy</option>
                            <option value="grain">Grain</option>
                            <option value="spice">Spice</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_unit" class="form-label">Unit of Measurement</label>
                        <select class="form-select" id="edit_unit" name="unit" required>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="g">Gram (g)</option>
                            <option value="l">Liter (l)</option>
                            <option value="ml">Milliliter (ml)</option>
                            <option value="pcs">Pieces (pcs)</option>
                            <option value="bunch">Bunch</option>
                            <option value="pack">Pack</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .ingredient-item.hidden {
        display: none;
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
    
    // Category filtering
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const filterValue = this.value;
        const ingredientItems = document.querySelectorAll('.ingredient-item');
        
        ingredientItems.forEach(item => {
            if (filterValue === 'all' || item.dataset.category === filterValue) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
    
    // Edit ingredient modal
    document.querySelectorAll('.edit-ingredient-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const category = this.dataset.category;
            const unit = this.dataset.unit;
            const description = this.dataset.description;
            
            document.getElementById('editIngredientForm').action = `/cook/ingredients/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_unit').value = unit;
            document.getElementById('edit_description').value = description;
            
            const editIngredientModal = new bootstrap.Modal(document.getElementById('editIngredientModal'));
            editIngredientModal.show();
        });
    });
</script>
@endpush
