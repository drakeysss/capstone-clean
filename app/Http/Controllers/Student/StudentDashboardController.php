<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Dashboard\BaseDashboardController;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Menu;
use App\Models\PreOrder;
use App\Models\Announcement;
use App\Models\Poll;
use App\Models\PollResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentDashboardController extends BaseDashboardController
{
    public function __construct()
    {
        parent::__construct('student', 'student');
    }
    
    /**
     * Handle the submission of student meal choices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submitMealChoices(Request $request)
    {
        // Validate the request
        $request->validate([
            'week_cycle' => 'required|in:1,2',
        ]);
        
        $weekCycle = $request->week_cycle;
        $userId = Auth::id();
        $now = Carbon::now();
        $weekStart = Carbon::now()->startOfWeek();
        
        // Process all meal choices from the form
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $mealChoices = [];
        
        foreach ($days as $day) {
            $dayDate = clone $weekStart;
            
            // Map day names to day numbers (1 = Monday, 5 = Friday)
            switch ($day) {
                case 'monday': $dayOffset = 0; break;
                case 'tuesday': $dayOffset = 1; break;
                case 'wednesday': $dayOffset = 2; break;
                case 'thursday': $dayOffset = 3; break;
                case 'friday': $dayOffset = 4; break;
                default: $dayOffset = 0;
            }
            
            $dayDate->addDays($dayOffset);
            $dateString = $dayDate->format('Y-m-d');
            
            foreach ($mealTypes as $mealType) {
                // Handle both week cycles (with or without _w2 suffix)
                $fieldName = $weekCycle == 1 ? "{$day}_{$mealType}" : "{$day}_{$mealType}_w2";
                
                if ($request->has($fieldName)) {
                    $isAttending = $request->input($fieldName) === 'yes';
                    
                    // Check if the deadline has passed
                    $deadlinePassed = false;
                    $currentDay = $now->dayOfWeek; // 1 = Monday, 7 = Sunday
                    $dayNumber = $dayOffset + 1; // Convert to 1-based day number
                    
                    // Only check deadline if it's the current day
                    if ($currentDay == $dayNumber) {
                        switch ($mealType) {
                            case 'breakfast':
                                $deadlinePassed = $now->hour >= 6; // 6:00 AM deadline
                                break;
                            case 'lunch':
                                $deadlinePassed = $now->hour >= 10; // 10:00 AM deadline
                                break;
                            case 'dinner':
                                $deadlinePassed = $now->hour >= 15; // 3:00 PM deadline
                                break;
                        }
                    }
                    
                    if (!$deadlinePassed) {
                        // Create or update the pre-order record
                        PreOrder::updateOrCreate(
                            [
                                'user_id' => $userId,
                                'date' => $dateString,
                                'meal_type' => $mealType
                            ],
                            [
                                'is_attending' => $isAttending,
                                'week_cycle' => $weekCycle
                            ]
                        );
                        
                        $mealChoices[] = [
                            'day' => ucfirst($day),
                            'meal_type' => ucfirst($mealType),
                            'attending' => $isAttending
                        ];
                    }
                }
            }
        }
        
        return redirect()->route('student.dashboard')
            ->with('success', 'Your meal choices have been submitted successfully!')
            ->with('meal_choices', $mealChoices);
    }

    /**
     * Display the student dashboard with today's menu.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $today = Carbon::today()->format('Y-m-d');
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();
        
        // Get today's menu items
        $todayMenu = Menu::where('date', $today)
            ->orderBy('meal_type')
            ->get()
            ->groupBy('meal_type');
        
        // Get student's pre-orders for today
        $studentPreOrders = PreOrder::where('user_id', Auth::id())
            ->where('date', $today)
            ->get()
            ->keyBy('meal_type');
        
        // Get active announcements
        $announcements = Announcement::where('is_active', true)
            ->where('expiry_date', '>=', $today)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get active meal polls
        $activeMealPolls = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get weekly menu for Week 1 and Week 2
        // Week 1 Menu (Current Week)
        $week1Menu = Menu::whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('meal_type')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->date)->format('l'); // Group by day of week
            });
            
        // Week 2 Menu (Next Week)
        $nextWeekStart = (clone $weekStart)->addWeek();
        $nextWeekEnd = (clone $weekEnd)->addWeek();
        $week2Menu = Menu::whereBetween('date', [$nextWeekStart->format('Y-m-d'), $nextWeekEnd->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('meal_type')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->date)->format('l'); // Group by day of week
            });
            
        // Get meal types for display
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
        
        // Get days of the week for display
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Get cutoff times for each meal type
        $cutoffTimes = [
            'breakfast' => '6:00 AM',
            'lunch' => '10:00 AM',
            'dinner' => '3:00 PM'
        ];
            
        // Get student's responses to meal polls
        $pollResponses = [];
        foreach ($activeMealPolls as $poll) {
            $response = PollResponse::where('announcement_id', $poll->id)
                ->where('user_id', Auth::id())
                ->first();
            if ($response) {
                $pollResponses[$poll->id] = [
                    'response' => $response->response
                ];
            }
        }
        
        // Calculate meal costs
        $breakfastTotal = 0;
        $lunchTotal = 0;
        $dinnerTotal = 0;
        
        if (isset($todayMenu['breakfast'])) {
            foreach ($todayMenu['breakfast'] as $item) {
                $breakfastTotal += $item->price;
            }
        }
        
        if (isset($todayMenu['lunch'])) {
            foreach ($todayMenu['lunch'] as $item) {
                $lunchTotal += $item->price;
            }
        }
        
        if (isset($todayMenu['dinner'])) {
            foreach ($todayMenu['dinner'] as $item) {
                $dinnerTotal += $item->price;
            }
        }
        
        $mealCosts = [
            'breakfast' => $breakfastTotal,
            'lunch' => $lunchTotal,
            'dinner' => $dinnerTotal
        ];
        
        // Get food waste statistics to show impact of meal attendance tracking
        $studentResponses = PreOrder::where('user_id', Auth::id())
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->where('is_attending', true)
            ->count();
            
        $pollResponsesCount = 0;
        foreach ($activeMealPolls as $poll) {
            if (PollResponse::where('poll_id', $poll->id)->where('user_id', Auth::id())->exists()) {
                $pollResponsesCount++;
            }
        }
        
        $wasteStats = [
            'weekly_reduction' => rand(15, 25), // Placeholder for actual waste reduction percentage
            'monthly_savings' => rand(500, 1500), // Placeholder for actual cost savings
            'contribution' => ($studentResponses + $pollResponsesCount) * 0.5 // Each attendance response saves ~0.5kg of food waste
        ];
        
        // Get meal times for display
        $mealTimes = [
            'breakfast' => '7:00 AM - 8:30 AM',
            'lunch' => '11:30 AM - 1:00 PM',
            'dinner' => '5:30 PM - 7:00 PM'
        ];
        
        // Get next meal type based on current time
        $currentTime = Carbon::now();
        $nextMeal = 'breakfast';
        
        if ($currentTime->hour >= 8) {
            $nextMeal = 'lunch';
        }
        
        if ($currentTime->hour >= 13) {
            $nextMeal = 'dinner';
        }
        
        if ($currentTime->hour >= 19) {
            $nextMeal = 'breakfast';
            $today = Carbon::tomorrow()->format('Y-m-d');
        }
        
        // Calculate budget tracking data
        $monthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        // Define meal costs
        $monthlyMealCosts = [
            'breakfast' => 3.50,
            'lunch' => 5.00,
            'dinner' => 6.50
        ];
        
        // Get all pre-orders for the current month
        $monthlyPreOrders = PreOrder::where('user_id', Auth::id())
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

        return view('student.dashboard', compact(
            'todayMenu',
            'studentPreOrders',
            'announcements',
            'activeMealPolls',
            'pollResponses',
            'mealTimes',
            'nextMeal',
            'today',
            'spentThisMonth',
            'remainingBudget',
            'dailyAverage',
            'breakfastTotal',
            'lunchTotal',
            'dinnerTotal',
            'mealCosts',
            'wasteStats',
            'week1Menu',
            'week2Menu',
            'mealTypes',
            'daysOfWeek',
            'cutoffTimes'
        ));
    }
    
    protected function getDashboardData()
    {
        $reports = Report::where('student_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return [
            'reports' => $reports,
            'recentOrders' => parent::getDashboardData()['recentOrders']
        ];
    }

    public function menu()
    {
        return view('student.menu');
    }

    public function orders()
    {
        return view('student.orders');
    }

    public function cart()
    {
        return view('student.cart');
    }

    public function profile()
    {
        return view('student.profile');
    }

    public function notifications()
    {
        return view('student.notifications');
    }

    public function settings()
    {
        return view('student.settings');
    }

    public function reports()
    {
        $reports = Report::where('student_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('student.reports', compact('reports'));
    }

    /**
     * Store a student's response to a meal poll.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePollResponse(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'response' => 'required|string|max:255',
        ]);
        
        // Check if user has already responded to this poll
        $existingResponse = \App\Models\PollResponse::where('announcement_id', $request->announcement_id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($existingResponse) {
            // Update existing response
            $existingResponse->update([
                'response' => $request->response,
            ]);
            
            return redirect()->back()->with('success', 'Your poll response has been updated. Thank you for your feedback!');
        }
        
        // Create new response
        \App\Models\PollResponse::create([
            'announcement_id' => $request->announcement_id,
            'user_id' => Auth::id(),
            'response' => $request->response,
        ]);
        
        return redirect()->back()->with('success', 'Your poll response has been recorded. Thank you for your feedback!');
    }
    
    public function storeReport(Request $request)
    {
        $request->validate([
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'report_date' => 'required|date',
            'meal_items' => 'required|array',
            'meal_items.*' => 'required|string',
            'feedback' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $report = Report::create([
            'student_id' => Auth::id(),
            'meal_type' => $request->meal_type,
            'report_date' => $request->report_date,
            'meal_items' => json_encode($request->meal_items),
            'feedback' => $request->feedback,
            'rating' => $request->rating
        ]);

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }
}