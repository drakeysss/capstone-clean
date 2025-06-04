@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Student Feedback</h2>
                    <p class="text-muted" style="color: white;">Review and analyze student feedback to improve meal quality</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Analysis Section -->
    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="card main-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Student Feedback</h5>
                    <div>
                        <select id="periodFilter" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="all">All Time</option>
                        </select>
                        <select id="mealFilter" class="form-select form-select-sm d-inline-block w-auto ms-2">
                            <option value="all">All Meals</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                        <select id="ratingFilter" class="form-select form-select-sm d-inline-block w-auto ms-2">
                            <option value="all">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Meal</th>
                                    <th>Rating</th>
                                    <th>Feedback</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks ?? [] as $feedback)
                                <tr class="feedback-item" 
                                    data-date="{{ $feedback->created_at }}" 
                                    data-meal="{{ $feedback->meal_type }}"
                                    data-rating="{{ $feedback->rating }}">
                                    <td>{{ date('M d, Y', strtotime($feedback->created_at)) }}</td>
                                    <td>{{ $feedback->student->name ?? 'Anonymous' }}</td>
                                    <td>{{ ucfirst($feedback->meal_type) }}</td>
                                    <td>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $feedback->rating)
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                @else
                                                    <i class="bi bi-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($feedback->feedback, 50) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary view-feedback-btn" 
                                            data-id="{{ $feedback->id }}"
                                            data-student="{{ $feedback->student->name ?? 'Anonymous' }}"
                                            data-date="{{ date('M d, Y', strtotime($feedback->created_at)) }}"
                                            data-meal-type="{{ ucfirst($feedback->meal_type) }}"
                                            data-rating="{{ $feedback->rating }}"
                                            data-feedback="{{ $feedback->feedback }}"
                                            data-meal-items="{{ json_encode($feedback->meal_items) }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No feedback found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card main-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Average Rating by Meal</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-4">
                        <canvas id="mealRatingChart"></canvas>
                    </div>
                    
                    <div class="meal-ratings mt-4">
                        <div class="meal-rating-item d-flex justify-content-between align-items-center mb-3">
                            <span><i class="bi bi-egg-fried me-2"></i> Breakfast</span>
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($breakfastRating ?? 0))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-muted"></i>
                                    @endif
                                @endfor
                                <span class="ms-2 fw-bold">{{ $breakfastRating ?? '0.0' }}/5</span>
                            </div>
                        </div>
                        
                        <div class="meal-rating-item d-flex justify-content-between align-items-center mb-3">
                            <span><i class="bi bi-cup-hot me-2"></i> Lunch</span>
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($lunchRating ?? 0))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-muted"></i>
                                    @endif
                                @endfor
                                <span class="ms-2 fw-bold">{{ $lunchRating ?? '0.0' }}/5</span>
                            </div>
                        </div>
                        
                        <div class="meal-rating-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-moon-stars me-2"></i> Dinner</span>
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($dinnerRating ?? 0))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-muted"></i>
                                    @endif
                                @endfor
                                <span class="ms-2 fw-bold">{{ $dinnerRating ?? '0.0' }}/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Feedback Modal -->
<div class="modal fade" id="viewFeedbackModal" tabindex="-1" aria-labelledby="viewFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFeedbackModalLabel">Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Student:</strong> <span id="view-feedback-student"></span></p>
                        <p><strong>Date:</strong> <span id="view-feedback-date"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Meal Type:</strong> <span id="view-feedback-meal-type"></span></p>
                        <p><strong>Rating:</strong> <span id="view-feedback-rating"></span></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p><strong>Feedback:</strong></p>
                    <p id="view-feedback-text"></p>
                </div>
                
                <div class="mb-3">
                    <p><strong>Meal Items:</strong></p>
                    <ul id="view-feedback-meal-items">
                        <!-- Items will be added dynamically -->
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .feedback-item.hidden {
        display: none;
    }
    
    .chart-container {
        height: 200px;
        position: relative;
    }
    
    .insight-item {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    // Filtering
    function filterFeedback() {
        const periodFilter = document.getElementById('periodFilter').value;
        const mealFilter = document.getElementById('mealFilter').value;
        const ratingFilter = document.getElementById('ratingFilter').value;
        const feedbackItems = document.querySelectorAll('.feedback-item');
        
        const today = new Date();
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay());
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        
        feedbackItems.forEach(item => {
            const itemDate = new Date(item.dataset.date);
            const itemMeal = item.dataset.meal;
            const itemRating = item.dataset.rating;
            
            let periodMatch = true;
            if (periodFilter === 'week') {
                periodMatch = itemDate >= weekStart;
            } else if (periodFilter === 'month') {
                periodMatch = itemDate >= monthStart;
            }
            // For 'all', show all dates
            
            const mealMatch = mealFilter === 'all' || itemMeal === mealFilter;
            const ratingMatch = ratingFilter === 'all' || itemRating === ratingFilter;
            
            if (periodMatch && mealMatch && ratingMatch) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }
    
    document.getElementById('periodFilter').addEventListener('change', filterFeedback);
    document.getElementById('mealFilter').addEventListener('change', filterFeedback);
    document.getElementById('ratingFilter').addEventListener('change', filterFeedback);
    
    // View feedback modal
    document.querySelectorAll('.view-feedback-btn').forEach(button => {
        button.addEventListener('click', function() {
            const student = this.dataset.student;
            const date = this.dataset.date;
            const mealType = this.dataset.mealType;
            const rating = this.dataset.rating;
            const feedback = this.dataset.feedback;
            const mealItems = JSON.parse(this.dataset.mealItems || '[]');
            
            document.getElementById('view-feedback-student').textContent = student;
            document.getElementById('view-feedback-date').textContent = date;
            document.getElementById('view-feedback-meal-type').textContent = mealType;
            
            // Display rating as stars
            let ratingHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    ratingHtml += '<i class="bi bi-star-fill text-warning"></i> ';
                } else {
                    ratingHtml += '<i class="bi bi-star text-muted"></i> ';
                }
            }
            document.getElementById('view-feedback-rating').innerHTML = ratingHtml;
            
            document.getElementById('view-feedback-text').textContent = feedback;
            
            // Display meal items
            const mealItemsList = document.getElementById('view-feedback-meal-items');
            mealItemsList.innerHTML = '';
            
            if (mealItems.length > 0) {
                mealItems.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    mealItemsList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'No specific meal items mentioned';
                mealItemsList.appendChild(li);
            }
            
            const viewFeedbackModal = new bootstrap.Modal(document.getElementById('viewFeedbackModal'));
            viewFeedbackModal.show();
        });
    });
    
    // Charts
    document.addEventListener('DOMContentLoaded', function() {
        // Meal Rating Chart
        const mealCtx = document.getElementById('mealRatingChart').getContext('2d');
        
        // Initialize with zero values until we have actual data
        const mealRatingChart = new Chart(mealCtx, {
            type: 'bar',
            data: {
                labels: ['Breakfast', 'Lunch', 'Dinner'],
                datasets: [{
                    label: 'Average Rating',
                    data: [0, 0, 0],
                    backgroundColor: [
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 159, 64, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Average Rating by Meal'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
