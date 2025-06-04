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
use App\Http\Controllers\Cook\PreOrderController as CookPreOrderController;
use App\Http\Controllers\Cook\PostAssessmentController;
use App\Http\Controllers\Cook\PurchaseOrderController;
use App\Http\Controllers\Cook\StudentFeedbackController as CookStudentFeedbackController;
use App\Http\Controllers\Cook\SupplierController;
use App\Http\Controllers\Kitchen\KitchenDashboardController;
use App\Http\Controllers\Kitchen\InventoryCheckController;
use App\Http\Controllers\Kitchen\PreOrderController as KitchenPreOrderController;
use App\Http\Controllers\Kitchen\PostAssessmentController as KitchenPostAssessmentController;
use App\Http\Controllers\Kitchen\StudentFeedbackController;
use App\Http\Controllers\Kitchen\AnnouncementController;
use App\Http\Controllers\Kitchen\PollController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentHistoryController;
use App\Http\Controllers\Student\StudentMenuController;
use App\Http\Controllers\Student\PreOrderController as StudentPreOrderController;
use App\Http\Controllers\Student\FeedbackController;

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
    
    // Meal History - Disabled
    // Route::get('/history', [StudentHistoryController::class, 'index'])->name('history');
    
    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    
    // Poll Responses
    Route::post('/poll-response', [StudentDashboardController::class, 'storePollResponse'])->name('poll-response.store');
    
    // Meal Submissions
    Route::post('/meals/submit', [StudentDashboardController::class, 'submitMealChoices'])->name('meals.submit');
    
    // Settings
    Route::get('/settings', [StudentDashboardController::class, 'settings'])->name('settings');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/cook/menu/update', [\App\Http\Controllers\Cook\MenuController::class, 'update'])->name('cook.menu.update');
});

// Cook/Admin Routes
Route::middleware(['auth', 'role:cook'])->prefix('cook')->name('cook.')->group(function () {
    // Dashboard & Overview
    Route::get('/dashboard', [CookDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [CookDashboardController::class, 'settings'])->name('settings');
    Route::get('/meal-attendance', [CookDashboardController::class, 'mealAttendance'])->name('meal-attendance');

    // Menu Management
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.delete');
    
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
    
    // Menu Management
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::post('/api/menu/update', [MenuController::class, 'update'])->name('menu.update');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    Route::post('/menu/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menu.toggle');

    // Purchase Orders
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::put('/purchase-orders/{id}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
    Route::delete('/purchase-orders/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.delete');
    
    // Pre-Orders Management
    Route::get('/pre-orders', [CookPreOrderController::class, 'index'])->name('pre-orders');
    Route::get('/pre-orders/export', [CookPreOrderController::class, 'export'])->name('pre-orders.export');
    
    // Post Assessment
    Route::get('/post-assessment', [PostAssessmentController::class, 'index'])->name('post-assessment');
    Route::post('/post-assessment', [PostAssessmentController::class, 'store'])->name('post-assessment.store');
    Route::put('/post-assessment/{id}', [PostAssessmentController::class, 'update'])->name('post-assessment.update');
    
    // Student Feedback & Communication
    Route::get('/student-feedback', [CookStudentFeedbackController::class, 'index'])->name('student-feedback');
    
    // Access to Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
});

// Kitchen Team Routes
Route::middleware(['auth', 'role:kitchen'])->prefix('kitchen')->name('kitchen.')->group(function () {
    // Dashboard & Overview
    Route::get('/dashboard', [KitchenDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [KitchenDashboardController::class, 'settings'])->name('settings');

    // Pre-Orders
    Route::get('/pre-orders', [KitchenPreOrderController::class, 'index'])->name('pre-orders');
    Route::post('/pre-orders/mark-prepared', [KitchenPreOrderController::class, 'markPrepared'])->name('pre-orders.mark-prepared');
    
    // Post Assessment (Leftovers)
    Route::get('/post-assessment', [KitchenPostAssessmentController::class, 'index'])->name('post-assessment');
    Route::post('/post-assessment', [KitchenPostAssessmentController::class, 'store'])->name('post-assessment.store');
    
    // Inventory Check
    Route::get('/inventory', [InventoryCheckController::class, 'index'])->name('inventory');
    Route::post('/inventory/check', [InventoryCheckController::class, 'submitCheck'])->name('inventory.check');
    
    // Recipe & Meal Execution
    Route::get('/recipes', [KitchenDashboardController::class, 'recipes'])->name('recipes');
    Route::get('/meal-planning', [KitchenDashboardController::class, 'mealPlanning'])->name('meal-planning');
    Route::get('/preparation', [KitchenDashboardController::class, 'preparation'])->name('preparation');
    Route::get('/orders', [KitchenDashboardController::class, 'orders'])->name('orders');
    
    // Student Feedback
    Route::get('/student-feedback', [StudentFeedbackController::class, 'index'])->name('student-feedback');
    
    // Meal Polling System
    Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
    Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
    Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');
    Route::delete('/polls/{poll}', [PollController::class, 'destroy'])->name('polls.destroy');
    
    // Daily Menu
    Route::get('/daily-menu', [KitchenDashboardController::class, 'dailyMenu'])->name('daily-menu');
    
    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.delete');
});









