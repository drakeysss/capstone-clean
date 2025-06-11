@extends('layouts.app')

@section('content')
<div class="col-12 mb-4">
            <div class="card border-0 bg-primary text-white overflow-hidden">
                <div class="card-body p-4 position-relative" style="background-color: var(--secondary-color);">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fw-bold mb-1">Kitchen Dashboard</h4>
                            <p class="mb-0">Execute meal plans created by Cook</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cup-hot display-4 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    <!-- Quick Access Feature Cards -->
    <div class="row">
        <div class="col-12 mb-3">
            <h5 class="text-dark">Quick Access</h5>
        </div>

        <!-- Inventory Check Feature -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('kitchen.inventory') }}" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto me-3">
                                <div class="rounded-circle bg-primary p-3 text-white">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="font-weight-bold text-primary mb-1">Inventory Check</h6>
                                <p class="text-muted small mb-0">Report counted items to cook</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-2">
                                    <i class="bi bi-plus-circle text-primary"></i>
                                </div>
                                <span class="small">Add items you've counted</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Leftover Report Feature -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('kitchen.post-assessment') }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto me-3">
                                <div class="rounded-circle bg-success p-3 text-white">
                                    <i class="bi bi-trash"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="font-weight-bold text-success mb-1">Leftover Report</h6>
                                <p class="text-muted small mb-0">Report leftover food to cook</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-2">
                                    <i class="bi bi-plus-circle text-success"></i>
                                </div>
                                <span class="small">Add food items and quantities</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Feedback Feature -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="/kitchen/feedback" class="text-decoration-none">
                <div class="card border-left-danger shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto me-3">
                                <div class="rounded-circle bg-danger p-3 text-white">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="font-weight-bold text-danger mb-1">Feedback</h6>
                                <p class="text-muted small mb-0">View student meal feedback</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-2">
                                    <i class="bi bi-eye text-danger"></i>
                                </div>
                                <span class="small">See student comments and ratings</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Daily Menu Feature -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="/kitchen/daily-menu" class="text-decoration-none">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto me-3">
                                <div class="rounded-circle bg-info p-3 text-white">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="font-weight-bold text-info mb-1">Today's Menu</h6>
                                <p class="text-muted small mb-0">View cook's meal plan</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-2">
                                    <i class="bi bi-eye text-info"></i>
                                </div>
                                <span class="small">See meals to prepare today</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Main Cards */
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        transition: all 0.3s ease;
    }

    .main-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
    }

    /* Feature Overview Cards */
    .feature-overview-card {
        border: none;
        overflow: hidden;
    }

    .feature-overview-card .card-header {
        border: none;
        padding: 1rem 1.25rem;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #ff9933 0%, #ff7700 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9500 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #22bbea 0%, #0099cc 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    .bg-gradient-dark {
        background: linear-gradient(135deg, #343a40 0%, #212529 100%);
    }

    .metric-item {
        padding: 0.5rem 0;
    }

    .metric-item h4, .metric-item h5 {
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .metric-item small {
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .feature-overview-card .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .feature-overview-card .btn-light:hover {
        background: white;
        transform: scale(1.1);
    }

    /* Quick Access Cards */
    .border-left-primary {
        border-left: 0.25rem solid var(--primary-color, #ff9933) !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #28a745 !important;
    }

    .border-left-danger {
        border-left: 0.25rem solid #dc3545 !important;
    }

    .border-left-info {
        border-left: 0.25rem solid var(--secondary-color, #22bbea) !important;
    }

    .card.shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }

    .card.shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25) !important;
        transition: all 0.3s ease;
    }

    .rounded-circle {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-primary {
        background-color: var(--primary-color, #ff9933) !important;
    }

    .text-primary {
        color: var(--primary-color, #ff9933) !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .border-top {
        border-top: 1px solid #e3e6f0 !important;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        transition: all 0.3s ease;
    }

    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0.5rem !important;
        }

        .card-body {
            padding: 1rem 0.75rem !important;
        }

        .rounded-circle {
            width: 2.5rem !important;
            height: 2.5rem !important;
            margin-bottom: 0.5rem;
        }

        .row.no-gutters {
            margin: 0 !important;
        }

        .row.no-gutters > * {
            padding: 0 !important;
        }

        .border-top {
            margin-top: 1rem !important;
            padding-top: 1rem !important;
        }

        .d-flex {
            flex-wrap: wrap !important;
            justify-content: center !important;
        }

        .small {
            font-size: 0.8rem !important;
            text-align: center !important;
        }
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 0.75rem 0.5rem !important;
        }

        .rounded-circle {
            width: 2rem !important;
            height: 2rem !important;
        }

        .font-weight-bold {
            font-size: 0.9rem !important;
        }

        .small {
            font-size: 0.75rem !important;
        }
    }
</style>
@endpush
