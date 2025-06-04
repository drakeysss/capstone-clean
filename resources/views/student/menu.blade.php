@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Meal Planning</h1>
       
    </div>

    <div class="row">
        <!-- Calendar View -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Weekly Meal Schedule</h6>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="btn btn-outline-primary btn-sm">
                            May 1 - May 7, 2023
                        </button>
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="100">Time</th>
                                    @foreach($weekDates as $dateInfo)
                                        <th class="{{ $dateInfo['date'] == $today ? 'bg-light' : '' }}">
                                            {{ $dateInfo['day'] }}<br>
                                            <small>{{ $dateInfo['formatted'] }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['breakfast', 'lunch', 'dinner'] as $mealType)
                                <tr>
                                    <td>{{ ucfirst($mealType) }}<br>
                                        @if($mealType == 'breakfast')
                                            <small>7:00 AM</small>
                                        @elseif($mealType == 'lunch')
                                            <small>12:00 PM</small>
                                        @else
                                            <small>6:00 PM</small>
                                        @endif
                                    </td>
                                    @foreach($weekDates as $dateInfo)
                                        <td class="meal-cell {{ $dateInfo['date'] == $today ? 'bg-light' : '' }}">
                                            @if(isset($menuItems[$dateInfo['date']]))
                                                @foreach($menuItems[$dateInfo['date']]->where('meal_type', $mealType) as $menuItem)
                                                    <div class="fw-bold">{{ $menuItem->name }}</div>
                                                    <small>{{ $menuItem->description }}</small>
                                                    <div class="mt-1">
                                                        <a href="{{ route('student.pre-order') }}?date={{ $dateInfo['date'] }}&meal_type={{ $mealType }}" class="btn btn-sm btn-outline-primary">Pre-order</a>
                                                    </div>
                                                @endforeach
                                            @else
                                                <small class="text-muted">No menu available</small>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

          


<style>
.meal-cell {
    background-color: #f8f9fc;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.meal-cell:hover {
    background-color: #eaecf4;
}

.meal-cell small {
    color: #858796;
}
</style>
@endsection
