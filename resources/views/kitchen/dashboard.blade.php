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
    

        <!-- Recent Alerts -->
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tasks from Cook/Admin</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @if(isset($inventoryItems) && count($inventoryItems) > 0)
                            @foreach($inventoryItems as $item)
                            <a href="{{ route('kitchen.inventory') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Low Stock: {{ $item->name }}</h6>
                                    <small>{{ $item->quantity }} {{ $item->unit }} left</small>
                                </div>
                                <p class="mb-1">Notify cook when preparing meals</p>
                            </a>
                            @endforeach
                        @else
                            <a href="{{ route('kitchen.post-assessment') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Report Leftover Food</h6>
                                    <small class="badge bg-danger">Important</small>
                                </div>
                                <p class="mb-1">Report today's leftover food quantities to cook</p>
                            </a>
                            <a href="{{ route('kitchen.inventory') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Inventory Check</h6>
                                    <small>Weekly Task</small>
                                </div>
                                <p class="mb-1">Report counted inventory items to cook</p>
                            </a>
                            <a href="/kitchen/feedback" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Review Student Feedback</h6>
                                    <small class="badge bg-info">New</small>
                                </div>
                                <p class="mb-1">Check student comments and ratings on meals</p>
                            </a>
                            <a href="/kitchen/daily-menu" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Execute Today's Menu</h6>
                                    <small>From Cook</small>
                                </div>
                                <p class="mb-1">Prepare meals according to cook's plan</p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
