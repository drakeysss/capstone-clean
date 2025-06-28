<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Cook\CookDashboardController;
use App\Http\Controllers\Cook\IngredientController;
use App\Http\Controllers\Cook\InventoryController;
use App\Http\Controllers\Cook\MenuController;
use App\Http\Controllers\Cook\OrderController;

use App\Http\Controllers\Cook\PostAssessmentController;
use App\Http\Controllers\Cook\PurchaseOrderController;
use App\Http\Controllers\Cook\StudentFeedbackController as CookStudentFeedbackController;
use App\Http\Controllers\Cook\FeedbackController as CookFeedbackController;
use App\Http\Controllers\Cook\SupplierController;
use App\Http\Controllers\Kitchen\KitchenDashboardController;
use App\Http\Controllers\Kitchen\InventoryCheckController;
use App\Http\Controllers\Kitchen\PreOrderController as KitchenPreOrderController;
use App\Http\Controllers\Kitchen\PostAssessmentController as KitchenPostAssessmentController;
use App\Http\Controllers\Kitchen\FeedbackController as KitchenFeedbackController;
use App\Http\Controllers\Kitchen\AnnouncementController;
use App\Http\Controllers\Kitchen\PollController;
use App\Http\Controllers\Kitchen\MenuController as KitchenMenuController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentHistoryController;
use App\Http\Controllers\Student\MenuController as StudentMenuController;
use App\Http\Controllers\Student\PreOrderController as StudentPreOrderController;
use App\Http\Controllers\Student\FeedbackController;
use App\Http\Controllers\Kitchen\PollController as KitchenPollController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard (Today's Menu)
    Route::get('/dashboard', [StudentDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Weekly Menu
    Route::get('/menu', [StudentMenuController::class, 'index'])->name('menu');
    Route::get('/weekly-menu', [\App\Http\Controllers\Student\WeeklyMenuController::class, 'index'])->name('weekly-menu');
    
    // Pre-Order Meals
    Route::get('/pre-order', [StudentPreOrderController::class, 'index'])->name('pre-order');
    Route::post('/pre-order', [StudentPreOrderController::class, 'store'])->name('pre-order.store');
    Route::put('/pre-order/{id}', [StudentPreOrderController::class, 'update'])->name('pre-order.update');
    Route::get('/pre-order/history', [StudentPreOrderController::class, 'history'])->name('pre-order.history');

    // Kitchen Poll Integration
    Route::get('/polls/kitchen', [StudentPreOrderController::class, 'getKitchenPolls'])->name('polls.kitchen');
    Route::post('/polls/{pollId}/respond', [StudentPreOrderController::class, 'respondToKitchenPoll'])->name('polls.respond');
    
    // Meal History - Disabled
    // Route::get('/history', [StudentHistoryController::class, 'index'])->name('history');
    
    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::delete('/feedback/delete-all', [FeedbackController::class, 'destroyAll'])->name('feedback.destroyAll');
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');
    
    // Poll Responses
    Route::post('/poll-response', [StudentDashboardController::class, 'storePollResponse'])->name('poll-response.store');
    
    // Meal Submissions
    Route::post('/meals/submit', [StudentDashboardController::class, 'submitMealChoices'])->name('meals.submit');
    
    // Settings
    Route::get('/settings', [StudentDashboardController::class, 'settings'])->name('settings');
});

// Cook/Admin Routes
Route::middleware(['auth', 'role:cook'])->prefix('cook')->name('cook.')->group(function () {
    // Dashboard & Overview
    Route::get('/dashboard', [CookDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [CookDashboardController::class, 'settings'])->name('settings');


    // Menu Management
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::post('/menu/update', [MenuController::class, 'update'])->name('menu.update');
    Route::post('/menu/clear-week', [MenuController::class, 'clearWeek'])->name('menu.clear-week');
    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.delete');
    Route::get('/menu/{weekCycle}', [MenuController::class, 'getMenu'])->name('menu.get');
    Route::get('/menu/{weekCycle}/{day}/{mealType}', [MenuController::class, 'getMeal'])->name('menu.meal');
    Route::get('/menu/kitchen/status', [MenuController::class, 'getKitchenStatus'])->name('menu.kitchen-status');

    
    // Weekly Menu Management
    Route::get('/weekly-menu', [\App\Http\Controllers\Cook\WeeklyMenuController::class, 'index'])->name('weekly-menu');
    Route::put('/weekly-menu/update', [\App\Http\Controllers\Cook\WeeklyMenuController::class, 'update'])->name('weekly-menu.update');
    Route::put('/weekly-menu/update-day', [\App\Http\Controllers\Cook\WeeklyMenuController::class, 'updateDay'])->name('weekly-menu.update-day');
    Route::post('/menu/update-weekly', [\App\Http\Controllers\Cook\WeeklyMenuController::class, 'updateWeekly'])->name('menu.update-weekly');
    
    // Ingredients Management
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::put('/ingredients/{id}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/ingredients/{id}', [IngredientController::class, 'destroy'])->name('ingredients.delete');

    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{item}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{item}', [InventoryController::class, 'destroy'])->name('inventory.delete');
    Route::post('/inventory/notify-delivery', [InventoryController::class, 'notifyDelivery'])->name('inventory.notify-delivery');

    // Supplier Management
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    

    
    // Post Assessment
    Route::get('/post-assessment', [PostAssessmentController::class, 'index'])->name('post-assessment');
    Route::post('/post-assessment', [PostAssessmentController::class, 'store'])->name('post-assessment.store');
    Route::delete('/post-assessment/bulk-delete', [PostAssessmentController::class, 'deleteAll'])->name('post-assessment.bulk-delete');
    Route::delete('/post-assessment/{id}', [PostAssessmentController::class, 'destroy'])->name('post-assessment.destroy');
    
    // Student Feedback & Communication
    Route::get('/student-feedback', [CookStudentFeedbackController::class, 'index'])->name('student-feedback');
    Route::get('/feedback', [CookFeedbackController::class, 'index'])->name('feedback');
    Route::get('/feedback/{id}', [CookFeedbackController::class, 'show'])->name('feedback.show');
    Route::delete('/feedback/{id}', [CookFeedbackController::class, 'destroy'])->name('feedback.destroy');
    Route::delete('/feedback', [CookFeedbackController::class, 'destroyAll'])->name('feedback.destroy-all');
    
    // Access to Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');

    // Stock Management (Cook reviews kitchen reports and approves restocking)
    Route::get('/stock-management', [InventoryController::class, 'index'])->name('stock-management');
    Route::get('/stock-management/reports', [InventoryController::class, 'reports'])->name('inventory.reports');
    Route::delete('/stock-management/clear-all', [InventoryController::class, 'clearAllReports'])->name('inventory.clear-all-reports');
    Route::get('/stock-management/reports/{id}', [InventoryController::class, 'showReport'])->name('inventory.show-report');
    Route::post('/stock-management/reports/{id}/approve', [InventoryController::class, 'approveReport'])->name('inventory.approve-report');
    Route::delete('/stock-management/reports/{id}', [InventoryController::class, 'deleteReport'])->name('inventory.delete-report');
    Route::post('/stock-management/restock', [InventoryController::class, 'recordRestock'])->name('inventory.record-restock');
    Route::get('/stock-management/alerts', [InventoryController::class, 'alerts'])->name('inventory.alerts');
    Route::post('/menu/inventory-requirements', [MenuController::class, 'calculateInventoryRequirements'])->name('menu.inventory-requirements');



    // Cross-System Integration
    Route::get('/cross-system-data', [MenuController::class, 'getCrossSystemData'])->name('cross-system-data');
    Route::get('/system-integration', [CookDashboardController::class, 'systemIntegration'])->name('system-integration');


});

// Kitchen Team Routes
Route::middleware(['auth', 'role:kitchen'])->prefix('kitchen')->name('kitchen.')->group(function () {
    // Dashboard & Overview
    Route::get('/dashboard', [KitchenDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [KitchenDashboardController::class, 'settings'])->name('settings');

    // Daily Menu
    Route::get('/daily-menu', [KitchenMenuController::class, 'index'])->name('daily-menu');
    Route::get('/menu/{weekCycle}', [KitchenMenuController::class, 'getMenu'])->name('menu.get');

    // Pre-Orders
    Route::get('/pre-orders', [App\Http\Controllers\Kitchen\PreOrderController::class, 'index'])->name('pre-orders');
    Route::post('/pre-orders/mark-prepared', [App\Http\Controllers\Kitchen\PreOrderController::class, 'markMenuItemsPrepared'])->name('pre-orders.mark-prepared');
    Route::post('/pre-orders/mark-preorder-status', [App\Http\Controllers\Kitchen\PreOrderController::class, 'markPreOrderStatus'])->name('pre-orders.mark-preorder-status');
    
    // Post Assessment (Leftovers)
    Route::get('/post-assessment', [KitchenPostAssessmentController::class, 'index'])->name('post-assessment');
    Route::post('/post-assessment', [KitchenPostAssessmentController::class, 'store'])->name('post-assessment.store');



    // Debug route for post-assessment
    Route::get('/debug-post-assessment', function() {
        return response()->json([
            'success' => true,
            'message' => 'Post-assessment debug endpoint working',
            'user' => auth()->user(),
            'csrf_token' => csrf_token(),
            'route_exists' => route('kitchen.post-assessment.store'),
            'timestamp' => now()
        ]);
    });
    
    // Inventory Check
    Route::get('/inventory', [InventoryCheckController::class, 'index'])->name('inventory');
    Route::post('/inventory/check', [InventoryCheckController::class, 'store'])->name('inventory.check');
    Route::get('/inventory/history', [InventoryCheckController::class, 'history'])->name('inventory.history');
    Route::get('/inventory/{id}', [InventoryCheckController::class, 'show'])->name('inventory.show');
    Route::delete('/inventory/{id}', [InventoryCheckController::class, 'destroy'])->name('inventory.delete');
    Route::delete('/inventory/delete-all/reports', [InventoryCheckController::class, 'destroyAll'])->name('inventory.delete-all');
    
    // Recipe & Meal Execution
    Route::get('/recipes', [KitchenDashboardController::class, 'recipes'])->name('recipes');
    Route::get('/meal-planning', [KitchenDashboardController::class, 'mealPlanning'])->name('meal-planning');
    Route::get('/preparation', [KitchenDashboardController::class, 'preparation'])->name('preparation');
    Route::get('/orders', [KitchenDashboardController::class, 'orders'])->name('orders');
    

    
    // Meal Polling System (using Kitchen PollController)
    Route::get('/polls', [App\Http\Controllers\Kitchen\PollController::class, 'index'])->name('polls.index');
    Route::post('/polls', [App\Http\Controllers\Kitchen\PollController::class, 'store'])->name('polls.store');
    Route::get('/polls/{poll}', [App\Http\Controllers\Kitchen\PollController::class, 'show'])->name('polls.show');
    Route::delete('/polls/{poll}', [App\Http\Controllers\Kitchen\PollController::class, 'destroy'])->name('polls.destroy');
    
    // Daily Menu
    Route::get('/daily-menu', [KitchenDashboardController::class, 'dailyMenu'])->name('daily-menu');
    
    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.delete');

    // Menu Poll Management
    Route::get('/pre-orders/polls', [App\Http\Controllers\Kitchen\PreOrderController::class, 'getPolls'])->name('pre-orders.polls');
    Route::post('/pre-orders/create-poll', [App\Http\Controllers\Kitchen\PreOrderController::class, 'createPoll'])->name('pre-orders.create-poll');
    Route::post('/pre-orders/send-poll', [App\Http\Controllers\Kitchen\PreOrderController::class, 'sendPoll'])->name('pre-orders.send-poll');
    Route::post('/pre-orders/send-all-polls', [App\Http\Controllers\Kitchen\PreOrderController::class, 'sendAllPolls'])->name('pre-orders.send-all-polls');
    Route::post('/pre-orders/update-poll-deadline', [App\Http\Controllers\Kitchen\PreOrderController::class, 'updatePollDeadline'])->name('pre-orders.update-poll-deadline');
    Route::delete('/pre-orders/delete-poll/{pollId}', [App\Http\Controllers\Kitchen\PreOrderController::class, 'deletePoll'])->name('pre-orders.delete-poll');
    Route::get('/pre-orders/poll-results/{pollId}', [App\Http\Controllers\Kitchen\PreOrderController::class, 'getPollResults'])->name('pre-orders.poll-results');
    Route::post('/pre-orders/finish-poll', [App\Http\Controllers\Kitchen\PreOrderController::class, 'finishPoll'])->name('pre-orders.finish-poll');
    Route::post('/pre-orders/check-expired-polls', [App\Http\Controllers\Kitchen\PreOrderController::class, 'checkExpiredPolls'])->name('pre-orders.check-expired-polls');

    // Test route for debugging
    Route::get('/pre-orders/test', function() {
        return response()->json([
            'success' => true,
            'message' => 'Test route working',
            'timestamp' => now(),
            'user' => auth()->user() ? auth()->user()->name : 'Not authenticated',
            'role' => auth()->user() ? auth()->user()->role : 'No role'
        ]);
    })->name('pre-orders.test');

    // Debug route to test all endpoints
    Route::get('/pre-orders/debug-endpoints', function() {
        $endpoints = [
            'polls' => '/kitchen/pre-orders/polls',
            'available-meals' => '/kitchen/pre-orders/available-meals?meal_type=breakfast',
            'debug-meals' => '/kitchen/pre-orders/debug-meals',
            'daily-menu-updates' => '/kitchen/daily-menu/updates',
        ];

        return response()->json([
            'success' => true,
            'message' => 'Debug endpoints list',
            'endpoints' => $endpoints,
            'current_user' => auth()->user(),
            'timestamp' => now()
        ]);
    });

    // Daily Menu Real-time Updates
    Route::get('/daily-menu/updates', [App\Http\Controllers\Kitchen\PreOrderController::class, 'getDailyMenuUpdates'])->name('daily-menu.updates');
    Route::post('/daily-menu/update-status', [App\Http\Controllers\Kitchen\PreOrderController::class, 'updateDailyMenuStatus'])->name('daily-menu.update-status');
    Route::post('/daily-menu/update-portions', [App\Http\Controllers\Kitchen\PreOrderController::class, 'updateDailyMenuPortions'])->name('daily-menu.update-portions');

    // Legacy routes (keeping for compatibility)
    Route::get('/pre-orders/{weekCycle}', [App\Http\Controllers\Kitchen\PreOrderController::class, 'getPreOrders'])->name('pre-orders.get');
    Route::get('/pre-orders/check-menu', [App\Http\Controllers\Kitchen\PreOrderController::class, 'checkMenu'])->name('pre-orders.check-menu');
    Route::get('/pre-orders/available-meals', [App\Http\Controllers\Kitchen\PreOrderController::class, 'getAvailableMeals'])->name('pre-orders.available-meals');
    Route::get('/pre-orders/debug-meals', [App\Http\Controllers\Kitchen\PreOrderController::class, 'debugMeals'])->name('pre-orders.debug-meals');
    Route::post('/pre-orders/notify-deadline', [App\Http\Controllers\Kitchen\PreOrderController::class, 'notifyDeadline'])->name('pre-orders.notify-deadline');
    Route::post('/pre-orders/update-deadline', [App\Http\Controllers\Kitchen\PreOrderController::class, 'updateDeadline'])->name('pre-orders.update-deadline');
    Route::post('/pre-orders/mark-prepared', [App\Http\Controllers\Kitchen\PreOrderController::class, 'markPrepared'])->name('pre-orders.mark-prepared');

    // Feedback
    Route::get('/feedback', [KitchenFeedbackController::class, 'index'])->name('feedback');
    Route::get('/feedback/{id}', [KitchenFeedbackController::class, 'show'])->name('feedback.show');
    Route::delete('/feedback/delete-all', [App\Http\Controllers\Kitchen\FeedbackController::class, 'destroyAll'])->name('kitchen.feedback.deleteAll');
    Route::delete('/feedback/{id}', [App\Http\Controllers\Kitchen\FeedbackController::class, 'destroy'])->name('kitchen.feedback.destroy');
});

// Additional Kitchen Routes (for menu management)
Route::middleware(['auth', 'role:kitchen'])->prefix('kitchen')->group(function () {
    Route::get('/menu', [App\Http\Controllers\Kitchen\MenuController::class, 'index'])->name('kitchen.menu');
    Route::get('/menu/{weekCycle}', [App\Http\Controllers\Kitchen\MenuController::class, 'getMenu']);
    Route::post('/menu/update-status', [App\Http\Controllers\Kitchen\MenuController::class, 'updateStatus']);



    // Debug route for testing API
    Route::get('/debug/menu/{weekCycle}', function($weekCycle) {
        return response()->json([
            'success' => true,
            'message' => 'API is working',
            'weekCycle' => $weekCycle,
            'user' => auth()->user()->name ?? 'Unknown',
            'role' => auth()->user()->user_role ?? 'Unknown',
            'timestamp' => now()->toDateTimeString()
        ]);
    });
});

// Additional Student Routes (for menu viewing)
Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/menu', [App\Http\Controllers\Student\MenuController::class, 'index'])->name('student.menu');
    Route::get('/menu/{weekCycle}', [App\Http\Controllers\Student\MenuController::class, 'getMenu']);
});

// Notification Routes (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
    Route::get('/notifications/feature-status', [App\Http\Controllers\NotificationController::class, 'getFeatureStatus'])->name('notifications.feature-status');
    Route::post('/notifications/mark-feature-read', [App\Http\Controllers\NotificationController::class, 'markFeatureAsRead'])->name('notifications.mark-feature-read');
    Route::get('/notifications/stats', [App\Http\Controllers\NotificationController::class, 'getStats'])->name('notifications.stats');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/test', [App\Http\Controllers\NotificationController::class, 'test'])->name('notifications.test');

    // Debug route (only in local environment)
    if (app()->environment('local')) {
        Route::get('/debug/notifications', function() {
            return view('debug.notification-test');
        })->name('debug.notifications');

        // Debug route for week cycle calculation
        Route::get('/debug/week-cycle', function() {
            $weekInfo = \App\Services\WeekCycleService::getWeekInfo();
            $debug = \App\Services\WeekCycleService::debug();

            return response()->json([
                'success' => true,
                'current_week_info' => $weekInfo,
                'debug_info' => $debug,
                'comparison' => [
                    'laravel_week_of_month' => now()->weekOfMonth,
                    'helper_week_of_month' => $weekInfo['week_of_month'],
                    'helper_week_cycle' => $weekInfo['week_cycle'],
                    'cycle_description' => $weekInfo['cycle_description']
                ],
                'message' => 'Week cycle calculation is now consistent across all components!'
            ]);
        })->name('debug.week-cycle');
    }
});

// Error logging endpoint for JavaScript errors (available to all authenticated users)
Route::middleware(['auth'])->post('/api/log-error', function (Request $request) {
    try {
        \Log::error('Frontend Error', [
            'type' => $request->input('type', 'unknown'),
            'message' => $request->input('message'),
            'filename' => $request->input('filename'),
            'line' => $request->input('line'),
            'column' => $request->input('column'),
            'stack' => $request->input('stack'),
            'url' => $request->input('url'),
            'user_agent' => $request->input('user_agent'),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'unknown',
            'timestamp' => now()
        ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // Silently fail to avoid infinite error loops
        return response()->json(['success' => false], 500);
    }
});

// System health check endpoint
Route::middleware(['auth'])->get('/api/system-health', function () {
    try {
        $health = \App\Services\ErrorPreventionService::systemHealthCheck();
        return response()->json([
            'success' => true,
            'health' => $health,
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        \Log::error('System health check failed', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Health check failed',
            'timestamp' => now()
        ], 500);
    }
});
