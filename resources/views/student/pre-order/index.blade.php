@extends('layouts.app')

@section('title', 'Pre-Order Meals')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Pre-Order Meals</h3>
                        <p class="mb-0 text-muted">Select your meals for the upcoming week</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pre-Order Instructions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Important Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-clock me-2"></i>Cutoff Times</h5>
                        <ul class="mb-0">
                            <li><strong>Breakfast:</strong> 10:00 PM the day before</li>
                            <li><strong>Lunch:</strong> 10:00 PM the day before if the students eat early</li>
                            <li><strong>Lunch:</strong> 8:00 AM the same day</li>
                            <li><strong>Dinner:</strong> 2:00 PM the same day</li>
                        </ul>
                        <p class="mt-2 mb-0">Pre-orders after these times cannot be accepted.</p>
                    </div>
                    <p>Pre-ordering helps the kitchen team prepare the right amount of food and reduce waste. Please pre-order your meals whenever possible.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Weekly Menu Pre-Order Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Weekly Menu ({{ $startDate->format('M d') }} - {{ $endDate->format('M d') }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Day</th>
                                    <th>Meal</th>
                                    <th>Menu Items</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentDate = null; @endphp
                                
                                @foreach($menuItems as $date => $dayMenus)
                                    @php 
                                        $formattedDate = \Carbon\Carbon::parse($date);
                                        $isToday = $formattedDate->isToday();
                                        $isPast = $formattedDate->isPast() && !$isToday;
                                    @endphp
                                    
                                    @foreach(['breakfast', 'lunch', 'dinner'] as $mealType)
                                        @if(isset($dayMenus) && $dayMenus->where('meal_type', $mealType)->count() > 0)
                                            @php
                                                $mealItems = $dayMenus->where('meal_type', $mealType);
                                                $preOrderKey = $date . '_' . $mealType;
                                                $hasPreOrder = isset($studentPreOrders[$preOrderKey]);
                                                $cutoffPassed = \Carbon\Carbon::now()->greaterThan($cutoffTimes[$mealType]);
                                                
                                                if ($mealType === 'breakfast') {
                                                    $cutoffPassed = \Carbon\Carbon::now()->greaterThan(
                                                        \Carbon\Carbon::parse($date)->subDay()->setHour(18)->setMinute(0)
                                                    );
                                                } elseif ($mealType === 'lunch') {
                                                    $cutoffPassed = \Carbon\Carbon::now()->greaterThan(
                                                        \Carbon\Carbon::parse($date)->setHour(8)->setMinute(0)
                                                    );
                                                } elseif ($mealType === 'dinner') {
                                                    $cutoffPassed = \Carbon\Carbon::now()->greaterThan(
                                                        \Carbon\Carbon::parse($date)->setHour(14)->setMinute(0)
                                                    );
                                                }
                                                
                                                $disabled = $isPast || ($isToday && $cutoffPassed);
                                            @endphp
                                            
                                            <tr class="{{ $isToday ? 'table-primary' : '' }} {{ $isPast ? 'text-muted' : '' }}">
                                                @if($currentDate !== $date)
                                                    <td class="align-middle" rowspan="{{ $dayMenus->groupBy('meal_type')->count() }}">
                                                        <strong>{{ $formattedDate->format('D, M d') }}</strong>
                                                        @if($isToday)
                                                            <span class="badge bg-primary">Today</span>
                                                        @endif
                                                    </td>
                                                    @php $currentDate = $date; @endphp
                                                @endif
                                                
                                                <td class="align-middle">{{ ucfirst($mealType) }}</td>
                                                
                                                <td class="align-middle">
                                                    <div class="d-flex flex-column">
                                                        @foreach($mealItems as $menuItem)
                                                            <div class="mb-1">
                                                                <strong>{{ $menuItem->menu_item }}</strong>
                                                                @if($menuItem->description)
                                                                    <small class="text-muted d-block">{{ $menuItem->description }}</small>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                
                                                <td class="align-middle">
                                                    @if($hasPreOrder)
                                                        <span class="badge bg-success">Pre-ordered</span>
                                                    @else
                                                        <span class="badge bg-secondary">Not ordered</span>
                                                    @endif
                                                </td>
                                                
                                                <td class="align-middle">
                                                    @if($disabled)
                                                        <button class="btn btn-sm btn-secondary" disabled>
                                                            Cutoff passed
                                                        </button>
                                                    @else
                                                        @if($hasPreOrder)
                                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $date }}_{{ $mealType }}">
                                                                Cancel
                                                            </button>
                                                            
                                                            <!-- Cancel Modal -->
                                                            <div class="modal fade" id="cancelModal{{ $date }}_{{ $mealType }}" tabindex="-1" aria-labelledby="cancelModalLabel{{ $date }}_{{ $mealType }}" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="cancelModalLabel{{ $date }}_{{ $mealType }}">Cancel Pre-order</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Are you sure you want to cancel your pre-order for {{ ucfirst($mealType) }} on {{ $formattedDate->format('D, M d') }}?</p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            <form action="{{ route('student.pre-order.update', $studentPreOrders[$preOrderKey]->id) }}" method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <input type="hidden" name="is_attending" value="0">
                                                                                <button type="submit" class="btn btn-danger">Cancel Pre-order</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $date }}_{{ $mealType }}">
                                                                Pre-select
                                                            </button>
                                                            
                                                            <!-- Pre-order Modal -->
                                                            <div class="modal fade" id="orderModal{{ $date }}_{{ $mealType }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $date }}_{{ $mealType }}" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="orderModalLabel{{ $date }}_{{ $mealType }}">Pre-order {{ ucfirst($mealType) }}</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <form action="{{ route('student.pre-order.store') }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="date" value="{{ $date }}">
                                                                            <input type="hidden" name="meal_type" value="{{ $mealType }}">
                                                                            
                                                                            <div class="modal-body">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Select Menu Item:</label>
                                                                                    @foreach($mealItems as $menuItem)
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio" name="menu_id" value="{{ $menuItem->id }}" id="menuItem{{ $menuItem->id }}" {{ $loop->first ? 'checked' : '' }}>
                                                                                            <label class="form-check-label" for="menuItem{{ $menuItem->id }}">
                                                                                                {{ $menuItem->menu_item }}
                                                                                                @if($menuItem->description)
                                                                                                    <small class="text-muted d-block">{{ $menuItem->description }}</small>
                                                                                                @endif
                                                                                            </label>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                                
                                                                                <div class="mb-3">
                                                                                    <label for="notes{{ $date }}_{{ $mealType }}" class="form-label">Special Instructions (optional):</label>
                                                                                    <textarea class="form-control" id="notes{{ $date }}_{{ $mealType }}" name="notes" rows="2" placeholder="Any allergies or special requests?"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Confirm Pre-order</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                
                                @if(count($menuItems ?? []) === 0)
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="bi bi-info-circle me-2"></i>No menu items available for the upcoming week
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
</style>
@endpush
