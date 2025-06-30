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
                            <i class="bi bi-clipboard-data me-2"></i>Post-meal Report
                        </h3>
                        <p class="mb-0 opacity-75">Report leftover food to Cook</p>
                    </div>
                    <div class="text-end">
                        <span id="currentDateTime" class="fs-6 text-white"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Leftovers</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('kitchen.post-assessment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" id="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="meal_type" class="form-label">Meal Type</label>
                                <select id="meal_type" name="meal_type" class="form-select" required>
                                    <option value="breakfast">Breakfast</option>
                                    <option value="lunch" selected>Lunch</option>
                                    <option value="dinner">Dinner</option>
                                </select>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <button type="button" id="addItemBtn" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i> Add Another Food Item
                            </button>
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
</div>

@endsection

@push('styles')
<style>
.date-time-block { text-align: center; }
.date-line { font-size: 1.15rem; font-weight: 500; }
.time-line { font-size: 1rem; font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time date and time display
    function updateDateTime() {
        const now = new Date();
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const dateString = now.toLocaleDateString('en-US', dateOptions);
        const timeString = now.toLocaleTimeString('en-US', timeOptions);
        const currentDateTimeElement = document.getElementById('currentDateTime');
        if (currentDateTimeElement) {
            currentDateTimeElement.textContent = `${dateString} ${timeString}`;
        }
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Image upload preview
    const imageInput = document.getElementById('reportImage');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    imageInput.value = '';
                    return;
                }
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file');
                    imageInput.value = '';
                    return;
                }
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
    }

    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            previewImg.src = '';
        });
    }

    // Add & Remove Food Items
    let itemCount = 1; // Start from 1 since item 0 is already on the page
    const addItemBtn = document.getElementById('addItemBtn');
    const itemsContainer = document.querySelector('.leftover-items');

    if (addItemBtn) {
        addItemBtn.addEventListener('click', function() {
            const newItem = document.createElement('div');
            newItem.className = 'leftover-item card mb-3';
            newItem.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Additional Food Item</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Food Item</label>
                            <input type="text" class="form-control" name="items[${itemCount}][name]" placeholder="Enter food item name" required>
                        </div>
                    </div>
                </div>
            `;
            itemsContainer.appendChild(newItem);
            itemCount++;
        });
    }
    
    if (itemsContainer) {
        itemsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                e.target.closest('.leftover-item').remove();
            }
        });
    }

    // Form Submission
    const form = document.querySelector('form[action*="kitchen.post-assessment.store"]');
    if(form){
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const date = form.querySelector('[name="date"]').value;
            const mealType = form.querySelector('[name="meal_type"]').value;
            const itemNames = form.querySelectorAll('[name*="[name]"]');
            let isValid = true;
            let errorMessage = 'Please fix the following issues:\n';

            if (!date) {
                isValid = false;
                errorMessage += '• Date is required.\n';
            }
            if (!mealType) {
                isValid = false;
                errorMessage += '• Meal Type is required.\n';
            }
            if (itemNames.length === 0) {
                isValid = false;
                errorMessage += '• At least one food item is required.\n';
            } else {
                itemNames.forEach((input, index) => {
                    if (!input.value.trim()) {
                        isValid = false;
                        errorMessage += `• Food item name for item #${index + 1} is required.\n`;
                    }
                });
            }

            if (!isValid) {
                alert(errorMessage);
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
            }
            
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Post-assessment submitted successfully!');
                    window.location.href = "{{ route('kitchen.post-assessment') }}?date=" + date + "&meal_type=" + mealType;
                } else {
                    // This else block might not be reached if server throws error
                }
            })
            .catch(error => {
                let serverErrorMessage = error.message || 'An unknown error occurred.';
                if (error.errors) {
                    for (const key in error.errors) {
                        serverErrorMessage += `\n• ${error.errors[key].join(', ')}`;
                    }
                }
                alert('Validation Error:\n' + serverErrorMessage);
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send me-2"></i> Submit Report';
                }
            });
        });
    }

    // Duplicate check warning
    const dateInput = document.querySelector('[name="date"]');
    const mealTypeSelect = document.querySelector('[name="meal_type"]');
    function checkForDuplicates() {
        const date = dateInput?.value;
        const mealType = mealTypeSelect?.value;
        const warningDiv = document.getElementById('duplicate-warning');

        if (date && mealType && warningDiv) {
            const today = new Date().toISOString().split('T')[0];
            if (date === today) {
                warningDiv.style.display = 'block';
                warningDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Notice:</strong> You are submitting a report for today's ${mealType}. Make sure a report for this meal doesn't already exist.
                    </div>
                `;
            } else {
                warningDiv.style.display = 'none';
            }
        }
    }
    if(dateInput) dateInput.addEventListener('change', checkForDuplicates);
    if(mealTypeSelect) mealTypeSelect.addEventListener('change', checkForDuplicates);
});
</script>
@endpush
