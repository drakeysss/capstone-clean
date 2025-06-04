@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Meal Attendance</h2>
                    <p class="text-muted" style="color: white;">Let us know which meals you'll be attending</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Polls Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Available Meal Polls</h5>
                    <div>
                        <select id="dayFilter" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="all">All Days</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="this-week">This Week</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Meal</th>
                                    <th>Menu</th>
                                    <th>Cutoff Time</th>
                                    <th>Your Response</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example Poll 1 - Breakfast -->
                                <tr class="poll-item" data-day="today" data-meal="breakfast">
                                    <td>May 31, 2025</td>
                                    <td><span class="badge bg-warning text-dark">Breakfast</span></td>
                                    <td>
                                        <div class="fw-bold">Chicken Loaf with Energen</div>
                                        <small class="text-muted">Chicken Loaf, Energen, Water</small>
                                    </td>
                                    <td>May 30, 2025 8:00 PM</td>
                                    <td><span class="badge bg-success">Will Attend</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary respond-btn" data-bs-toggle="modal" data-bs-target="#respondModal" data-poll-id="1" data-meal="Breakfast" data-date="May 31, 2025" data-menu="Chicken Loaf with Energen">
                                            <i class="bi bi-pencil"></i> Change
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Example Poll 2 - Lunch -->
                                <tr class="poll-item" data-day="today" data-meal="lunch">
                                    <td>May 31, 2025</td>
                                    <td><span class="badge bg-primary">Lunch</span></td>
                                    <td>
                                        <div class="fw-bold">Fried Fish</div>
                                        <small class="text-muted">Fish, Oil, Salt</small>
                                    </td>
                                    <td>May 30, 2025 8:00 PM</td>
                                    <td><span class="badge bg-secondary">Undecided</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary respond-btn" data-bs-toggle="modal" data-bs-target="#respondModal" data-poll-id="2" data-meal="Lunch" data-date="May 31, 2025" data-menu="Fried Fish">
                                            <i class="bi bi-check-circle"></i> Respond
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Example Poll 3 - Dinner -->
                                <tr class="poll-item" data-day="today" data-meal="dinner">
                                    <td>May 31, 2025</td>
                                    <td><span class="badge bg-info">Dinner</span></td>
                                    <td>
                                        <div class="fw-bold">Ginisang Cabbage</div>
                                        <small class="text-muted">Cabbage, Garlic, Onion, Oil, Salt</small>
                                    </td>
                                    <td>May 30, 2025 8:00 PM</td>
                                    <td><span class="badge bg-danger">Will Not Attend</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary respond-btn" data-bs-toggle="modal" data-bs-target="#respondModal" data-poll-id="3" data-meal="Dinner" data-date="May 31, 2025" data-menu="Ginisang Cabbage">
                                            <i class="bi bi-pencil"></i> Change
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Example Poll 4 - Tomorrow's Breakfast -->
                                <tr class="poll-item" data-day="tomorrow" data-meal="breakfast">
                                    <td>June 1, 2025</td>
                                    <td><span class="badge bg-warning text-dark">Breakfast</span></td>
                                    <td>
                                        <div class="fw-bold">Champorado</div>
                                        <small class="text-muted">Rice, Cocoa, Sugar, Milk</small>
                                    </td>
                                    <td>May 31, 2025 8:00 PM</td>
                                    <td><span class="badge bg-secondary">Not Responded</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary respond-btn" data-bs-toggle="modal" data-bs-target="#respondModal" data-poll-id="4" data-meal="Breakfast" data-date="June 1, 2025" data-menu="Champorado">
                                            <i class="bi bi-check-circle"></i> Respond
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Your Response History Section -->
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title">Your Meal Attendance History</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Attendance Chart -->
                            <canvas id="attendanceChart" height="300"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center p-4 mb-3">
                                <h3 class="mb-3">Attendance Overview</h3>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <div class="stats-item">
                                            <div class="stats-value">85%</div>
                                            <div class="stats-label">Breakfast</div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stats-item">
                                            <div class="stats-value">92%</div>
                                            <div class="stats-label">Lunch</div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stats-item">
                                            <div class="stats-value">78%</div>
                                            <div class="stats-label">Dinner</div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stats-item">
                                            <div class="stats-value">86%</div>
                                            <div class="stats-label">Overall</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="respondModalLabel">Respond to Meal Poll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pollResponseForm">
                    <input type="hidden" id="poll_id" name="poll_id">
                    
                    <div class="meal-details mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Date:</span>
                            <span class="fw-bold" id="modal-date"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Meal:</span>
                            <span class="fw-bold" id="modal-meal"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Menu:</span>
                            <span class="fw-bold" id="modal-menu"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Response Due By:</span>
                            <span class="fw-bold text-danger" id="modal-cutoff"></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Will you attend this meal?</label>
                        <div class="response-options">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="response" id="response_yes" value="yes">
                                <label class="form-check-label" for="response_yes">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>Yes, I will attend
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="response" id="response_no" value="no">
                                <label class="form-check-label" for="response_no">
                                    <i class="bi bi-x-circle-fill text-danger me-2"></i>No, I will not attend
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="response" id="response_maybe" value="maybe">
                                <label class="form-check-label" for="response_maybe">
                                    <i class="bi bi-question-circle-fill text-warning me-2"></i>Undecided (will update later)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any dietary restrictions or special requests"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitResponseBtn">Submit Response</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .stats-item {
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
    }
    
    .stats-value {
        font-size: 24px;
        font-weight: bold;
        color: #0d6efd;
    }
    
    .stats-label {
        font-size: 14px;
        color: #6c757d;
    }
    
    .response-options .form-check {
        padding: 10px 15px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .response-options .form-check:hover {
        background-color: #f8f9fa;
    }
    
    .response-options .form-check-input:checked + .form-check-label {
        font-weight: bold;
    }
    
    .meal-details {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
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
    
    // Filter polls by day
    document.getElementById('dayFilter').addEventListener('change', function() {
        const filter = this.value;
        const rows = document.querySelectorAll('.poll-item');
        
        rows.forEach(row => {
            if (filter === 'all' || row.dataset.day === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Populate modal with poll details
    document.querySelectorAll('.respond-btn').forEach(button => {
        button.addEventListener('click', function() {
            const pollId = this.dataset.pollId;
            const date = this.dataset.date;
            const meal = this.dataset.meal;
            const menu = this.dataset.menu;
            
            document.getElementById('poll_id').value = pollId;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-meal').textContent = meal;
            document.getElementById('modal-menu').textContent = menu;
            document.getElementById('modal-cutoff').textContent = 'Today, 8:00 PM';
            
            // Clear previous selection
            document.querySelectorAll('[name="response"]').forEach(radio => {
                radio.checked = false;
            });
            
            // Find current response if any and set it
            const row = this.closest('tr');
            const responseCell = row.querySelector('td:nth-child(5)');
            const responseText = responseCell.textContent.trim();
            
            if (responseText === 'Will Attend') {
                document.getElementById('response_yes').checked = true;
            } else if (responseText === 'Will Not Attend') {
                document.getElementById('response_no').checked = true;
            } else if (responseText === 'Undecided') {
                document.getElementById('response_maybe').checked = true;
            }
        });
    });
    
    // Handle form submission
    document.getElementById('submitResponseBtn').addEventListener('click', function() {
        const form = document.getElementById('pollResponseForm');
        const pollId = form.elements['poll_id'].value;
        const response = form.elements['response'].value;
        const notes = form.elements['notes'].value;
        
        // In a real application, this would be an AJAX call to submit the response
        console.log('Submitting response:', { pollId, response, notes });
        
        // Update the UI to reflect the response
        const rows = document.querySelectorAll('.poll-item');
        rows.forEach(row => {
            const button = row.querySelector('.respond-btn');
            if (button && button.dataset.pollId === pollId) {
                const responseCell = row.querySelector('td:nth-child(5)');
                const actionCell = row.querySelector('td:nth-child(6)');
                
                if (response === 'yes') {
                    responseCell.innerHTML = '<span class="badge bg-success">Will Attend</span>';
                } else if (response === 'no') {
                    responseCell.innerHTML = '<span class="badge bg-danger">Will Not Attend</span>';
                } else {
                    responseCell.innerHTML = '<span class="badge bg-secondary">Undecided</span>';
                }
                
                // Change button to "Change" if it was "Respond"
                if (button.textContent.trim() === 'Respond') {
                    button.innerHTML = '<i class="bi bi-pencil"></i> Change';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-outline-primary');
                }
            }
        });
        
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('respondModal'));
        modal.hide();
        
        // Show success message
        alert('Your response has been recorded. Thank you!');
    });
    
    // Initialize attendance chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [
                    {
                        label: 'Breakfast',
                        data: [90, 80, 85, 85],
                        backgroundColor: '#ffc107'
                    },
                    {
                        label: 'Lunch',
                        data: [95, 90, 90, 93],
                        backgroundColor: '#0d6efd'
                    },
                    {
                        label: 'Dinner',
                        data: [80, 75, 80, 77],
                        backgroundColor: '#0dcaf0'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Attendance Percentage'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Your Monthly Meal Attendance'
                    }
                }
            }
        });
    });
</script>
@endpush
