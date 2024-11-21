<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\customer\HomeController;
use App\Http\Controllers\customer\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::prefix('customer')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'dashbord'])->name('dashboard');
    
});