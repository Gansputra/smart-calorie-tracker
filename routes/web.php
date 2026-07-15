<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FoodController as AdminFoodController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\FoodScannerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeightLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('welcome');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| User Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user|admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Food Scanner (AI)
    Route::get('/scanner', [FoodScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/predict', [FoodScannerController::class, 'predict'])->name('scanner.predict');
    Route::post('/scanner/save', [FoodScannerController::class, 'save'])->name('scanner.save');

    // Food Log (CRUD)
    Route::get('/food-log', [FoodLogController::class, 'index'])->name('food-log.index');
    Route::get('/food-log/create', [FoodLogController::class, 'create'])->name('food-log.create');
    Route::post('/food-log', [FoodLogController::class, 'store'])->name('food-log.store');
    Route::get('/food-log/{foodLog}/edit', [FoodLogController::class, 'edit'])->name('food-log.edit');
    Route::put('/food-log/{foodLog}', [FoodLogController::class, 'update'])->name('food-log.update');
    Route::delete('/food-log/{foodLog}', [FoodLogController::class, 'destroy'])->name('food-log.destroy');
    Route::get('/food-log/food-data', [FoodLogController::class, 'getFoodData'])->name('food-log.food-data');

    // Weight Log (Fat Loss Tracker)
    Route::get('/weight-log', [WeightLogController::class, 'index'])->name('weight-log.index');
    Route::post('/weight-log', [WeightLogController::class, 'store'])->name('weight-log.store');
    Route::put('/weight-log/{weightLog}', [WeightLogController::class, 'update'])->name('weight-log.update');
    Route::delete('/weight-log/{weightLog}', [WeightLogController::class, 'destroy'])->name('weight-log.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Food CRUD
    Route::resource('foods', AdminFoodController::class)->except(['show']);

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
