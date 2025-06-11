<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\BaseController;
use App\Models\Meal;
use App\Models\KitchenMenuPoll;
use App\Models\KitchenPollResponse;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends BaseController
{
    public function index()
    {
        return view('cook.menu');
    }

    public function getMenu($weekCycle)
    {
        return $this->safeApiResponse(function () use ($weekCycle) {
            $this->logUserAction('get_menu', ['week_cycle' => $weekCycle]);

            $meals = $this->safeTableQuery('meals', function () use ($weekCycle) {
                return Meal::forWeekCycle($weekCycle)
                    ->get()
                    ->groupBy('day_of_week')
                    ->map(function ($dayMeals) {
                        return $dayMeals->groupBy('meal_type')
                            ->map(function ($meal) {
                                $mealData = $meal->first()->toArray();
                                // Use safe status getter
                                $mealData['status'] = $this->getMealStatus($meal->first());
                                return $mealData;
                            });
                    });
            }, collect());

            // Return just the menu data - safeApiResponse will wrap it properly
            return $meals;
        }, 'Failed to load menu data');
    }

    public function getMeal($weekCycle, $day, $mealType)
    {
        $meal = Meal::forWeekCycle($weekCycle)
            ->forDay($day)
            ->forMealType($mealType)
            ->first();

        if (!$meal) {
            return response()->json([
                'success' => false,
                'message' => 'Meal not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'meal' => $meal
        ]);
    }

    public function update(Request $request)
    {
        // Add debugging
        \Log::info('Menu update request received', [
            'data' => $request->all(),
            'user' => auth()->id()
        ]);

        $validator = Validator::make($request->all(), [
            'day' => 'required|string',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner',
            'week_cycle' => 'required|integer|in:1,2',
            'name' => 'required|string|max:255',
            'ingredients' => 'required|string'
        ]);

        if ($validator->fails()) {
            \Log::warning('Menu update validation failed', [
                'errors' => $validator->errors(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Process ingredients - convert string to array if needed
        $ingredients = $request->ingredients;
        if (is_string($ingredients)) {
            // Split by comma and clean up
            $ingredients = array_map('trim', explode(',', $ingredients));
            $ingredients = array_filter($ingredients); // Remove empty values
        }

        try {
            $meal = Meal::updateOrCreate(
                [
                    'day_of_week' => strtolower($request->day),
                    'meal_type' => strtolower($request->meal_type),
                    'week_cycle' => $request->week_cycle
                ],
                [
                    'name' => $request->name,
                    'ingredients' => $ingredients,
                    'prep_time' => $request->prep_time ?? 30, // Default 30 minutes
                    'cooking_time' => $request->cooking_time ?? 30, // Default 30 minutes
                    'serving_size' => $request->serving_size ?? 50 // Default 50 servings
                ]
            );

            \Log::info('Menu updated successfully', [
                'meal_id' => $meal->id,
                'data' => $meal->toArray()
            ]);

            // Send notifications to kitchen and students about menu update
            $notificationService = new \App\Services\NotificationService();
            $notificationService->menuUpdated([
                'day' => $request->day,
                'meal_type' => $request->meal_type,
                'meal_name' => $request->name,
                'week_cycle' => $request->week_cycle
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meal updated successfully',
                'meal' => $meal
            ]);
        } catch (\Exception $e) {
            \Log::error('Menu update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update meal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created meal in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ingredients' => 'required|array',
            'prep_time' => 'required|integer|min:0',
            'cooking_time' => 'required|integer|min:0',
            'serving_size' => 'required|integer|min:1',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'day_of_week' => 'required|string',
            'week_cycle' => 'required|integer|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $meal = Meal::create([
            'name' => $request->name,
            'ingredients' => $request->ingredients,
            'prep_time' => $request->prep_time,
            'cooking_time' => $request->cooking_time,
            'serving_size' => $request->serving_size,
            'meal_type' => $request->meal_type,
            'day_of_week' => $request->day_of_week,
            'week_cycle' => $request->week_cycle,
        ]);

        // Send notifications to kitchen and students
        $notificationService = new \App\Services\NotificationService();
        $notificationService->menuCreated([
            'day' => $request->day_of_week,
            'meal_type' => $request->meal_type,
            'meal_name' => $request->name,
            'week_cycle' => $request->week_cycle
        ]);

        return response()->json([
            'success' => true,
            'meal' => $meal
        ]);
    }

    /**
     * Remove the specified meal from storage.
     */
    public function destroy($id)
    {
        $meal = Meal::find($id);
        if (!$meal) {
            return response()->json([
                'success' => false,
                'message' => 'Meal not found.'
            ], 404);
        }
        $meal->delete();
        return response()->json([
            'success' => true,
            'message' => 'Meal deleted successfully.'
        ]);
    }

    /**
     * Get kitchen status for today's meals
     */
    public function getKitchenStatus()
    {
        try {
            // UNIFIED: Use WeekCycleService for consistent calculation
            $weekInfo = \App\Services\WeekCycleService::getWeekInfo();
            $today = now()->toDateString();
            $dayOfWeek = $weekInfo['current_day'];
            $weekCycle = $weekInfo['week_cycle'];

            $todayMeals = Meal::forWeekCycle($weekCycle)
                ->forDay($dayOfWeek)
                ->get();

            $status = [];
            foreach (['breakfast', 'lunch', 'dinner'] as $mealType) {
                $meal = $todayMeals->where('meal_type', $mealType)->first();
                if ($meal) {
                    // Since meal_statuses table was removed, use a simple status based on meal existence
                    $status[$mealType] = 'Planned';
                } else {
                    $status[$mealType] = 'Not Planned';
                }
            }

            return response()->json([
                'success' => true,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get kitchen status', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load kitchen status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all meals for a specific week cycle
     */
    public function clearWeek(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'week_cycle' => 'required|integer|in:1,2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid week cycle',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deletedCount = Meal::where('week_cycle', $request->week_cycle)->delete();

            \Log::info('Week meals cleared', [
                'week_cycle' => $request->week_cycle,
                'deleted_count' => $deletedCount,
                'user' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$deletedCount} meals for Week {$request->week_cycle}",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to clear week meals', [
                'error' => $e->getMessage(),
                'week_cycle' => $request->week_cycle
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear meals: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Get cross-system integration data
     */
    public function getCrossSystemData()
    {
        try {
            // Get connected users count
            $connectedUsers = [
                'kitchen_staff' => User::where('role', 'kitchen')->count(),
                'students' => User::where('role', 'student')->count(),
                'total_users' => User::whereIn('role', ['kitchen', 'student'])->count()
            ];

            // Get kitchen status for today's meals
            $today = now()->toDateString();
            $dayOfWeek = strtolower(now()->format('l'));
            $weekOfMonth = now()->weekOfMonth;
            $weekCycle = ($weekOfMonth % 2 === 1) ? 1 : 2;

            $todayMeals = Meal::forWeekCycle($weekCycle)
                ->forDay($dayOfWeek)
                ->get();

            $kitchenStatus = [];
            foreach (['breakfast', 'lunch', 'dinner'] as $mealType) {
                $meal = $todayMeals->where('meal_type', $mealType)->first();
                if ($meal) {
                    // Since meal_statuses table was removed, use simple status
                    $kitchenStatus[$mealType] = 'Planned';
                } else {
                    $kitchenStatus[$mealType] = 'Not Planned';
                }
            }

            // Get active polls
            $activePolls = KitchenMenuPoll::where('status', 'active')
                ->orWhere('status', 'sent')
                ->get()
                ->map(function ($poll) {
                    return [
                        'id' => $poll->id,
                        'meal_name' => $poll->meal_name,
                        'poll_date' => $poll->poll_date->format('Y-m-d'),
                        'meal_type' => $poll->meal_type,
                        'status' => $poll->status,
                        'responses_count' => $poll->total_responses
                    ];
                });

            // Get poll responses summary
            $pollResponses = KitchenMenuPollResponse::whereHas('poll', function ($query) {
                $query->where('status', '!=', 'draft');
            })->get()->groupBy('poll_id');

            // Get recent menu updates
            $recentMenuUpdates = Meal::orderBy('updated_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($meal) {
                    return [
                        'name' => $meal->name,
                        'day_of_week' => $meal->day_of_week,
                        'meal_type' => $meal->meal_type,
                        'week_cycle' => $meal->week_cycle,
                        'updated_at' => $meal->updated_at->format('Y-m-d H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'connected_users' => $connectedUsers,
                    'kitchen_status' => $kitchenStatus,
                    'active_polls' => $activePolls,
                    'poll_responses' => $pollResponses,
                    'recent_menu_updates' => $recentMenuUpdates,
                    'integration_status' => [
                        'kitchen_connected' => $connectedUsers['kitchen_staff'] > 0,
                        'students_connected' => $connectedUsers['students'] > 0,
                        'polls_active' => $activePolls->count() > 0,
                        'real_time_sync' => true
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get cross-system data', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load cross-system data: ' . $e->getMessage()
            ], 500);
        }
    }
}

