<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\customer\HController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProviderController;
use App\Http\Middleware\RoleMiddleware;



Route::get('/', function () { return redirect('/login'); });

Auth::routes();


Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::get('/managers/create', [AdminController::class, 'create'])->name('managers.create');
    Route::post('/managers',[adminController::class, 'store'])->name('managers.store');
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('orders', OrderController::class);

    Route::get('/managers/show',[adminController::class, 'show'])->name('managers.show'); //DataTable route
    Route::get('/managers/data',[adminController::class, 'get_users'])->name('managers.getUsers'); //DataTable route
});

Route::prefix('customer')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('customer.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
        
});

Route::prefix('provider')->middleware(['auth', 'role:provider'])->group(function () {
    Route::get('/', [ProviderController::class, 'index'])->name('provider.dashboard');

    // Route::get('/', [HomeController::class, 'dashboard_provider'])->name('dashboard.provider');
});






