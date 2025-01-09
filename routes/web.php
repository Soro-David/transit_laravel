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
use App\Http\Controllers\AgentColisController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\AgentTransportController;
use App\Http\Controllers\GestionAgentController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\AgentScanController;
use App\Http\Controllers\QrcodeController;
use App\Http\Controllers\ProgrammeController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentController;
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
    Route::get('/qrcode/data',[QrcodeController::class, 'generate'])->name('qrcode.generate'); //DataTable route


    Route::prefix('colis')->name('colis.')->group(function(){
        Route::get('/', [ColisController::class,'index'])->name('index'); 
        Route::get('/on-hold', [ColisController::class,'hold'])->name('hold'); 
        Route::get('/on-dump', [ColisController::class,'dump'])->name('dump'); 
        Route::get('/history', [ColisController::class,'history'])->name('history'); 
        Route::get('/create', [ColisController::class,'create'])->name('create'); 
        Route::get('/get-colis',[ColisController::class, 'get_colis'])->name('getColis');
        Route::get('/get-colis-dump',[ColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-devis-colis',[ColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/get-contenaire-colis',[ColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/get-colis-hold',[ColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/devis-hold',[ColisController::class, 'devis_hold'])->name('devis.hold');

        // route edit
        Route::get('/on-hold/{id}/edit', [ColisController::class, 'edit_hold'])->name('hold.edit');
        Route::put('/on-hold/{id}', [ColisController::class, 'update_hold'])->name('hold.update');


        // route contenaire fermer
        Route::post('/contenaire-fermer',[ColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');

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
        Route::get('/create/step4', [ColisController::class, 'createStep4'])->name('create.step4');
        Route::post('/store/step4', [ColisController::class, 'storeStep4'])->name('store.step4');
        Route::get('/create/payement', [ColisController::class, 'stepPayement'])->name('create.payement');
        Route::post('/store/payement', [ColisController::class, 'storePayement'])->name('store.payement');
        Route::get('/create/qrcode', [ColisController::class, 'qrcode'])->name('create.qrcode');
        Route::get('/create/complete', [ColisController::class, 'complete'])->name('complete');

    });
        // agence Route 
    Route::prefix('agence')->name('agence.')->group(function(){
        Route::get('/', [AgenceController::class,'index'])->name('index'); 
        Route::get('/create', [AgenceController::class,'create'])->name('create'); 
        Route::get('/agence/data',[AgenceController::class, 'get_agence'])->name('getAgence');

        Route::delete('/agences/{id}', [AgenceController::class, 'destroy'])->name('agence.destroy');

        Route::post('/store', [AgenceController::class,'store'])->name('store'); 
        // Routes pour les agences
        Route::get('/agence/{id}/edit', [AgenceController::class, 'edit'])->name('agence.edit');
        Route::get('/agence/{id}/show', [AgenceController::class, 'show'])->name('agence.show');
        Route::put('/agence/{id}', [AgenceController::class, 'update'])->name('agence.update');

        // Route pour les agents
        Route::get('/agent/data',[GestionAgentController::class, 'get_users'])->name('get.agent');
        Route::get('/agent/{id}/edit', [GestionAgentController::class, 'edit'])->name('agent.edit');
        Route::get('/agent/{id}/show', [GestionAgentController::class, 'show'])->name('agent.show');
        Route::put('/agent/{id}', [GestionAgentController::class, 'update'])->name('agent.update');
        Route::delete('/agent/{id}', [GestionAgentController::class, 'destroy'])->name('agent.destroy');

    });
// transport
    Route::prefix('transport')->name('transport.')->group(function(){
        Route::get('/', [TransportController::class,'index'])->name('index'); 
        Route::get('/create', [TransportController::class,'create'])->name('create');
        Route::get('/show-chauffeur', [TransportController::class,'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur', [TransportController::class,'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/reference.auto/{query}', [TransportController::class, 'reference_auto'])->name('reference.auto');

        Route::get('/chauffeur/data',[TransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');

        Route::get('/store',[TransportController::class, 'store'])->name('store');
        Route::post('/store', [TransportController::class,'store'])->name('store'); 

    });

    // Scan
    Route::prefix('scan')->name('scan.')->group(function(){
        Route::get('/en-entrepot', [ScanController::class,'entrepot'])->name('entrepot'); 
        Route::get('/en-chargement', [ScanController::class,'chargement'])->name('chargement'); 
        Route::get('/en-dechargement', [ScanController::class,'dechargement'])->name('dechargement'); 
        Route::get('/get-colis-entrepot',[ScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement',[ScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-chargement',[ScanController::class, 'get_colis_charge'])->name('get.colis.charge');

        Route::get('/chauffeur/data',[TransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');

        Route::get('/store',[TransportController::class, 'store'])->name('store');
        Route::post('/store', [TransportController::class,'store'])->name('store'); 

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
        Route::get('/get-colis-suivi',[CustomerColisController::class, 'get_colis_suivi'])->name('get.colis.suivi');
        Route::get('/get-colis-valide',[CustomerColisController::class, 'get_colis_valide'])->name('get.colis.valide');
        Route::get('/get-invoice',[CustomerColisController::class, 'get_facture'])->name('get.facture');
        Route::get('/facture/pdf', [CustomerColisController::class, 'telechargerPdf'])->name('facture.pdf');



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

Route::prefix('agent')->middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/', [AgentController::class, 'index'])->name('agent.dashboard');

    Route::prefix('agent_colis')->name('agent_colis.')->group(function(){
        Route::get('/', [AgentColisController::class,'index'])->name('index'); 
        Route::get('/on-hold', [AgentColisController::class,'hold'])->name('hold'); 
        Route::get('/history', [AgentColisController::class,'history'])->name('history'); 
        Route::get('/on-dump', [AgentColisController::class,'dump'])->name('dump'); 
        Route::get('/create', [AgentColisController::class,'create'])->name('create'); 
        Route::get('/get-colis',[AgentColisController::class, 'get_colis'])->name('getColis');
        Route::get('/get-colis-hold',[AgentColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/get-colis-dump',[AgentColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-devis-colis',[ColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/get-contenaire-colis',[AgentColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/get-colis-hold',[AgentColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/devis-hold',[AgentColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-devis-colis',[AgentColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/get-contenaire-colis',[AgentColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/get-colis-hold',[AgentColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/devis-hold',[AgentColisController::class, 'devis_hold'])->name('devis.hold');
        // route contenaire fermer
        Route::post('/contenaire-fermer',[AgentColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');

        Route::get('/list-contenaire',[AgentColisController::class, 'liste_contenaire'])->name('liste.contenaire');

        Route::post('/store', [AgentAgentColisController::class,'store'])->name('store'); 
        Route::get('/{coli}', [AgentColisController::class,'show'])->name('show'); 
        Route::get('/{coli}/edit', [AgentColisController::class,'edit'])->name('edit'); 
        Route::put('/{coli}', [AgentColisController::class,'update'])->name('update'); 
        Route::delete('/{coli}', [AgentColisController::class,'destroy'])->name('destroy');
        Route::post('/store-expediteur', [AgentColisController::class,'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [AgentColisController::class,'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [AgentColisController::class, 'search'])->name('search.expediteurs');

        Route::get('/create/step1', [AgentColisController::class, 'createStep1'])->name('create.step1');
        Route::post('/store/step1', [AgentColisController::class, 'storeStep1'])->name('store.step1');
        Route::get('/create/step2', [AgentColisController::class, 'createStep2'])->name('create.step2');
        Route::post('/store/step2', [AgentColisController::class, 'storeStep2'])->name('store.step2');
        Route::get('/create/step3', [AgentColisController::class, 'createStep3'])->name('create.step3');
        Route::post('/store/step3', [AgentColisController::class, 'storeStep3'])->name('store.step3');
        Route::get('/create/step4', [AgentColisController::class, 'createStep4'])->name('create.step4');
        Route::post('/store/step4', [AgentColisController::class, 'storeStep4'])->name('store.step4');
        Route::get('/create/payement', [AgentColisController::class, 'stepPayement'])->name('create.payement');
        Route::post('/store/payement', [AgentColisController::class, 'storePayement'])->name('store.payement');
        Route::get('/create/qrcode', [AgentColisController::class, 'qrcode'])->name('create.qrcode');
        Route::get('/create/complete', [AgentColisController::class, 'complete'])->name('complete');

    });
    Route::prefix('agent_scan')->name('agent_scan.')->group(function(){
        Route::get('/en-entrepot', [AgentScanController::class,'entrepot'])->name('entrepot'); 
        Route::get('/en-chargement', [AgentScanController::class,'chargement'])->name('chargement'); 
        Route::get('/en-dechargement', [AgentScanController::class,'dechargement'])->name('dechargement'); 
        Route::get('/get-colis-entrepot',[AgentScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement',[AgentScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-chargement',[AgentScanController::class, 'get_colis_charge'])->name('get.colis.charge');

        Route::get('/chauffeur/data',[AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');

        Route::get('/store',[AgentTransportController::class, 'store'])->name('store');
        Route::post('/store', [AgentTransportController::class,'store'])->name('store'); 

    });
    Route::prefix('agent_transport')->name('agent_transport.')->group(function(){
        Route::get('/', [AgentTransportController::class,'index'])->name('index'); 
        Route::get('/create', [AgentTransportController::class,'create'])->name('create');
        Route::get('/show-chauffeur', [AgentTransportController::class,'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur', [AgentTransportController::class,'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/reference.auto/{query}', [AgentTransportController::class, 'reference_auto'])->name('reference.auto');

        Route::get('/chauffeur/data',[AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');

        Route::get('/store',[AgentTransportController::class, 'store'])->name('store');
        Route::post('/store', [AgentTransportController::class,'store'])->name('store'); 

    });
   
});
Route::get('/programme', function () {
    return view('admin.Programme.programme'); // Chemin correct : admin/RDV/rdv.blade.php
})->name('programme.index');
Route::post('/programme/chauffeur/store', [ProgrammeController::class, 'storeChauffeur'])->name('programme.chauffeur.store');
Route::post('/programme/store', [ProgrammeController::class, 'storeProgramme'])->name('programme.store');
Route::get('/programme/data', [ProgrammeController::class, 'data'])->name('programme.data');