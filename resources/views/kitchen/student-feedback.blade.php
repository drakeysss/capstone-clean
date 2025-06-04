@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Student Feedback</h2>
                    <p class="text-muted" style="color: white;">Review student feedback to improve meal quality</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

 
   
    

       

    <!-- Feedback List -->
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Student Feedback</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Menu Item</th>
                                    <th>Rating</th>
                                    <th>Feedback</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedback ?? [] as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>{{ $item->user->name ?? 'Anonymous' }}</td>
                                        <td>{{ $item->menu->name ?? 'Unknown Item' }}</td>
                                        <td>
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $item->rating)
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    @else
                                                        <i class="bi bi-star text-muted"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($item->comment, 50) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary view-feedback-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewFeedbackModal"
                                                data-student="{{ $item->user->name ?? 'Anonymous' }}"
                                                data-date="{{ $item->created_at->format('M d, Y') }}"
                                                data-menu="{{ $item->menu->name ?? 'Unknown Item' }}"
                                                data-rating="{{ $item->rating }}"
                                                data-comment="{{ $item->comment }}">
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
                    
                    <div class="d-flex justify-content-center mt-4 mb-4">
                        {{ $feedback->links() ?? '' }}
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
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <h5 id="view-menu"></h5>
                        <div id="view-rating"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Student: <span id="view-student"></span></small>
                        <small class="text-muted">Date: <span id="view-date"></span></small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Feedback:</label>
                    <p id="view-comment"></p>
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
    .rating {
        color: #ffc107;
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
    
    // View feedback modal
    document.querySelectorAll('.view-feedback-btn').forEach(button => {
        button.addEventListener('click', function() {
            const student = this.dataset.student;
            const date = this.dataset.date;
            const menu = this.dataset.menu;
            const rating = this.dataset.rating;
            const comment = this.dataset.comment;
            
            document.getElementById('view-student').textContent = student;
            document.getElementById('view-date').textContent = date;
            document.getElementById('view-menu').textContent = menu;
            document.getElementById('view-comment').textContent = comment || 'No comment provided';
            
            // Display rating as stars
            let ratingHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    ratingHtml += '<i class="bi bi-star-fill text-warning"></i> ';
                } else {
                    ratingHtml += '<i class="bi bi-star text-muted"></i> ';
                }
            }
            document.getElementById('view-rating').innerHTML = ratingHtml;
        });
    });
</script>
@endpush
