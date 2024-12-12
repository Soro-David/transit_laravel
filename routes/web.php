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
use App\Http\Controllers\CustomerColisController;


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
        Route::get('/get-colis-hold',[ColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/devis-hold',[ColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/list-contenaire',[ColisController::class, 'liste_contenaire'])->name('liste.contenaire');

        Route::post('/store', [ColisController::class,'store'])->name('store'); 
        Route::get('/{coli}', [ColisController::class,'show'])->name('show'); 
        Route::get('/{coli}/edit', [ColisController::class,'edit'])->name('edit'); 
        Route::put('/{coli}', [ColisController::class,'update'])->name('update'); 
        Route::delete('/{coli}', [ColisController::class,'destroy'])->name('destroy');
        Route::post('/store-expediteur', [ColisController::class,'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [ColisController::class,'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [ColisController::class, 'search'])->name('search.expediteurs');

        Route::get('/create/step1', [ColisController::class, 'createStep1'])->name('create.step1');
        Route::post('/store/step1', [ColisController::class, 'storeStep1'])->name('store.step1');
        Route::get('/create/step2', [ColisController::class, 'createStep2'])->name('create.step2');
        Route::post('/store/step2', [ColisController::class, 'storeStep2'])->name('store.step2');
        Route::get('/create/step3', [ColisController::class, 'createStep3'])->name('create.step3');
        Route::post('/store/step3', [ColisController::class, 'storeStep3'])->name('store.step3');
        Route::get('/create/payement', [ColisController::class, 'stepPayement'])->name('create.payement');
        Route::post('/store/payement', [ColisController::class, 'storePayement'])->name('store.payement');
        Route::get('/create/qrcode', [ColisController::class, 'qrcode'])->name('create.qrcode');
        Route::get('/complete', [ColisController::class, 'complete'])->name('complete');


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

    Route::prefix('customer_colis')->name('customer_colis.')->group(function(){
        Route::get('/', [CustomerColisController::class,'index'])->name('index'); 
        Route::get('/create', [CustomerColisController::class,'create'])->name('create'); 
        Route::get('/on-hold', [CustomerColisController::class,'hold'])->name('hold'); 
        Route::get('/history', [CustomerColisController::class,'history'])->name('history');
        Route::get('/suivi', [CustomerColisController::class,'suivi'])->name('suivi');
        Route::get('/facture', [CustomerColisController::class,'facture'])->name('facture');
        Route::get('/get-colis',[CustomerColisController::class, 'get_colis'])->name('get.colis');

        // Route::get('/agence/data',[AgenceController::class, 'get_agence'])->name('getAgence');

        // Route::post('/store', [AgenceController::class,'store'])->name('store'); 
        Route::get('/create/step1', [CustomerColisController::class, 'createStep1'])->name('create.step1');
        Route::post('/store/step1', [CustomerColisController::class, 'storeStep1'])->name('store.step1');
        Route::get('/create/step2', [CustomerColisController::class, 'createStep2'])->name('create.step2');
        Route::post('/store/step2', [CustomerColisController::class, 'storeStep2'])->name('store.step2');
        Route::get('/create/step3', [CustomerColisController::class, 'createStep3'])->name('create.step3');
        Route::post('/store/step3', [CustomerColisController::class, 'storeStep3'])->name('store.step3');

        Route::get('/create/step4', [CustomerColisController::class, 'createStep4'])->name('create.step4');
        Route::post('/store/step4', [CustomerColisController::class, 'storeStep4'])->name('store.step4');

        Route::get('/create/payement', [CustomerColisController::class, 'stepPayement'])->name('create.payement');
        Route::post('/store/payement', [CustomerColisController::class, 'storePayement'])->name('store.payement');
        Route::get('/create/qrcode', [CustomerColisController::class, 'qrcode'])->name('create.qrcode');
        Route::get('/complete', [CustomerColisController::class, 'complete'])->name('complete');

    });
        
});

Route::prefix('provider')->middleware(['auth', 'role:provider'])->group(function () {
    Route::get('/', [ProviderController::class, 'index'])->name('provider.dashboard');

    // Route::get('/', [HomeController::class, 'dashboard_provider'])->name('dashboard.provider');
});
