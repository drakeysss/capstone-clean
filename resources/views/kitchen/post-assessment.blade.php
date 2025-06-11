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
                <form action="{{ route('kitchen.post-assessment.store') }}" method="POST" enctype="multipart/form-data">
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

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-camera me-2"></i>Attach Photo (Optional)
                        </label>
                        <input type="file" class="form-control" name="report_image" accept="image/*" id="reportImage">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload a photo of the leftovers to help the cook/admin see the actual situation.
                            Supported formats: JPEG, PNG, GIF (Max: 5MB)
                        </div>
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="previewImg" src="" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeImage">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </div>
                    </div>

                    <!-- Duplicate Warning Section -->
                    <div id="duplicate-warning" style="display: none;" class="mb-4"></div>

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
                                            <input type="number" class="form-control" name="items[0][prepared_quantity]" min="0" step="0.01" required>
                                            <span class="input-group-text">servings</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Leftover Quantity</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="items[0][leftover_quantity]" min="0" step="0.01" required>
                                            <span class="input-group-text">servings</span>
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

        document.getElementById('currentDateTime').innerHTML = `${dateString}<br><small>${timeString}</small>`;
    }

    // Update immediately and then every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Image upload preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('reportImage');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const removeImageBtn = document.getElementById('removeImage');

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];

                if (file) {
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size must be less than 5MB');
                        imageInput.value = '';
                        return;
                    }

                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select a valid image file');
                        imageInput.value = '';
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                }
            });

            // Remove image functionality
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function() {
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    previewImg.src = '';
                });
            }
        }
    });
    
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

    document.addEventListener('DOMContentLoaded', function() {
        // Find all forms for post-assessment (add/edit)
        document.querySelectorAll('form').forEach(function(form) {
            if (form.action && form.action.includes('/kitchen/post-assessment')) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Client-side validation
                    const date = form.querySelector('[name="date"]').value;
                    const mealType = form.querySelector('[name="meal_type"]').value;
                    const itemNames = form.querySelectorAll('[name*="[name]"]');
                    const preparedQtys = form.querySelectorAll('[name*="[prepared_quantity]"]');
                    const leftoverQtys = form.querySelectorAll('[name*="[leftover_quantity]"]');

                    console.log('🔍 Client-side validation check:', {
                        date: date,
                        mealType: mealType,
                        itemCount: itemNames.length,
                        hasAllNames: Array.from(itemNames).every(input => input.value.trim() !== ''),
                        hasAllPrepared: Array.from(preparedQtys).every(input => input.value !== ''),
                        hasAllLeftover: Array.from(leftoverQtys).every(input => input.value !== '')
                    });

                    // Check if we have at least one complete item
                    if (itemNames.length === 0) {
                        alert('Please add at least one food item.');
                        return;
                    }

                    // Check if all required fields are filled
                    let hasEmptyFields = false;
                    let validationErrors = [];

                    // Check date and meal type
                    if (!date) validationErrors.push('Date is required');
                    if (!mealType) validationErrors.push('Meal type is required');

                    // Check each item
                    itemNames.forEach((input, index) => {
                        if (!input.value.trim()) {
                            validationErrors.push(`Food item ${index + 1}: Name is required`);
                            hasEmptyFields = true;
                        }
                    });

                    preparedQtys.forEach((input, index) => {
                        if (!input.value || input.value === '') {
                            validationErrors.push(`Food item ${index + 1}: Prepared quantity is required`);
                            hasEmptyFields = true;
                        }
                    });

                    leftoverQtys.forEach((input, index) => {
                        if (!input.value || input.value === '') {
                            validationErrors.push(`Food item ${index + 1}: Leftover quantity is required`);
                            hasEmptyFields = true;
                        }
                    });

                    if (hasEmptyFields) {
                        alert('Please fix the following errors:\n\n' + validationErrors.join('\n'));
                        return;
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                    }
                    const formData = new FormData(form);

                    // Debug: Log form data
                    console.log('📝 Submitting post-assessment form data:');
                    console.log('📊 Form validation check:');

                    // Check form structure
                    const formElements = {
                        date: form.querySelector('[name="date"]')?.value,
                        meal_type: form.querySelector('[name="meal_type"]')?.value,
                        notes: form.querySelector('[name="notes"]')?.value,
                        report_image: form.querySelector('[name="report_image"]')?.files[0]?.name || 'No file',
                        items: []
                    };

                    // Check items structure
                    const itemInputs = form.querySelectorAll('[name*="items["]');
                    console.log('🔍 Found item inputs:', itemInputs.length);

                    // Group items by index
                    const itemsByIndex = {};
                    itemInputs.forEach(input => {
                        const match = input.name.match(/items\[(\d+)\]\[(\w+)\]/);
                        if (match) {
                            const index = match[1];
                            const field = match[2];
                            if (!itemsByIndex[index]) itemsByIndex[index] = {};
                            itemsByIndex[index][field] = input.value;
                        }
                    });

                    formElements.items = Object.values(itemsByIndex);
                    console.log('📋 Structured form data:', formElements);

                    // Validate items
                    const itemValidation = formElements.items.map((item, index) => ({
                        index,
                        name: item.name || 'MISSING',
                        prepared_quantity: item.prepared_quantity || 'MISSING',
                        leftover_quantity: item.leftover_quantity || 'MISSING',
                        valid: !!(item.name && item.prepared_quantity !== undefined && item.leftover_quantity !== undefined)
                    }));

                    console.log('✅ Item validation:', itemValidation);

                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}:`, value);
                    }

                    fetch(form.action, {
                        method: form.method || 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            // Don't set Content-Type for FormData - let browser set it with boundary
                        }
                    })
                    .then(response => {
                        console.log('📡 Response status:', response.status);
                        // Don't throw error for 422 - let it be handled in the next then block
                        if (!response.ok && response.status !== 422) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json().then(data => ({ status: response.status, data }));
                    })
                    .then(({ status, data }) => {
                        console.log('✅ Response data:', data);
                        if (data.success) {
                            alert('Post-assessment submitted successfully!');
                            form.reset();
                            location.reload();
                        } else {
                            console.error('❌ Submission failed:', data);

                            // Show detailed error information
                            let errorMessage = data.message || 'Failed to submit post-assessment';

                            // Handle specific error cases
                            if (status === 422) {
                                if (data.message && data.message.includes('already exists')) {
                                    errorMessage = '⚠️ Duplicate Report Alert\n\n' + data.message + '\n\n' +
                                                 'Solutions:\n' +
                                                 '• Choose a different date\n' +
                                                 '• Choose a different meal type (breakfast/lunch/dinner)\n' +
                                                 '• Contact admin to modify the existing report';
                                } else if (data.errors) {
                                    errorMessage = 'Validation Error:\n\n' + data.message;

                                    // Add specific validation errors
                                    Object.keys(data.errors).forEach(field => {
                                        errorMessage += '\n• ' + field + ': ' + data.errors[field].join(', ');
                                    });
                                }
                            }

                            if (data.debug) {
                                console.error('🔍 Debug information:', data.debug);

                                // Show specific validation errors
                                if (data.debug.all_validation_errors) {
                                    console.error('📋 Validation errors breakdown:', data.debug.all_validation_errors);

                                    // Create user-friendly error message
                                    const errors = data.debug.all_validation_errors;
                                    let detailedErrors = [];

                                    if (errors.date) detailedErrors.push('Date: ' + errors.date.join(', '));
                                    if (errors.meal_type) detailedErrors.push('Meal Type: ' + errors.meal_type.join(', '));
                                    if (errors.items) detailedErrors.push('Items: ' + errors.items.join(', '));

                                    // Check for item-specific errors
                                    Object.keys(errors).forEach(key => {
                                        if (key.startsWith('items.')) {
                                            detailedErrors.push(key + ': ' + errors[key].join(', '));
                                        }
                                    });

                                    if (detailedErrors.length > 0) {
                                        errorMessage += '\n\nSpecific errors:\n' + detailedErrors.join('\n');
                                    }
                                }

                                if (data.debug.items_data) {
                                    console.error('📦 Items data received by server:', data.debug.items_data);
                                }
                            }

                            if (data.errors) {
                                console.error('Validation errors:', data.errors);
                            }

                            alert(errorMessage);
                        }
                    })
                    .catch(error => {
                        console.error('💥 Fetch error:', error);
                        alert('An error occurred while submitting post-assessment: ' + error.message);
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="bi bi-send me-2"></i> Submit Report';
                        }
                    });
                });
            }
        });
    });

    // Add duplicate check warning
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('[name="date"]');
        const mealTypeSelect = document.querySelector('[name="meal_type"]');

        function checkForDuplicates() {
            const date = dateInput?.value;
            const mealType = mealTypeSelect?.value;

            if (date && mealType) {
                // Simple check - warn if it's today and the same meal type
                const today = new Date().toISOString().split('T')[0];
                if (date === today) {
                    const warningDiv = document.getElementById('duplicate-warning');
                    if (warningDiv) {
                        warningDiv.style.display = 'block';
                        warningDiv.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Notice:</strong> You are submitting a report for today's ${mealType}.
                                Please make sure you haven't already submitted a report for this meal.
                            </div>
                        `;
                    }
                } else {
                    const warningDiv = document.getElementById('duplicate-warning');
                    if (warningDiv) {
                        warningDiv.style.display = 'none';
                    }
                }
            }
        }

        if (dateInput) dateInput.addEventListener('change', checkForDuplicates);
        if (mealTypeSelect) mealTypeSelect.addEventListener('change', checkForDuplicates);
    });


</script>
@endpush
