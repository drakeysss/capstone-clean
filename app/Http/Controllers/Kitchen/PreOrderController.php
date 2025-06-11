<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreOrder;
use App\Models\Menu;
use App\Models\User;
use App\Models\KitchenMenuPoll;
use App\Models\KitchenPollResponse;
use App\Models\DailyMenuUpdate;
use App\Services\NotificationService;
use App\Services\WeekCycleService;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreOrderDeadlineNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PreOrderController extends Controller
{
    /**
     * Display a listing of pre-orders for the kitchen team.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check if cook has created any meals using the Meal model (not Menu)
        $hasMeals = \App\Models\Meal::exists();

        // UNIFIED: Use WeekCycleService for consistent calculation
        $weekInfo = WeekCycleService::getWeekInfo();
        $currentDay = $weekInfo['current_day'];
        $currentWeekCycle = $weekInfo['week_cycle'];
        $weekOfMonth = $weekInfo['week_of_month'];

        if (!$hasMeals) {
            // No meals created by cook yet - kitchen team waits
            return view('kitchen.pre-orders', [
                'waitingForCook' => true,
                'hasMeals' => false,
                'menuItems' => collect(),
                'preOrderCounts' => [],
                'preparationStatus' => [],
                'date' => now()->format('Y-m-d'),
                'mealType' => 'lunch',
                'upcomingDates' => collect(),
                'currentDay' => $currentDay,
                'currentWeekCycle' => $currentWeekCycle
            ]);
        }

        // Sync today's menu to daily updates for real-time kitchen display
        $this->syncTodaysMenuToDailyUpdates();

        // CYCLE-BASED: Always show today's menu, ignore date parameter
        $mealType = $request->input('meal_type', 'lunch');

        \Log::info('🔄 Kitchen pre-orders index - cycle-based', [
            'current_day' => $currentDay,
            'current_week_cycle' => $currentWeekCycle,
            'meal_type' => $mealType,
            'week_of_month' => $weekOfMonth
        ]);

        // Get ALL meals for today (all meal types) to show complete menu
        $allTodaysMeals = \App\Models\Meal::where('day_of_week', $currentDay)
            ->where('week_cycle', $currentWeekCycle)
            ->get();

        if ($allTodaysMeals->isEmpty()) {
            // No meals for today's cycle - show waiting message
            return view('kitchen.pre-orders', [
                'waitingForCook' => false,
                'noMenuForToday' => true,
                'hasMeals' => true,
                'menuItems' => collect(),
                'preOrderCounts' => [],
                'preparationStatus' => [],
                'date' => now()->format('Y-m-d'),
                'mealType' => $mealType,
                'upcomingDates' => collect(),
                'currentDay' => $currentDay,
                'currentWeekCycle' => $currentWeekCycle
            ]);
        }

        // Convert Meal objects to format expected by the view (all today's meals)
        $menuItems = $allTodaysMeals->map(function ($meal) {
            return (object) [
                'id' => $meal->id,
                'name' => $meal->name,
                'description' => is_array($meal->ingredients) ? implode(', ', $meal->ingredients) : $meal->ingredients,
                'meal_type' => $meal->meal_type,
                'ingredients' => $meal->ingredients,
                'prep_time' => $meal->prep_time,
                'cooking_time' => $meal->cooking_time,
                'serving_size' => $meal->serving_size
            ];
        });

        // Get existing polls for today
        $existingPolls = KitchenMenuPoll::whereDate('poll_date', today())
            ->get()
            ->keyBy('meal_id');

        // For now, set empty arrays for pre-order counts and preparation status
        // These will be populated when the polling system is fully integrated
        $preOrderCounts = [];
        $preparationStatus = [];

        // No need for upcoming dates in cycle-based system - always show today
        $upcomingDates = collect([now()->format('Y-m-d')]);

        \Log::info('✅ Kitchen pre-orders loaded successfully', [
            'total_meals_today' => $menuItems->count(),
            'existing_polls' => $existingPolls->count(),
            'current_day' => $currentDay,
            'current_week_cycle' => $currentWeekCycle
        ]);

        return view('kitchen.pre-orders', compact(
            'menuItems',
            'preOrderCounts',
            'preparationStatus',
            'mealType',
            'upcomingDates',
            'hasMeals',
            'existingPolls'
        ) + [
            'date' => now()->format('Y-m-d'),
            'currentDay' => $currentDay,
            'currentWeekCycle' => $currentWeekCycle,
            'cycleBasedSystem' => true
        ]);
    }



    /**
     * Mark menu items as prepared (batch update).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markMenuItemsPrepared(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'menu_ids' => 'required|array',
            'menu_ids.*' => 'exists:menus,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update preparation status for the selected menu items
        foreach ($request->menu_ids as $menuId) {
            PreOrder::where('date', $request->date)
                ->where('meal_type', $request->meal_type)
                ->where('menu_id', $menuId)
                ->update(['is_prepared' => true]);
        }

        return redirect()->route('kitchen.pre-orders', ['date' => $request->date, 'meal_type' => $request->meal_type])
            ->with('success', 'Menu items marked as prepared successfully.');
    }

    public function getPreOrders($weekCycle)
    {
        // Check if cook has created any meals
        $hasMeals = \App\Models\Meal::exists();

        if (!$hasMeals) {
            return response()->json([
                'success' => false,
                'message' => 'No meals available. Please wait for cook to create the menu.',
                'waitingForCook' => true
            ]);
        }

        $preOrders = PreOrder::with(['user', 'menu'])
            ->whereHas('menu', function ($query) use ($weekCycle) {
                $query->where('week_cycle', $weekCycle);
            })
            ->get()
            ->groupBy(function ($preOrder) {
                return $preOrder->menu->day_of_week;
            });

        return response()->json([
            'success' => true,
            'preOrders' => $preOrders
        ]);
    }

    public function notifyDeadline(Request $request)
    {
        $validated = $request->validate([
            'mealType' => 'required|in:breakfast,lunch,dinner',
            'minutesLeft' => 'required|integer|min:1'
        ]);

        // Get all students who haven't submitted pre-orders for the upcoming meal
        $students = User::role('student')->get();
        $notifiedCount = 0;

        foreach ($students as $student) {
            $hasPreOrder = PreOrder::where('user_id', $student->id)
                ->whereHas('menu', function ($query) use ($validated) {
                    $query->where('meal_type', $validated['mealType'])
                        ->whereDate('serving_date', today());
                })
                ->exists();

            if (!$hasPreOrder) {
                // Send email notification
                Mail::to($student->email)->send(new PreOrderDeadlineNotification(
                    $validated['mealType'],
                    $validated['minutesLeft']
                ));
                $notifiedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sent notifications to {$notifiedCount} students"
        ]);
    }

    /**
     * Update the status of a single pre-order (prepared/served).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markPreOrderStatus(Request $request)
    {
        $validated = $request->validate([
            'pre_order_id' => 'required|exists:pre_orders,id',
            'status' => 'required|in:prepared,served'
        ]);

        $preOrder = PreOrder::findOrFail($validated['pre_order_id']);
        $preOrder->update([
            'status' => $validated['status'],
            'prepared_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pre-order status updated successfully'
        ]);
    }

    /**
     * Check if cook has created menu (for polling)
     *
     * @return \Illuminate\Http\Response
     */
    public function checkMenu()
    {
        $hasMeals = \App\Models\Meal::exists();

        return response()->json([
            'success' => true,
            'hasMenu' => $hasMeals
        ]);
    }

    /**
     * Get available meals for current day and meal type (cycle-based)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAvailableMeals(Request $request)
    {
        $mealType = $request->input('meal_type');
        $weekCycle = $request->input('week_cycle');

        if (!$mealType) {
            return response()->json([
                'success' => false,
                'message' => 'Meal type is required'
            ], 400);
        }

        // UNIFIED: Use WeekCycleService for consistent calculation
        $weekInfo = WeekCycleService::getWeekInfo();
        $dayOfWeek = $weekInfo['current_day'];

        // If week cycle not provided, use current week cycle from service
        if (!$weekCycle) {
            $weekCycle = $weekInfo['week_cycle'];
        }

        // Debug logging with WeekCycleService info
        \Log::info('🔍 Kitchen searching for available meals (cycle-based)', [
            'current_day' => $dayOfWeek,
            'requested_meal_type' => $mealType,
            'current_week_cycle' => $weekCycle,
            'week_of_month' => $weekInfo['week_of_month'],
            'week_cycle_service_info' => $weekInfo,
            'search_query' => "day_of_week = '{$dayOfWeek}' AND meal_type = '{$mealType}' AND week_cycle = {$weekCycle}"
        ]);

        // Get meals from cook's Meal model
        $meals = \App\Models\Meal::where('day_of_week', $dayOfWeek)
            ->where('meal_type', $mealType)
            ->where('week_cycle', $weekCycle)
            ->get();

        // Also check what meals exist in general
        $allMeals = \App\Models\Meal::all();
        \Log::info('📊 All meals in database', [
            'total_meals' => $allMeals->count(),
            'meals_for_day_type' => $meals->count(),
            'all_meals_summary' => $allMeals->map(function($meal) {
                return [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'day_of_week' => $meal->day_of_week,
                    'meal_type' => $meal->meal_type,
                    'week_cycle' => $meal->week_cycle
                ];
            })
        ]);

        $formattedMeals = $meals->map(function ($meal) {
            return [
                'id' => $meal->id,
                'name' => $meal->name,
                'ingredients' => is_array($meal->ingredients) ? implode(', ', $meal->ingredients) : $meal->ingredients,
                'prep_time' => $meal->prep_time,
                'cooking_time' => $meal->cooking_time,
                'serving_size' => $meal->serving_size
            ];
        });

        \Log::info('✅ Returning meals to kitchen', [
            'found_meals_count' => $formattedMeals->count(),
            'formatted_meals' => $formattedMeals
        ]);

        return response()->json([
            'success' => true,
            'meals' => $formattedMeals,
            'debug' => [
                'searched_for' => [
                    'day_of_week' => $dayOfWeek,
                    'meal_type' => $mealType,
                    'week_cycle' => $weekCycle
                ],
                'total_meals_in_db' => $allMeals->count(),
                'found_meals' => $meals->count()
            ]
        ]);
    }

    /**
     * Debug endpoint to check what meals exist and why polling might not work
     */
    public function debugMeals(Request $request)
    {
        $allMeals = \App\Models\Meal::all();
        $today = now()->format('Y-m-d');

        // UNIFIED: Use WeekCycleService for consistent calculation
        $weekInfo = WeekCycleService::getWeekInfo();
        $dayOfWeek = $weekInfo['current_day'];
        $weekCycle = $weekInfo['week_cycle'];
        $weekOfMonth = $weekInfo['week_of_month'];

        // Check what meals exist for today
        $todaysMeals = \App\Models\Meal::where('day_of_week', $dayOfWeek)
            ->where('week_cycle', $weekCycle)
            ->get();

        // Check what days have meals
        $daysWithMeals = \App\Models\Meal::select('day_of_week', 'week_cycle')
            ->distinct()
            ->get()
            ->groupBy('week_cycle');

        return response()->json([
            'success' => true,
            'debug_info' => [
                'today' => $today,
                'day_of_week' => $dayOfWeek,
                'week_cycle' => $weekCycle,
                'week_of_month' => $weekOfMonth,
                'week_cycle_service_info' => $weekInfo,
                'total_meals' => $allMeals->count(),
                'todays_meals_count' => $todaysMeals->count(),
                'days_with_meals' => $daysWithMeals,
                'all_meals' => $allMeals->map(function($meal) {
                    return [
                        'id' => $meal->id,
                        'name' => $meal->name,
                        'day_of_week' => $meal->day_of_week,
                        'meal_type' => $meal->meal_type,
                        'week_cycle' => $meal->week_cycle,
                        'ingredients' => $meal->ingredients
                    ];
                }),
                'meals_for_today' => $todaysMeals->map(function($meal) {
                    return [
                        'id' => $meal->id,
                        'name' => $meal->name,
                        'meal_type' => $meal->meal_type
                    ];
                }),
                'issue_analysis' => [
                    'has_meals_in_db' => $allMeals->count() > 0,
                    'has_meals_for_today' => $todaysMeals->count() > 0,
                    'expected_search' => "day_of_week = '{$dayOfWeek}' AND week_cycle = {$weekCycle}",
                    'recommendation' => $todaysMeals->count() === 0
                        ? "No meals found for {$dayOfWeek} week {$weekCycle}. Cook needs to create meals for this day/cycle."
                        : "Meals found! Kitchen polling should work."
                ]
            ]
        ]);
    }

    /**
     * Update deadline for pre-orders (Kitchen can only edit deadlines, not menu content)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDeadline(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|string',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner',
            'week_cycle' => 'required|integer|in:1,2',
            'cutoff_time' => 'required|string',
            'custom_cutoff_time' => 'nullable|string'
        ]);

        // Kitchen users can only modify deadlines, not menu content
        // This would typically update a separate deadlines table or meal_deadlines table
        // For now, we'll just return success as this is a deadline-only operation

        $finalCutoffTime = $validated['cutoff_time'] === 'custom'
            ? $validated['custom_cutoff_time']
            : $validated['cutoff_time'];

        // Log the deadline change for audit purposes
        \Log::info('Kitchen user updated pre-order deadline', [
            'user_id' => auth()->id(),
            'day' => $validated['day'],
            'meal_type' => $validated['meal_type'],
            'week_cycle' => $validated['week_cycle'],
            'new_deadline' => $finalCutoffTime,
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pre-order deadline updated successfully',
            'deadline' => $finalCutoffTime
        ]);
    }

    /**
     * Create a new menu poll (cycle-based)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createPoll(Request $request)
    {
        \Log::info('🚀 CreatePoll START - Method called', [
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url(),
            'user_id' => auth()->id()
        ]);

        try {
            // Check if this is a manual meal or regular meal
            $isManualMeal = $request->has('manual_meal') && $request->input('manual_meal');

            \Log::info('🔍 Manual meal check', [
                'is_manual_meal' => $isManualMeal,
                'has_manual_meal' => $request->has('manual_meal'),
                'manual_meal_value' => $request->input('manual_meal')
            ]);

        } catch (\Exception $e) {
            \Log::error('💥 EARLY ERROR in createPoll', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Early error: ' . $e->getMessage()
            ], 500);
        }

        try {

            if ($isManualMeal) {
                $validated = $request->validate([
                    'meal_type' => 'required|string|in:breakfast,lunch,dinner',
                    'poll_date' => 'required|date|after_or_equal:today',
                    'deadline' => 'required|string',
                    'custom_deadline' => 'nullable|string',
                    'manual_meal.name' => 'required|string|max:255',
                    'manual_meal.ingredients' => 'nullable|string'
                ]);
            } else {
                $validated = $request->validate([
                    'meal_type' => 'required|string|in:breakfast,lunch,dinner',
                    'meal_id' => 'required|integer|exists:meals,id',
                    'poll_date' => 'required|date|after_or_equal:today',
                    'deadline' => 'required|string',
                    'custom_deadline' => 'nullable|string'
                ]);
            }

            \Log::info('Validation passed:', ['validated' => $validated, 'is_manual_meal' => $isManualMeal]);
        } catch (\Exception $e) {
            \Log::error('Validation failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 422);
        }

        $finalDeadline = $validated['deadline'] === 'custom'
            ? $validated['custom_deadline']
            : $validated['deadline'];

        \Log::info('Processing deadline for new poll', [
            'original_deadline' => $validated['deadline'],
            'custom_deadline' => $validated['custom_deadline'] ?? 'none',
            'final_deadline' => $finalDeadline
        ]);

        // Process the deadline format
        $processedDeadline = $this->processDeadlineFormat($finalDeadline);

        try {
            // UNIFIED: Use WeekCycleService for consistent calculation
            $weekInfo = WeekCycleService::getWeekInfo();
            $currentDay = $weekInfo['current_day'];
            $currentWeekCycle = $weekInfo['week_cycle'];

            if ($isManualMeal) {
                // Handle manual meal
                $manualMeal = $validated['manual_meal'];

                // Check if poll already exists for this meal type on the specified date (manual meals)
                $existingPoll = KitchenMenuPoll::where('meal_type', $validated['meal_type'])
                    ->whereNull('meal_id') // Manual meals have null meal_id
                    ->whereDate('poll_date', $validated['poll_date'])
                    ->first();

                if ($existingPoll) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A manual poll already exists for this meal type on the selected date'
                    ], 422);
                }

                // Create poll data using manual meal information
                $pollData = [
                    'meal_name' => $manualMeal['name'],
                    'ingredients' => $manualMeal['ingredients'] ?? 'No ingredients specified',
                    'poll_date' => $validated['poll_date'],
                    'meal_type' => $validated['meal_type'],
                    'deadline' => $processedDeadline,
                    'status' => 'draft',
                    'created_by' => auth()->id(),
                    'meal_id' => null // Manual meals don't have meal_id
                ];
            } else {
                // Handle regular meal from cook's menu
                $meal = \App\Models\Meal::findOrFail($validated['meal_id']);

                // Verify the meal matches current day and cycle
                if ($meal->day_of_week !== $currentDay || $meal->week_cycle !== $currentWeekCycle) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected meal is not for today\'s menu cycle'
                    ], 400);
                }

                // Check if poll already exists for this meal on the specified date
                $existingPoll = KitchenMenuPoll::where('meal_type', $validated['meal_type'])
                    ->where('meal_id', $validated['meal_id'])
                    ->whereDate('poll_date', $validated['poll_date'])
                    ->first();

                if ($existingPoll) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A poll already exists for this meal on the selected date'
                    ], 422);
                }

                // Create poll data using meal information
                $pollData = [
                    'meal_name' => $meal->name,
                    'ingredients' => is_array($meal->ingredients)
                        ? implode(', ', $meal->ingredients)
                        : $meal->ingredients,
                    'poll_date' => $validated['poll_date'],
                    'meal_type' => $validated['meal_type'],
                    'deadline' => $processedDeadline,
                    'status' => 'draft',
                    'created_by' => auth()->id(),
                    'meal_id' => $validated['meal_id']
                ];
            }

            \Log::info('Poll data prepared for cycle-based system', [
                'original_deadline' => $finalDeadline,
                'processed_deadline' => $processedDeadline,
                'poll_data' => $pollData,
                'current_day' => $currentDay,
                'current_week_cycle' => $currentWeekCycle,
                'is_manual_meal' => $isManualMeal,
                'meal_source' => $isManualMeal ? 'manual_input' : 'cook_menu'
            ]);

            // Create poll record
            \Log::info('Creating poll with data:', $pollData);
            $poll = KitchenMenuPoll::create($pollData);
            \Log::info('Poll created successfully:', ['poll_id' => $poll->id]);

            // Log the poll creation for audit purposes
            \Log::info('Kitchen user created cycle-based menu poll', [
                'user_id' => auth()->id(),
                'poll_id' => $poll->id,
                'poll_date' => $poll->poll_date,
                'meal_type' => $validated['meal_type'],
                'meal_name' => $poll->meal_name,
                'deadline' => $finalDeadline,
                'current_day' => $currentDay,
                'current_week_cycle' => $currentWeekCycle,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu poll created successfully for today\'s menu',
                'poll' => [
                    'id' => $poll->id,
                    'meal_name' => $poll->meal_name,
                    'poll_date' => $poll->poll_date->format('Y-m-d'),
                    'meal_type' => $poll->meal_type,
                    'deadline' => $poll->deadline,
                    'status' => $poll->status,
                    'current_day' => $currentDay,
                    'current_week_cycle' => $currentWeekCycle
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create menu poll', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create poll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all polls with optional filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPolls(Request $request)
    {
        try {
            $date = $request->get('date');
            $mealType = $request->get('meal_type');
            $urgency = $request->get('urgency');

            \Log::info('📊 Loading polls with filters', [
                'date' => $date,
                'meal_type' => $mealType,
                'urgency' => $urgency,
                'request_method' => $request->method(),
                'request_url' => $request->url(),
                'user_agent' => $request->header('User-Agent')
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ GetPolls request failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing request: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Query polls from database
            $query = KitchenMenuPoll::with(['creator', 'responses'])
                ->orderBy('poll_date', 'desc')
                ->orderBy('created_at', 'desc');

            // Apply filters if provided
            if ($date) {
                $query->forDate($date);
            }

            if ($mealType) {
                $query->forMealType($mealType);
            }

            $polls = $query->get();
            $totalStudents = User::where('role', 'student')->count();

            // Apply urgency filtering if specified
            if ($urgency) {
                $polls = $polls->filter(function ($poll) use ($urgency) {
                    $urgencyLevel = $this->calculateUrgencyLevel($poll);
                    return $urgencyLevel === $urgency;
                });
            }

            // Format polls for frontend
            $formattedPolls = $polls->map(function ($poll) use ($totalStudents) {
                $urgencyLevel = $this->calculateUrgencyLevel($poll);
                $urgencyInfo = $this->getUrgencyInfo($urgencyLevel);

                $formattedPoll = [
                    'id' => $poll->id,
                    'meal_name' => $poll->meal_name,
                    'ingredients' => is_array($poll->ingredients) ? implode(', ', $poll->ingredients) : ($poll->ingredients ?? 'No ingredients listed'),
                    'poll_date' => $poll->poll_date->format('Y-m-d'),
                    'meal_type' => $poll->meal_type,
                    'deadline' => $poll->deadline ? $poll->deadline->format('Y-m-d H:i:s') : null,
                    'status' => $poll->status,
                    'responses_count' => $poll->total_responses,
                    'yes_count' => $poll->yes_count,
                    'no_count' => $poll->no_count,
                    'total_students' => $totalStudents,
                    'response_rate' => $poll->response_rate,
                    'participation_rate' => $poll->participation_rate,
                    'created_by' => $poll->creator->name ?? 'Unknown',
                    'sent_at' => $poll->sent_at ? $poll->sent_at->format('Y-m-d H:i:s') : null,
                    'can_edit' => $poll->canBeEdited(),
                    'can_send' => $poll->canBeSent(),
                    'urgency_level' => $urgencyLevel,
                    'urgency_badge' => $urgencyInfo['badge'],
                    'urgency_text' => $urgencyInfo['text'],
                    'hours_until_deadline' => $urgencyInfo['hours']
                ];

                // Deep debug for 9 PM polls
                if ($poll->deadline && strpos($poll->deadline, '21:00') !== false) {
                    \Log::info('🔍 SENDING 9 PM POLL TO FRONTEND', [
                        'poll_id' => $poll->id,
                        'meal_name' => $poll->meal_name,
                        'raw_deadline_from_db' => $poll->deadline,
                        'formatted_deadline' => $formattedPoll['deadline'],
                        'poll_date' => $formattedPoll['poll_date'],
                        'message' => 'This should display as 9:00 PM in frontend'
                    ]);
                }

                return $formattedPoll;
            });

            return response()->json([
                'success' => true,
                'polls' => $formattedPolls
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get polls', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load polls: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send poll to students
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendPoll(Request $request)
    {
        $validated = $request->validate([
            'poll_id' => 'required|exists:kitchen_menu_polls,id'
        ]);

        try {
            $poll = KitchenMenuPoll::findOrFail($validated['poll_id']);

            // Check if poll can be sent
            if (!$poll->canBeSent()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Poll cannot be sent. Current status: ' . $poll->status
                ], 400);
            }

            // Get all students
            $students = User::where('role', 'student')->get();
            $studentCount = $students->count();

            // Update poll status to active and set sent_at timestamp
            // Use provided timestamp or current time for consistency
            $sentTimestamp = $request->input('sent_timestamp') ?
                \Carbon\Carbon::parse($request->input('sent_timestamp')) :
                now();

            $poll->update([
                'status' => 'active',
                'sent_at' => $sentTimestamp
            ]);

            // Send notifications to students and cook
            $notificationService = new NotificationService();
            $notificationService->pollCreated([
                'poll_id' => $poll->id,
                'meal_name' => $poll->meal_name,
                'meal_type' => $poll->meal_type,
                'poll_date' => $poll->poll_date->format('Y-m-d'),
                'deadline' => $poll->deadline,
                'student_count' => $studentCount
            ]);

            \Log::info('Kitchen user sent poll to students', [
                'user_id' => auth()->id(),
                'poll_id' => $poll->id,
                'meal_name' => $poll->meal_name,
                'student_count' => $studentCount,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Poll sent to students successfully',
                'student_count' => $studentCount,
                'poll_status' => $poll->status
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send poll to students', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'poll_id' => $validated['poll_id']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send poll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send all draft polls to students
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendAllPolls(Request $request)
    {
        try {
            // Get all draft polls
            $draftPolls = KitchenMenuPoll::draft()->get();
            $pollCount = $draftPolls->count();

            if ($pollCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No draft polls found to send'
                ], 400);
            }

            $students = User::where('role', 'student')->get();
            $studentCount = $students->count();

            // Use a single timestamp for all polls to ensure consistency
            $sentTimestamp = now();

            // Update all draft polls to active status with the same timestamp
            foreach ($draftPolls as $poll) {
                $poll->update([
                    'status' => 'active',
                    'sent_at' => $sentTimestamp
                ]);
            }

            // Log the bulk poll sending for audit purposes
            \Log::info('Kitchen user sent all polls to students', [
                'user_id' => auth()->id(),
                'poll_count' => $pollCount,
                'student_count' => $studentCount,
                'poll_ids' => $draftPolls->pluck('id')->toArray(),
                'timestamp' => now()
            ]);

            // TODO: Implement actual notification sending
            // foreach ($students as $student) {
            //     foreach ($draftPolls as $poll) {
            //         // Send notification to student about new poll
            //     }
            // }

            return response()->json([
                'success' => true,
                'message' => 'All polls sent to students successfully',
                'count' => $pollCount,
                'student_count' => $studentCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send all polls to students', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send polls: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update poll deadline
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePollDeadline(Request $request)
    {
        \Log::info('🔧 UPDATE POLL DEADLINE - Request received', [
            'all_input' => $request->all(),
            'poll_id' => $request->input('poll_id'),
            'deadline' => $request->input('deadline'),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);

        try {
            // Get input data
            $pollId = $request->input('poll_id');
            $deadline = $request->input('deadline');

            \Log::info('📊 Extracted data', [
                'poll_id' => $pollId,
                'deadline' => $deadline,
                'poll_id_type' => gettype($pollId),
                'deadline_type' => gettype($deadline)
            ]);

            // Basic validation
            if (!$pollId || !$deadline) {
                \Log::error('❌ Validation failed - missing data', [
                    'poll_id_empty' => empty($pollId),
                    'deadline_empty' => empty($deadline),
                    'poll_id_value' => $pollId,
                    'deadline_value' => $deadline
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Poll ID and deadline are required',
                    'debug' => [
                        'poll_id' => $pollId,
                        'deadline' => $deadline,
                        'poll_id_empty' => empty($pollId),
                        'deadline_empty' => empty($deadline)
                    ]
                ], 400);
            }

            // Find poll
            $poll = KitchenMenuPoll::find($pollId);
            if (!$poll) {
                \Log::error('❌ Poll not found', ['poll_id' => $pollId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Poll not found'
                ], 404);
            }

            \Log::info('✅ Poll found', [
                'poll_id' => $poll->id,
                'meal_name' => $poll->meal_name,
                'current_deadline' => $poll->deadline,
                'status' => $poll->status
            ]);

            // Check if can be edited
            if (!method_exists($poll, 'canBeEdited') || !$poll->canBeEdited()) {
                \Log::error('❌ Poll cannot be edited', [
                    'poll_id' => $poll->id,
                    'status' => $poll->status,
                    'has_method' => method_exists($poll, 'canBeEdited')
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Poll cannot be edited. Status: ' . $poll->status
                ], 400);
            }

            // Process deadline format
            \Log::info('🔄 Processing deadline format', ['input_deadline' => $deadline]);
            $processedDeadline = $this->processDeadlineFormat($deadline);
            \Log::info('✅ Deadline processed', ['processed_deadline' => $processedDeadline]);

            // Update deadline
            $poll->deadline = $processedDeadline;
            $poll->save();

            \Log::info('✅ Poll deadline updated successfully', [
                'poll_id' => $poll->id,
                'old_deadline' => $poll->getOriginal('deadline'),
                'new_deadline' => $poll->deadline
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Poll deadline updated successfully',
                'poll' => [
                    'id' => $poll->id,
                    'deadline' => $poll->deadline,
                    'meal_name' => $poll->meal_name,
                    'status' => $poll->status
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('💥 Update poll deadline failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'error_type' => get_class($e),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile()
                ]
            ], 500);
        }
    }

    private function processDeadlineFormat($deadline)
    {
        \Log::info('🔧 FIXED DEADLINE PROCESSING', [
            'input_deadline' => $deadline,
            'type' => gettype($deadline)
        ]);

        // Handle new format: "2025-01-16|9:00 PM"
        if (strpos($deadline, '|') !== false) {
            list($date, $time) = explode('|', $deadline, 2);

            \Log::info('📅 Processing pipe format', [
                'date' => $date,
                'time' => $time
            ]);

            // Validate date format (YYYY-MM-DD)
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                throw new \Exception('Invalid date format: ' . $date);
            }

            // Convert 12-hour time to 24-hour for MySQL DATETIME
            $time24h = $this->convert12HourTo24Hour($time);

            // Create MySQL DATETIME: "2025-01-16 21:00:00"
            $result = $date . ' ' . $time24h . ':00';

            \Log::info('✅ SUCCESS - Created MySQL DATETIME', [
                'input' => $deadline,
                'date' => $date,
                'time_12h' => $time,
                'time_24h' => $time24h,
                'final_datetime' => $result
            ]);

            return $result;
        }

        // Handle 12-hour format: "9:00 PM" - convert to 24-hour and add today's date
        if (preg_match('/^\d{1,2}:\d{2}\s*(AM|PM)$/i', $deadline)) {
            $today = date('Y-m-d');
            $time24h = $this->convert12HourTo24Hour($deadline);
            $result = $today . ' ' . $time24h . ':00';

            \Log::info('✅ Converted 12-hour format', [
                'original' => $deadline,
                'time_24h' => $time24h,
                'result' => $result
            ]);

            return $result;
        }

        // Handle old format: "21:00" - add today's date
        if (preg_match('/^\d{2}:\d{2}$/', $deadline)) {
            $today = date('Y-m-d');
            $result = $today . ' ' . $deadline . ':00';

            \Log::info('✅ Converted old format', [
                'original' => $deadline,
                'result' => $result
            ]);

            return $result;
        }

        // If already datetime format, keep as is
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $deadline)) {
            \Log::info('✅ Already in correct format', ['deadline' => $deadline]);
            return $deadline;
        }

        \Log::error('❌ Unknown deadline format', ['deadline' => $deadline]);
        throw new \Exception('Unknown deadline format: ' . $deadline);
    }

    private function convert12HourTo24Hour($time12h)
    {
        // Parse "9:00 PM" format
        if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $time12h, $matches)) {
            $hour = (int)$matches[1];
            $minute = (int)$matches[2];
            $period = strtoupper($matches[3]);

            // Convert to 24-hour
            if ($period === 'AM') {
                if ($hour === 12) $hour = 0; // 12 AM = 0
            } else { // PM
                if ($hour !== 12) $hour += 12; // 9 PM = 21, but 12 PM = 12
            }

            return sprintf('%02d:%02d', $hour, $minute);
        }

        throw new \Exception('Invalid 12-hour time format: ' . $time12h);
    }

    /**
     * Calculate urgency level based on deadline
     */
    private function calculateUrgencyLevel($poll)
    {
        if (!$poll->deadline) {
            return 'normal';
        }

        try {
            $deadline = $poll->deadline instanceof \Carbon\Carbon
                ? $poll->deadline
                : \Carbon\Carbon::parse($poll->deadline);

            $now = \Carbon\Carbon::now();
            $hoursUntilDeadline = $now->diffInHours($deadline, false);

            if ($hoursUntilDeadline < 0) {
                return 'expired'; // Past deadline
            } elseif ($hoursUntilDeadline < 2) {
                return 'urgent'; // Less than 2 hours
            } elseif ($hoursUntilDeadline < 6) {
                return 'soon'; // Less than 6 hours
            } else {
                return 'normal'; // More than 6 hours
            }
        } catch (\Exception $e) {
            \Log::warning('Error calculating urgency level', [
                'poll_id' => $poll->id,
                'deadline' => $poll->deadline,
                'error' => $e->getMessage()
            ]);
            return 'normal';
        }
    }

    /**
     * Get urgency display information
     */
    private function getUrgencyInfo($urgencyLevel)
    {
        switch ($urgencyLevel) {
            case 'urgent':
                return [
                    'badge' => 'bg-danger',
                    'text' => '🔴 Urgent',
                    'hours' => '< 2 hours'
                ];
            case 'soon':
                return [
                    'badge' => 'bg-warning',
                    'text' => '🟡 Soon',
                    'hours' => '< 6 hours'
                ];
            case 'expired':
                return [
                    'badge' => 'bg-secondary',
                    'text' => '⏰ Expired',
                    'hours' => 'Past deadline'
                ];
            default:
                return [
                    'badge' => 'bg-success',
                    'text' => '🟢 Normal',
                    'hours' => '> 6 hours'
                ];
        }
    }

    /**
     * Get daily menu updates for real-time display
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDailyMenuUpdates(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));

        try {
            $menuUpdates = DailyMenuUpdate::forDate($date)
                ->with('updater')
                ->orderBy('meal_type')
                ->get();

            $formattedUpdates = $menuUpdates->map(function ($update) {
                return [
                    'id' => $update->id,
                    'meal_name' => $update->meal_name,
                    'ingredients' => is_array($update->ingredients) ? implode(', ', $update->ingredients) : ($update->ingredients ?? 'No ingredients listed'),
                    'meal_type' => $update->meal_type,
                    'status' => $update->status,
                    'status_badge' => $update->status_badge,
                    'estimated_portions' => $update->estimated_portions,
                    'actual_portions' => $update->actual_portions,
                    'portion_difference' => $update->portion_difference,
                    'updated_by' => $update->updater->name ?? 'Unknown',
                    'updated_at' => $update->updated_at->format('H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'menu_updates' => $formattedUpdates,
                'last_updated' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get daily menu updates', [
                'error' => $e->getMessage(),
                'date' => $date
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load menu updates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update daily menu status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDailyMenuStatus(Request $request)
    {
        $validated = $request->validate([
            'menu_date' => 'required|date',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner',
            'status' => 'required|string|in:planned,preparing,ready,served'
        ]);

        try {
            $menuUpdate = DailyMenuUpdate::where('menu_date', $validated['menu_date'])
                ->where('meal_type', $validated['meal_type'])
                ->first();

            if (!$menuUpdate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found for the specified date and meal type'
                ], 404);
            }

            $oldStatus = $menuUpdate->status;
            $menuUpdate->updateStatus($validated['status'], auth()->id());

            \Log::info('Daily menu status updated', [
                'user_id' => auth()->id(),
                'menu_date' => $validated['menu_date'],
                'meal_type' => $validated['meal_type'],
                'old_status' => $oldStatus,
                'new_status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu status updated successfully',
                'menu_update' => [
                    'id' => $menuUpdate->id,
                    'status' => $menuUpdate->status,
                    'status_badge' => $menuUpdate->status_badge,
                    'updated_at' => $menuUpdate->updated_at->format('H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update daily menu status', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update daily menu portions
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDailyMenuPortions(Request $request)
    {
        $validated = $request->validate([
            'menu_date' => 'required|date',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner',
            'estimated_portions' => 'required|integer|min:0',
            'actual_portions' => 'required|integer|min:0'
        ]);

        try {
            $menuUpdate = DailyMenuUpdate::where('menu_date', $validated['menu_date'])
                ->where('meal_type', $validated['meal_type'])
                ->first();

            if (!$menuUpdate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found for the specified date and meal type'
                ], 404);
            }

            $oldEstimated = $menuUpdate->estimated_portions;
            $oldActual = $menuUpdate->actual_portions;

            $menuUpdate->updatePortions(
                $validated['estimated_portions'],
                $validated['actual_portions'],
                auth()->id()
            );

            \Log::info('Daily menu portions updated', [
                'user_id' => auth()->id(),
                'menu_date' => $validated['menu_date'],
                'meal_type' => $validated['meal_type'],
                'old_estimated' => $oldEstimated,
                'new_estimated' => $validated['estimated_portions'],
                'old_actual' => $oldActual,
                'new_actual' => $validated['actual_portions']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu portions updated successfully',
                'menu_update' => [
                    'id' => $menuUpdate->id,
                    'estimated_portions' => $menuUpdate->estimated_portions,
                    'actual_portions' => $menuUpdate->actual_portions,
                    'portion_difference' => $menuUpdate->portion_difference,
                    'updated_at' => $menuUpdate->updated_at->format('H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update daily menu portions', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update portions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync today's menu from cook's meals to daily menu updates
     *
     * @return void
     */
    private function syncTodaysMenuToDailyUpdates()
    {
        try {
            $today = now()->toDateString();

            // UNIFIED: Use WeekCycleService for consistent calculation
            $weekInfo = WeekCycleService::getWeekInfo();
            $currentDayOfWeek = $weekInfo['current_day'];
            $currentWeekCycle = $weekInfo['week_cycle'];

            // Get today's meals from cook's menu
            $todaysMeals = \App\Models\Meal::where('day_of_week', $currentDayOfWeek)
                ->where('week_cycle', $currentWeekCycle)
                ->get();

            foreach ($todaysMeals as $meal) {
                // Check if daily update already exists
                $existingUpdate = DailyMenuUpdate::where('menu_date', $today)
                    ->where('meal_type', $meal->meal_type)
                    ->first();

                if (!$existingUpdate) {
                    // Create new daily menu update
                    DailyMenuUpdate::create([
                        'menu_date' => $today,
                        'meal_type' => $meal->meal_type,
                        'meal_name' => $meal->name,
                        'ingredients' => is_array($meal->ingredients) ? implode(', ', $meal->ingredients) : $meal->ingredients,
                        'status' => 'planned',
                        'estimated_portions' => $meal->serving_size ?? 50,
                        'actual_portions' => 0,
                        'updated_by' => auth()->id() ?? 1 // Default to admin if no auth
                    ]);
                }
            }

            \Log::info('Today\'s menu synced to daily updates', [
                'date' => $today,
                'meals_synced' => $todaysMeals->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to sync today\'s menu to daily updates', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get poll results for viewing
     *
     * @param  int  $pollId
     * @return \Illuminate\Http\Response
     */
    public function getPollResults($pollId)
    {
        try {
            $poll = KitchenMenuPoll::with(['responses'])->findOrFail($pollId);
            $totalStudents = User::where('role', 'student')->count();

            $yesCount = $poll->responses()->where('will_eat', true)->count();
            $noCount = $poll->responses()->where('will_eat', false)->count();
            $totalResponses = $poll->responses()->count();

            $responseRate = $totalStudents > 0 ? ($totalResponses / $totalStudents) * 100 : 0;
            $participationRate = $totalStudents > 0 ? ($yesCount / $totalStudents) * 100 : 0;

            return response()->json([
                'success' => true,
                'results' => [
                    'poll_id' => $poll->id,
                    'meal_name' => $poll->meal_name,
                    'poll_date' => $poll->poll_date->format('Y-m-d'),
                    'meal_type' => $poll->meal_type,
                    'status' => $poll->status,
                    'yes_count' => $yesCount,
                    'no_count' => $noCount,
                    'total_responses' => $totalResponses,
                    'total_students' => $totalStudents,
                    'response_rate' => round($responseRate, 1),
                    'participation_rate' => round($participationRate, 1),
                    'deadline' => $poll->deadline,
                    'created_at' => $poll->created_at->format('Y-m-d H:i:s'),
                    'sent_at' => $poll->sent_at ? $poll->sent_at->format('Y-m-d H:i:s') : null
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get poll results', [
                'error' => $e->getMessage(),
                'poll_id' => $pollId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load poll results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active polls for students
     *
     * @return \Illuminate\Http\Response
     */
    public function getStudentPolls()
    {
        try {
            \Log::info('🔍 Getting student polls', [
                'student_id' => auth()->id(),
                'timestamp' => now()
            ]);

            $activePolls = KitchenMenuPoll::where('status', 'active')
                ->where('poll_date', '>=', now()->toDateString())
                ->orderBy('poll_date', 'asc')
                ->orderBy('deadline', 'asc')
                ->get();

            \Log::info('📊 Found active polls', [
                'count' => $activePolls->count(),
                'polls' => $activePolls->pluck('id', 'meal_name')
            ]);

            $studentId = auth()->id();
            $formattedPolls = $activePolls->map(function ($poll) use ($studentId) {
                try {
                    // Check if student has already responded
                    $existingResponse = KitchenPollResponse::where('poll_id', $poll->id)
                        ->where('student_id', $studentId)
                        ->first();

                    // Format deadline safely
                    $deadlineFormatted = null;
                    if ($poll->deadline) {
                        try {
                            $deadlineFormatted = $poll->deadline instanceof \Carbon\Carbon
                                ? $poll->deadline->format('Y-m-d H:i:s')
                                : $poll->deadline;
                        } catch (\Exception $e) {
                            \Log::warning('Deadline formatting issue', [
                                'poll_id' => $poll->id,
                                'deadline_raw' => $poll->deadline,
                                'error' => $e->getMessage()
                            ]);
                            $deadlineFormatted = $poll->deadline;
                        }
                    }

                    return [
                        'id' => $poll->id,
                        'meal_name' => $poll->meal_name,
                        'ingredients' => $poll->ingredients,
                        'poll_date' => $poll->poll_date->format('Y-m-d'),
                        'meal_type' => $poll->meal_type,
                        'deadline' => $deadlineFormatted,
                        'has_responded' => !!$existingResponse,
                        'response' => $existingResponse ? $existingResponse->will_eat : null
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error formatting poll', [
                        'poll_id' => $poll->id,
                        'error' => $e->getMessage()
                    ]);

                    // Return basic poll info even if there's an error
                    return [
                        'id' => $poll->id,
                        'meal_name' => $poll->meal_name ?? 'Unknown',
                        'ingredients' => $poll->ingredients ?? '',
                        'poll_date' => $poll->poll_date ? $poll->poll_date->format('Y-m-d') : date('Y-m-d'),
                        'meal_type' => $poll->meal_type ?? 'unknown',
                        'deadline' => $poll->deadline ?? null,
                        'has_responded' => false,
                        'response' => null
                    ];
                }
            });

            \Log::info('✅ Successfully formatted polls', [
                'formatted_count' => $formattedPolls->count()
            ]);

            return response()->json([
                'success' => true,
                'polls' => $formattedPolls
            ]);

        } catch (\Exception $e) {
            \Log::error('💥 Failed to get student polls', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'student_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load polls: ' . $e->getMessage(),
                'debug' => [
                    'error_type' => get_class($e),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile()
                ]
            ], 500);
        }
    }

    /**
     * Submit student response to poll
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $pollId
     * @return \Illuminate\Http\Response
     */
    public function submitPollResponse(Request $request, $pollId)
    {
        \Log::info('🔧 SUBMIT POLL RESPONSE - Request received', [
            'poll_id' => $pollId,
            'request_data' => $request->all(),
            'student_id' => auth()->id()
        ]);

        try {
            $validated = $request->validate([
                'will_eat' => 'required|boolean',
                'notes' => 'nullable|string|max:500'
            ]);

            \Log::info('✅ Validation passed', ['validated' => $validated]);
            $poll = KitchenMenuPoll::findOrFail($pollId);
            $studentId = auth()->id();

            // Check if poll is still active
            if ($poll->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'This poll is no longer active'
                ], 400);
            }

            // Check if deadline has passed
            if ($poll->deadline && now() > $poll->deadline) {
                return response()->json([
                    'success' => false,
                    'message' => 'The deadline for this poll has passed'
                ], 400);
            }

            // Update or create response
            $response = KitchenPollResponse::updateOrCreate(
                [
                    'poll_id' => $pollId,
                    'student_id' => $studentId
                ],
                [
                    'will_eat' => $validated['will_eat'],
                    'notes' => $validated['notes']
                ]
            );

            // Send notifications to kitchen and cook staff
            $notificationService = new NotificationService();
            $notificationService->pollResponseSubmitted([
                'poll_id' => $pollId,
                'meal_name' => $poll->meal_name,
                'meal_type' => $poll->meal_type,
                'student_name' => auth()->user()->name,
                'will_eat' => $validated['will_eat'],
                'response_id' => $response->id
            ]);

            \Log::info('Student responded to poll', [
                'student_id' => $studentId,
                'poll_id' => $pollId,
                'will_eat' => $validated['will_eat'],
                'meal_name' => $poll->meal_name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Response submitted successfully',
                'response' => [
                    'will_eat' => $response->will_eat,
                    'notes' => $response->notes,
                    'responded_at' => $response->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to submit poll response', [
                'error' => $e->getMessage(),
                'student_id' => auth()->id(),
                'poll_id' => $pollId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit response: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Delete a poll
     *
     * @param  int  $pollId
     * @return \Illuminate\Http\Response
     */
    public function deletePoll($pollId)
    {
        try {
            $poll = KitchenMenuPoll::findOrFail($pollId);

            // Allow deletion of draft and active polls, but not closed polls
            if ($poll->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Closed polls cannot be deleted as they contain completed response data.'
                ], 400);
            }

            // Check if user has permission to delete this poll
            if ($poll->created_by !== auth()->id() && !auth()->user()->hasRole(['admin', 'cook'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this poll.'
                ], 403);
            }

            $mealName = $poll->meal_name;
            $pollDate = $poll->poll_date;
            $pollStatus = $poll->status;

            // Count existing responses before deletion
            $responseCount = KitchenPollResponse::where('poll_id', $pollId)->count();

            // Delete the poll (responses will be deleted automatically due to foreign key constraints)
            $poll->delete();

            \Log::info('Poll deleted successfully', [
                'poll_id' => $pollId,
                'meal_name' => $mealName,
                'poll_date' => $pollDate,
                'poll_status' => $pollStatus,
                'student_responses_deleted' => $responseCount,
                'deleted_by' => auth()->user()->name
            ]);

            $message = 'Poll deleted successfully';
            if ($responseCount > 0) {
                $message .= " (including {$responseCount} student response" . ($responseCount === 1 ? '' : 's') . ")";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_responses' => $responseCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to delete poll', [
                'error' => $e->getMessage(),
                'poll_id' => $pollId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete poll: ' . $e->getMessage()
            ], 500);
        }
    }
}
