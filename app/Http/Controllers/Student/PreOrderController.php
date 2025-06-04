<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\PreOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreOrderController extends Controller
{
    /**
     * Display a listing of the student pre-orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get start and end date for the week (today + 7 days)
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);
        
        // Get menu items for the next week
        $menuItems = Menu::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('meal_type')
            ->get()
            ->groupBy('date');
        
        // Get student's pre-orders (meal attendance responses)
        $studentPreOrders = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function ($item) {
                return $item->date . '_' . $item->meal_type;
            });
        
        // Define cutoff times for each meal type
        $cutoffTimes = [
            'breakfast' => Carbon::today()->setHour(18)->setMinute(0), // 6 PM the day before
            'lunch' => Carbon::today()->setHour(8)->setMinute(0),     // 8 AM same day
            'dinner' => Carbon::today()->setHour(14)->setMinute(0),   // 2 PM same day
        ];
        
        // Get active meal polls for the upcoming week
        $activeMealPolls = \App\Models\Announcement::where('is_active', true)
            ->where('is_poll', true)
            ->whereDate('expiry_date', '>=', $startDate)
            ->whereDate('expiry_date', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get student's responses to meal polls
        $pollResponses = [];
        foreach ($activeMealPolls as $poll) {
            $response = $poll->pollResponses()->where('user_id', $user->id)->first();
            if ($response) {
                $pollResponses[$poll->id] = $response->response;
            }
        }
        
        // Get food waste statistics to show impact of meal attendance tracking
        $wasteStats = [
            'weekly_reduction' => rand(15, 25), // Placeholder for actual waste reduction percentage
            'monthly_savings' => rand(500, 1500), // Placeholder for actual cost savings
            'contribution' => $studentPreOrders->where('is_attending', true)->count() * 0.5 // Each attendance response saves ~0.5kg of food waste
        ];
        
        return view('student.pre-order.index', compact(
            'menuItems', 
            'studentPreOrders', 
            'startDate', 
            'endDate', 
            'cutoffTimes',
            'activeMealPolls',
            'pollResponses',
            'wasteStats'
        ));
    }

    /**
     * Store a newly created pre-order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'notes' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        $menu = Menu::findOrFail($request->menu_id);
        
        // Check if cutoff time has passed
        $now = Carbon::now();
        $orderDate = Carbon::parse($request->date);
        
        $cutoffPassed = false;
        
        if ($request->meal_type === 'breakfast') {
            // Cutoff for breakfast is 6 PM the day before
            $cutoffTime = Carbon::parse($request->date)->subDay()->setHour(18)->setMinute(0);
            $cutoffPassed = $now->greaterThan($cutoffTime);
        } elseif ($request->meal_type === 'lunch') {
            // Cutoff for lunch is 8 AM the same day
            $cutoffTime = Carbon::parse($request->date)->setHour(8)->setMinute(0);
            $cutoffPassed = $now->greaterThan($cutoffTime);
        } elseif ($request->meal_type === 'dinner') {
            // Cutoff for dinner is 2 PM the same day
            $cutoffTime = Carbon::parse($request->date)->setHour(14)->setMinute(0);
            $cutoffPassed = $now->greaterThan($cutoffTime);
        }
        
        // Check if user already has a pre-order for this date and meal type
        $existingPreOrder = PreOrder::where('user_id', $user->id)
            ->where('date', $request->date)
            ->where('meal_type', $request->meal_type)
            ->first();
            
        if ($existingPreOrder) {
            // Update existing pre-order instead of showing error
            $existingPreOrder->update([
                'menu_id' => $request->menu_id,
                'is_attending' => $request->is_attending,
                'special_requests' => $request->special_requests,
            ]);
            
            $message = $request->is_attending ? 
                'Your meal attendance has been updated. Thank you for helping reduce food waste!' : 
                'Your meal attendance has been updated. Thanks for letting us know you won\'t be attending.';
                
            return redirect()->back()->with('success', $message);
        }
        
        // Create new pre-order
        $preOrder = PreOrder::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'meal_type' => $request->meal_type,
            'menu_id' => $request->menu_id,
            'is_attending' => $request->is_attending,
            'special_requests' => $request->special_requests,
        ]);
        
        // Calculate impact on food waste reduction
        $wasteReduction = 0.5; // Approximate kg of food saved by accurate attendance tracking
        $costSaving = 2.50; // Approximate cost saving per accurate attendance record
        
        $message = $request->is_attending ? 
            "Your meal attendance has been recorded. You've helped save approximately {$wasteReduction}kg of food waste!" : 
            "Thanks for letting us know you won't be attending. This helps us prepare the right amount of food.";
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Update the specified pre-order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $preOrder = PreOrder::findOrFail($id);
        
        // Ensure the pre-order belongs to the authenticated user
        if ($preOrder->user_id !== Auth::id()) {
            return redirect()->route('student.pre-order.index')
                ->with('error', 'You are not authorized to update this pre-order.');
        }
        
        // Check if cutoff time has passed
        $now = Carbon::now();
        $orderDate = Carbon::parse($preOrder->date);
        
        $cutoffPassed = false;
        
        if ($preOrder->meal_type === 'breakfast') {
            // Cutoff for breakfast is 6 PM the day before
            $cutoffTime = Carbon::parse($preOrder->date)->subDay()->setHour(18)->setMinute(0);
            $cutoffPassed = $now->greaterThan($cutoffTime);
        } elseif ($preOrder->meal_type === 'lunch') {
            // Cutoff for lunch is 8 AM the same day
            $cutoffTime = Carbon::parse($preOrder->date)->setHour(8)->setMinute(0);
            $cutoffPassed = $now->greaterThan($cutoffTime);
        } elseif ($preOrder->meal_type === 'dinner') {
            // Cutoff for dinner is 2 PM the same day
            $cutoffTime = Carbon::parse($preOrder->date)->setHour(14)->setMinute(0);
            $cutoffPassed = $now->greaterThan($cutoffTime);
        }
        
        if ($cutoffPassed) {
            return redirect()->route('student.pre-order.index')
                ->with('error', 'The cutoff time for this meal has passed. You cannot update your pre-order.');
        }
        
        // Update pre-order
        $preOrder->update([
            'is_attending' => $request->has('is_attending') ? $request->is_attending : $preOrder->is_attending,
            'notes' => $request->has('notes') ? $request->notes : $preOrder->notes,
        ]);
        
        return redirect()->route('student.pre-order.index')
            ->with('success', 'Your pre-order has been updated successfully!');
    }

    /**
     * Display the student's pre-order history.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        $user = Auth::user();
        
        // Get student's past pre-orders (up to 30 days ago)
        $pastPreOrders = PreOrder::where('user_id', $user->id)
            ->where('date', '<', Carbon::today())
            ->where('date', '>=', Carbon::today()->subDays(30))
            ->with('menu')
            ->orderBy('date', 'desc')
            ->orderBy('meal_type')
            ->paginate(15);
        
        // Define meal costs
        $mealCosts = [
            'breakfast' => 3.50,
            'lunch' => 5.00,
            'dinner' => 6.50
        ];
        
        // Calculate budget tracking data
        $monthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        // Get all pre-orders for the current month
        $monthlyPreOrders = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('is_attending', true)
            ->with('menu')
            ->get();
        
        // Calculate total spent this month
        $spentThisMonth = 0;
        $breakfastTotal = 0;
        $lunchTotal = 0;
        $dinnerTotal = 0;
        
        foreach ($monthlyPreOrders as $preOrder) {
            $mealCost = $mealCosts[$preOrder->meal_type] ?? 5.00;
            $spentThisMonth += $mealCost;
            
            if ($preOrder->meal_type === 'breakfast') {
                $breakfastTotal += $mealCost;
            } elseif ($preOrder->meal_type === 'lunch') {
                $lunchTotal += $mealCost;
            } elseif ($preOrder->meal_type === 'dinner') {
                $dinnerTotal += $mealCost;
            }
        }
        
        // Calculate remaining budget (assuming monthly budget of $150)
        $monthlyBudget = 150.00;
        $remainingBudget = $monthlyBudget - $spentThisMonth;
        
        // Calculate daily average
        $daysInMonth = Carbon::now()->daysInMonth;
        $daysElapsed = Carbon::now()->day;
        $dailyAverage = $daysElapsed > 0 ? $spentThisMonth / $daysElapsed : 0;
        
        return view('student.pre-order.history', compact(
            'pastPreOrders',
            'mealCosts',
            'spentThisMonth',
            'remainingBudget',
            'dailyAverage',
            'breakfastTotal',
            'lunchTotal',
            'dinnerTotal'
        ));
    }
    
    /**
     * Display the student's meal spending dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get current month's data
        $currentMonth = Carbon::now()->format('F Y');
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Get previous month's data
        $previousMonth = Carbon::now()->subMonth()->format('F Y');
        $startOfPrevMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfPrevMonth = Carbon::now()->subMonth()->endOfMonth();
        
        // Calculate meal totals for current month
        $currentBreakfastTotal = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->where('meal_type', 'breakfast')
            ->where('is_attending', true)
            ->count();
            
        $currentLunchTotal = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->where('meal_type', 'lunch')
            ->where('is_attending', true)
            ->count();
            
        $currentDinnerTotal = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->where('meal_type', 'dinner')
            ->where('is_attending', true)
            ->count();
        
        // Calculate meal totals for previous month
        $prevBreakfastTotal = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$startOfPrevMonth->format('Y-m-d'), $endOfPrevMonth->format('Y-m-d')])
            ->where('meal_type', 'breakfast')
            ->where('is_attending', true)
            ->count();
            
        $prevLunchTotal = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$startOfPrevMonth->format('Y-m-d'), $endOfPrevMonth->format('Y-m-d')])
            ->where('meal_type', 'lunch')
            ->where('is_attending', true)
            ->count();
            
        $prevDinnerTotal = PreOrder::where('user_id', $user->id)
            ->whereBetween('date', [$startOfPrevMonth->format('Y-m-d'), $endOfPrevMonth->format('Y-m-d')])
            ->where('meal_type', 'dinner')
            ->where('is_attending', true)
            ->count();
        
        // Get spending by day of week
        $spendingByDayOfWeek = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0
        ];
        
        // Get monthly spending history (last 6 months)
        $monthlySpendingHistory = [];
        
        // Define meal costs
        $mealCosts = [
            'breakfast' => 2.50,
            'lunch' => 5.00,
            'dinner' => 5.00
        ];
        
        return view('student.pre-order.dashboard', compact(
            'currentBreakfastTotal',
            'currentLunchTotal',
            'currentDinnerTotal',
            'prevBreakfastTotal',
            'prevLunchTotal',
            'prevDinnerTotal',
            'spendingByDayOfWeek',
            'monthlySpendingHistory',
            'mealCosts',
            'currentMonth',
            'previousMonth'
        ));
    }
}
