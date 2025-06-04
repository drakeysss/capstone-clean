@extends('layouts.app')

@section('content')
<div class="col-12 mb-4">
    <div class="card border-0 bg-primary text-white overflow-hidden">
        <div class="card-body p-4 position-relative" style="background-color: var(--secondary-color);">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="fw-bold mb-1">Leftover Food Report</h4>
                    <p class="mb-0">Report leftover food to Cook</p>
                </div>
                <div class="col-auto">
                    <div class="text-end">
                        <div id="currentDateTime" class="fs-6 mb-1"></div>
                        <i class="bi bi-trash display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Simple Leftover Report Form -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Report Leftovers</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kitchen.post-assessment.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" id="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="meal_type" class="form-label">Meal Type</label>
                            <select id="meal_type" name="meal_type" class="form-select" required>
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch" selected>Lunch</option>
                                <option value="dinner">Dinner</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Notes for Cook</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any notes about the leftovers"></textarea>
                    </div>
                    
                    <div class="leftover-items mb-4">
                        <div class="leftover-item card mb-3">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Food Item</label>
                                        <input type="text" class="form-control" name="items[0][name]" placeholder="Enter food item name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Prepared Quantity</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="items[0][prepared_quantity]" min="0" required>
                                          
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Leftover Quantity</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="items[0][leftover_quantity]" min="0" required>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <button type="button" id="addItemBtn" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i> Add Another Food Item
                        </button>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i> Submit Report
                        </button>
                    </div>
                </form>
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
            day: 'numeric' 
        };
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        
        document.getElementById('currentDateTime').innerHTML = 
            `${now.toLocaleDateString('en-US', options)}<br>${now.toLocaleTimeString('en-US', timeOptions)}`;
    }
    
    // Update immediately and then every second
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Add new food item functionality
    document.addEventListener('DOMContentLoaded', function() {
        let itemCount = 0;
        
        document.getElementById('addItemBtn').addEventListener('click', function() {
            itemCount++;
            
            const newItem = document.createElement('div');
            newItem.className = 'leftover-item card mb-3';
            newItem.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Food Item ${itemCount + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Food Item</label>
                            <input type="text" class="form-control" name="items[${itemCount}][name]" placeholder="Enter food item name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prepared Quantity</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="items[${itemCount}][prepared_quantity]" min="0" required>
                                <span class="input-group-text">servings</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Leftover Quantity</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="items[${itemCount}][leftover_quantity]" min="0" required>
                                <span class="input-group-text">servings</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.querySelector('.leftover-items').appendChild(newItem);
            
            // Add event listener to the remove button
            newItem.querySelector('.remove-item').addEventListener('click', function() {
                newItem.remove();
            });
        });
        
        // Event delegation for dynamically added remove buttons
        document.querySelector('.leftover-items').addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                e.target.closest('.leftover-item').remove();
            }
        });
    });
</script>
@endpush
