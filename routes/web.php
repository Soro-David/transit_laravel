<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\customer\HController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ColisController;
use App\Http\Controllers\AgenceController;

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
    Route::get('/managers/agence', [AdminController::class, 'gestion_agence'])->name('managers.agence');
    Route::post('/managers',[adminController::class, 'store'])->name('managers.store');
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('orders', OrderController::class);

    Route::get('/managers/agent',[adminController::class, 'add_agent'])->name('managers.agent'); //DataTable route
    Route::get('/managers/data',[adminController::class, 'get_users'])->name('managers.getUsers'); //DataTable route


    Route::prefix('colis')->name('colis.')->group(function(){
        Route::get('/', [ColisController::class,'index'])->name('index'); 
        Route::get('/on-hold', [ColisController::class,'hold'])->name('hold'); 
        Route::get('/history', [ColisController::class,'history'])->name('history'); 
        Route::get('/create', [ColisController::class,'create'])->name('create'); 
        Route::get('/get-colis',[ColisController::class, 'get_colis'])->name('getColis');
        Route::get('/devis-hold',[ColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/list-contenaire',[ColisController::class, 'liste_contenaire'])->name('liste.contenaire');

        Route::post('/store', [ColisController::class,'store'])->name('store'); 
        Route::get('/{coli}', [ColisController::class,'show'])->name('show'); 
        Route::get('/{coli}/edit', [ColisController::class,'edit'])->name('edit'); 
        Route::put('/{coli}', [ColisController::class,'update'])->name('update'); 
        Route::delete('/{coli}', [ColisController::class,'destroy'])->name('destroy');
        Route::post('/store-expediteur', [ColisController::class,'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [ColisController::class,'store_destinataire'])->name('store.destinataire'); 


    });
        // agence Route 
    Route::prefix('agence')->name('agence.')->group(function(){
        Route::get('/', [AgenceController::class,'index'])->name('index'); 
        Route::get('/create', [AgenceController::class,'create'])->name('create'); 
        Route::get('/agence/data',[AgenceController::class, 'get_agence'])->name('getAgence');

        Route::post('/store', [AgenceController::class,'store'])->name('store'); 

    });
     
    Route::put('/profile/photo', [UserController::class, 'updateProfilePhoto'])->name('profile.photo.update');

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
