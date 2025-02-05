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
// use App\Http\Controllers\ApmsAngreColisController; 
use App\Http\Controllers\ApmsAngreColisController;
use App\Http\Controllers\ApmsAngreScanController;
use App\Http\Controllers\ApmsColisController;
use App\Http\Controllers\ApmsScanController;

use App\Http\Controllers\TransportController;
use App\Http\Controllers\AgentTransportController;
use App\Http\Controllers\GestionAgentController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\AgentScanController;
use App\Http\Controllers\QrcodeController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\NavAdminController;

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
        Route::get('/get-colis-valide',[ColisController::class, 'get_colis_valide'])->name('get.colis.valide');
        Route::get('/devis/{id}/edit', [ColisController::class, 'edit_qrcode'])->name('qrcode.edit');

        Route::get('/get-contenaire-colis',[ColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/get-vol-colis',[ColisController::class, 'get_colis_vol'])->name('get.colis.vol');
        Route::get('/get-colis-hold',[ColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/devis-hold',[ColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/colis-valide',[ColisController::class, 'colis_valide'])->name('colis.valide');
        Route::get('/cargaison-ferme',[ColisController::class, 'cargaison_ferme'])->name('cargaison.ferme');
        Route::get('/get-cargaison-ferme',[ColisController::class, 'get_cargaison_ferme'])->name('get.cargaison.ferme');

        // route edit
        Route::get('/on-hold/{id}/edit', [ColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit', [ColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}', [ColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}', [ColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print', [ColisController::class, 'print_facture'])->name('facture.colis.print');

        // route suppression edit
        Route::delete('/colis/{id}', [ColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');
        // Route::delete('/colis-valide/{id}', [ColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');
        // Route::delete('/colis-valide/{id}', [ColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');

        // route contenaire fermer
        Route::post('/contenaire-fermer',[ColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');

        Route::get('/list-contenaire',[ColisController::class, 'liste_contenaire'])->name('liste.contenaire');
        Route::get('/list-vol',[ColisController::class, 'liste_vol'])->name('liste.vol');


        Route::post('/store', [ColisController::class,'store'])->name('store'); 
        Route::get('/{coli}', [ColisController::class,'show'])->name('show'); 
        Route::get('/{coli}/edit', [ColisController::class,'edit'])->name('edit'); 
        Route::put('/{coli}', [ColisController::class,'update'])->name('update'); 
        Route::delete('/{coli}', [ColisController::class,'destroy'])->name('destroy');
        Route::post('/store-expediteur', [ColisController::class,'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [ColisController::class,'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [ColisController::class, 'search'])->name('search.expediteurs');

        Route::get('/create/colis/ajoute', [ColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis/store', [ColisController::class, 'store_colis'])->name('store.colis');
        Route::get('/create/payement/colis', [ColisController::class, 'stepPayment'])->name('create.payement');
        Route::post('/store/payment/colis', [ColisController::class, 'storePayment'])->name('store.payment');
        Route::get('/generer/qrcode.colis', [ColisController::class, 'generer_qrcode'])->name('generer.qrcode');


        Route::get('/create/colis', [ColisController::class, 'createStep1'])->name('create.step1');
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
        Route::get('/programme-chauffeur', [TransportController::class,'programme_chauffeur'])->name('programme.chauffeur');
        Route::get('/show-chauffeur', [TransportController::class,'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur', [TransportController::class,'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/reference.auto/{query}', [TransportController::class, 'reference_auto'])->name('reference.auto');

        Route::get('/chauffeur/data',[TransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::get('/programme/data',[TransportController::class, 'get_programme_list'])->name('get.programme.list');
        Route::post('/store-chauffeur', [TransportController::class,'store_chauffeur'])->name('store.chauffeur'); 
        Route::post('/store-planification', [TransportController::class,'store_plannification'])->name('store.plannification'); 
        // route edit programme et update programme
        Route::get('/programme/{id}/edit', [TransportController::class, 'edit_programme'])->name('programme.edit');
        Route::put('/on-hold/{id}', [ColisController::class, 'update_hold'])->name('hold.update');
        Route::delete('/programme/{id}', [TransportController::class, 'delete_chauffeur'])->name('programme.delete');

        Route::get('/store',[TransportController::class, 'store'])->name('store');
        Route::post('/store', [TransportController::class,'store'])->name('store'); 

});
    Route::prefix('chauffeur')->name('chauffeur.')->group(function(){
    });
    
        // Client rouute
        Route::prefix('client')->name('client.')->group(function(){
            Route::get('/', [ClientController::class,'index'])->name('index');
            Route::get('/get-client',[ClientController::class, 'get_client'])->name('get.client');
            Route::get('/clients/data', [ClientController::class, 'getClientsData'])->name('clients.data');
        });




        Route::prefix('notification')->name('notification.')->group(function(){
            Route::get('/', [NavAdminController::class,'index'])->name('index');
            Route::get('/get-notifications',[NavAdminController::class, 'get_notifications'])->name('get.notifications');
            Route::post('/notification-markAsRead', [NavAdminController::class, 'markAsRead'])->name('markAsRead');

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
        Route::post('/update-colis-status/entrepot', [ScanController::class, 'getColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge', [ScanController::class, 'getColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge', [ScanController::class, 'getColisDecharge'])->name('update.colis.decharge');

        Route::get('/store',[TransportController::class, 'store'])->name('store');
        Route::post('/store', [TransportController::class,'store'])->name('store'); 

    });
    Route::prefix('setting')->name('setting.')->group(function(){
        Route::get('/', [SettingController::class,'index'])->name('index');
        Route::get('/agence-info',[SettingController::class, 'agenceIndex'])->name('agence.index');
        Route::get('/chauffeur-info',[SettingController::class, 'chauffeurIndex'])->name('chauffeur.index');
        Route::get('/clients/data', [SettingController::class, 'getClientsData'])->name('clients.data');
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

    // route edit de paiement
            Route::get('/payment/{id}/edit', [CustomerColisController::class, 'edit_payement'])->name('payement.edit');
            Route::post('/store-payement{id}', [CustomerColisController::class, 'step_payement'])->name('store.payement');




            // Route::get('/agence/data',[AgenceController::class, 'get_agence'])->name('getAgence');

            // Route::post('/store', [AgenceController::class,'store'])->name('store'); 

            Route::get('/create/colis/add', [CustomerColisController::class, 'add_colis'])->name('create.colis');
            Route::post('/store/colis/store/customer', [CustomerColisController::class, 'store_colis'])->name('store.colis');
            Route::get('/create/payement/colis', [ColisController::class, 'stepPayment'])->name('create.payement');
            Route::post('/store/payment/colis', [ColisController::class, 'storePayment'])->name('store.payment');
            Route::get('/generer/qrcode.colis', [ColisController::class, 'generer_qrcode'])->name('generer.qrcode');
            Route::get('/create/colis/end', [CustomerColisController::class, 'end_colis'])->name('create.end');



            Route::get('/create/step1', [CustomerColisController::class, 'createStep1'])->name('create.step1');
            Route::post('/store/step1', [CustomerColisController::class, 'storeStep1'])->name('store.step1');
            Route::get('/create/step2', [CustomerColisController::class, 'createStep2'])->name('create.step2');
            Route::post('/store/step2', [CustomerColisController::class, 'storeStep2'])->name('store.step2');
            Route::get('/create/step3', [CustomerColisController::class, 'createStep3'])->name('create.step3');
            Route::post('/store/step3', [CustomerColisController::class, 'storeStep3'])->name('store.step3');

            Route::get('/create/step4', [CustomerColisController::class, 'createStep4'])->name('create.step4');
            Route::post('/store/step4', [CustomerColisController::class, 'storeStep4'])->name('store.step4');

            // Route::get('/create/payement', [CustomerColisController::class, 'stepPayement'])->name('create.payement');
            // Route::post('/store/payement', [CustomerColisController::class, 'storePayement'])->name('store.payement');
            Route::get('/create/qrcode', [CustomerColisController::class, 'qrcode'])->name('create.qrcode');
            Route::get('/complete', [CustomerColisController::class, 'complete'])->name('complete');

        });
        Route::prefix('customer_notification')->name('customer_notification.')->group(function(){
            Route::get('/', [NavCustomerController::class,'index'])->name('index');
            Route::get('/get-notifications',[NavCustomerController::class, 'get_notifications'])->name('get.notifications');
            Route::post('/notification-markAsRead', [NavCustomerController::class, 'markAsRead'])->name('markAsRead');

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
        // Route::get('/get-devis-colis',[ColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/get-contenaire-colis',[AgentColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/get-colis-hold',[AgentColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/devis-hold',[AgentColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-devis-colis',[AgentColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/devis/{id}/edit', [AgentColisController::class, 'edit_qrcode'])->name('qrcode.edit');
        Route::get('/colis-valide',[AgentColisController::class, 'colis_valide'])->name('colis.valide');
        Route::get('/get-colis-valide',[AgentColisController::class, 'get_colis_valide'])->name('get.colis.valide');
        // Route::get('/get-colis-valide',[ColisController::class, 'get_colis_valide'])->name('get.colis.valide');



        // rouute pour les cargaison
        Route::get('/get-vol-colis',[AgentColisController::class, 'get_colis_vol'])->name('get.colis.vol');
        Route::get('/cargaison-ferme',[AgentColisController::class, 'cargaison_ferme'])->name('cargaison.ferme');
        Route::get('/get-cargaison-ferme',[AgentColisController::class, 'get_cargaison_ferme'])->name('get.cargaison.ferme');
        Route::get('/list-vol',[AgentColisController::class, 'liste_vol'])->name('liste.vol');
        // Route::get('/list-vol',[ColisController::class, 'liste_vol'])->name('liste.vol');
        // route edit
        Route::get('/on-hold/{id}/edit', [AgentColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit', [AgentColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}', [AgentColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}', [AgentColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print', [AgentColisController::class, 'print_facture'])->name('facture.colis.print');



        Route::get('/get-contenaire-colis',[AgentColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/get-colis-hold',[AgentColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/devis-hold',[AgentColisController::class, 'devis_hold'])->name('devis.hold');
        // route contenaire fermer
        Route::post('/contenaire-fermer',[AgentColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');
        
        Route::get('/on-hold/{id}/edit', [AgentColisController::class, 'edit_hold'])->name('hold.edit');
        Route::put('/on-hold/{id}', [AgentColisController::class, 'update_hold'])->name('hold.update');

        Route::get('/list-contenaire',[AgentColisController::class, 'liste_contenaire'])->name('liste.contenaire');
        Route::get('/list-vol',[AgentColisController::class, 'liste_vol'])->name('liste.vol');
        // Route::get('/on-hold/{id}/edit', [ColisController::class, 'edit_hold'])->name('hold.edit');

        // route suppression edit
        Route::delete('/colis/{id}', [AgentColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');


        Route::post('/store', [AgentAgentColisController::class,'store'])->name('store'); 
        Route::get('/{coli}', [AgentColisController::class,'show'])->name('show'); 
        Route::get('/{coli}/edit', [AgentColisController::class,'edit'])->name('edit'); 
        Route::put('/{coli}', [AgentColisController::class,'update'])->name('update'); 
        Route::delete('/{coli}', [AgentColisController::class,'destroy'])->name('destroy');
        Route::post('/store-expediteur', [AgentColisController::class,'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [AgentColisController::class,'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [AgentColisController::class, 'search'])->name('search.expediteurs');

        Route::get('/create/colis', [AgentColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis', [AgentColisController::class, 'store_colis'])->name('store.colis');
        Route::get('/create/payement', [AgentColisController::class, 'stepPayment'])->name('create.payement');
        Route::post('/store/payment', [AgentColisController::class, 'storePayment'])->name('store.payment');
        Route::get('/generer/qrcode', [AgentColisController::class, 'generer_qrcode'])->name('generer.qrcode');



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
        Route::post('/update-colis-status/entrepot', [ScanController::class, 'getColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge', [ScanController::class, 'getColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge', [ScanController::class, 'getColisDecharge'])->name('update.colis.decharge');
        
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
        Route::post('/store-chauffeur', [AgentTransportController::class,'store_chauffeur'])->name('store.chauffeur'); 
        Route::post('/store-planification', [AgentTransportController::class,'store_plannification'])->name('store.plannification'); 
        Route::get('/store',[AgentTransportController::class, 'store'])->name('store');
        Route::post('/store', [AgentTransportController::class,'store'])->name('store'); 

    });
    Route::prefix('agent_notification')->name('agent_notification.')->group(function(){
        Route::get('/', [NavAdminController::class,'index'])->name('index');
        Route::get('/get-notifications',[NavAdminController::class, 'get_notifications'])->name('get.notifications');
        Route::post('/notification-markAsRead', [NavAdminController::class, 'markAsRead'])->name('markAsRead');

    });
   
});

// ROUTE POUR INTERFACE AFT AGENCE LOUIS BLERIOT

Route::prefix('AFT_LOUIS_BLERIOT')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'AFT_LOUIS_BLERIOT_INDEX'])->name('AFT_LOUIS_BLERIOT.dashboard');

    // Groupe de routes pour les opérations sur les colis
    Route::prefix('agent_colis')->name('agent_colis.')->group(function(){
        Route::get('/', [AgentColisController::class, 'index'])->name('index'); 
        Route::get('/on-hold', [AgentColisController::class, 'hold'])->name('hold'); 
        Route::get('/history', [AgentColisController::class, 'history'])->name('history'); 
        Route::get('/on-dump', [AgentColisController::class, 'dump'])->name('dump'); 
        Route::get('/create', [AgentColisController::class, 'create'])->name('create'); 
        Route::get('/get-colis', [AgentColisController::class, 'get_colis'])->name('getColis');
        Route::get('/get-colis-hold', [AgentColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/get-colis-dump', [AgentColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-colis-contenaire', [AgentColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/devis-hold', [AgentColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-devis-colis', [AgentColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/devis/{id}/edit', [AgentColisController::class, 'edit_qrcode'])->name('qrcode.edit');
        Route::get('/colis-valide', [AgentColisController::class, 'colis_valide'])->name('colis.valide');
        Route::get('/get-colis-valide', [AgentColisController::class, 'get_colis_valide'])->name('get.colis.valide');

        // Routes pour les cargaisons
        Route::get('/get-vol-colis', [AgentColisController::class, 'get_colis_vol'])->name('get.colis.vol');
        Route::get('/cargaison-ferme', [AgentColisController::class, 'cargaison_ferme'])->name('cargaison.ferme');
        Route::get('/get-cargaison-ferme', [AgentColisController::class, 'get_cargaison_ferme'])->name('get.cargaison.ferme');
        Route::get('/list-vol', [AgentColisController::class, 'liste_vol'])->name('liste.vol');

        // Routes d'édition et mise à jour
        Route::get('/on-hold/{id}/edit', [AgentColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit', [AgentColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}', [AgentColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}', [AgentColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print', [AgentColisController::class, 'print_facture'])->name('facture.colis.print');

        // Route pour fermer un contenaire
        Route::post('/contenaire-fermer', [AgentColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');
        
        // Liste des conteneurs
        Route::get('/list-contenaire', [AgentColisController::class, 'liste_contenaire'])->name('liste.contenaire');
        
        // Suppression d'un colis validé
        Route::delete('/colis/{id}', [AgentColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');

        // CRUD classique sur colis
        Route::post('/store', [AgentColisController::class, 'store'])->name('store'); 
        Route::get('/{coli}', [AgentColisController::class, 'show'])->name('show'); 
        Route::get('/{coli}/edit', [AgentColisController::class, 'edit'])->name('edit'); 
        Route::put('/{coli}', [AgentColisController::class, 'update'])->name('update'); 
        Route::delete('/{coli}', [AgentColisController::class, 'destroy'])->name('destroy');

        // Enregistrement des expéditeurs et destinataires
        Route::post('/store-expediteur', [AgentColisController::class, 'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [AgentColisController::class, 'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [AgentColisController::class, 'search'])->name('search.expediteurs');

        // Création et stockage d'un colis
        Route::get('/create/colis', [AgentColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis', [AgentColisController::class, 'store_colis'])->name('store.colis');

        // Gestion du paiement et génération de QR code
        Route::get('/create/payement', [AgentColisController::class, 'stepPayment'])->name('create.payement');
        Route::post('/store/payment', [AgentColisController::class, 'storePayment'])->name('store.payment');
        Route::get('/generer/qrcode', [AgentColisController::class, 'generer_qrcode'])->name('generer.qrcode');
    });

    // Groupe de routes pour la gestion du scan
    Route::prefix('agent_scan')->name('agent_scan.')->group(function(){
        Route::get('/en-entrepot', [AgentScanController::class, 'entrepot'])->name('entrepot'); 
        Route::get('/en-chargement', [AgentScanController::class, 'chargement'])->name('chargement'); 
        Route::get('/en-dechargement', [AgentScanController::class, 'dechargement'])->name('dechargement'); 
        Route::get('/get-colis-entrepot', [AgentScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement', [AgentScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-chargement', [AgentScanController::class, 'get_colis_charge'])->name('get.colis.charge');
        Route::post('/update-colis-status/entrepot', [AgentScanController::class, 'updateColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge', [AgentScanController::class, 'updateColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge', [AgentScanController::class, 'updateColisDecharge'])->name('update.colis.decharge');

        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store');
    });

    // Groupe de routes pour la gestion du transport
    Route::prefix('agent_transport')->name('agent_transport.')->group(function(){
        Route::get('/', [AgentTransportController::class, 'index'])->name('index'); 
        Route::get('/create', [AgentTransportController::class, 'create'])->name('create');
        Route::get('/show-chauffeur', [AgentTransportController::class, 'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur', [AgentTransportController::class, 'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/reference.auto/{query}', [AgentTransportController::class, 'reference_auto'])->name('reference.auto');
        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::post('/store-chauffeur', [AgentTransportController::class, 'store_chauffeur'])->name('store.chauffeur'); 
        Route::post('/store-planification', [AgentTransportController::class, 'store_plannification'])->name('store.plannification'); 
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store'); 
    });

    // Groupe de routes pour les notifications de l'agent
    Route::prefix('agent_notification')->name('agent_notification.')->group(function(){
        Route::get('/', [NavAdminController::class, 'index'])->name('index');
        Route::get('/get-notifications', [NavAdminController::class, 'get_notifications'])->name('get.notifications');
        Route::post('/notification-markAsRead', [NavAdminController::class, 'markAsRead'])->name('markAsRead');
    });
});


Route::prefix('IPMS_SIMEXCI')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'IPMS_SIMEXCI_INDEX'])->name('IPMS_SIMEXCI.dashboard');

    // Groupe de routes pour les opérations sur les colis
    Route::prefix('ipms_colis')->name('ipms_colis.')->group(function(){
        Route::get('/', [ApmsColisController::class, 'index'])->name('index'); 
        Route::get('/on-hold-simexci', [ApmsColisController::class, 'hold'])->name('hold'); 
        Route::get('/history-simexci', [ApmsColisController::class, 'history'])->name('history'); 
        Route::get('/on-dump-simexci', [ApmsColisController::class, 'dump'])->name('dump'); 
        Route::get('/create-simexci', [ApmsColisController::class, 'create'])->name('create'); 
        Route::get('/get-colis-simexci', [ApmsColisController::class, 'get_colis'])->name('getColis');
        Route::get('/get-colis-hold-simexci', [ApmsColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/get-colis-dump-simexci', [ApmsColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-colis-contenaire-simexci', [ApmsColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/devis-hold-simexci', [ApmsColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-devis-colis-simexci', [ApmsColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/devis/{id}/edit-simexci', [ApmsColisController::class, 'edit_qrcode'])->name('qrcode.edit');
        Route::get('/colis-valide-simexci', [ApmsColisController::class, 'colis_valide'])->name('colis.valide');
        Route::get('/get-colis-valide-simexci', [ApmsColisController::class, 'get_colis_valide'])->name('get.colis.valide');

        // Routes pour les cargaisons
        Route::get('/get-vol-colis-simexci', [ApmsColisController::class, 'get_colis_vol'])->name('get.colis.vol');
        Route::get('/cargaison-ferme-simexci', [ApmsColisController::class, 'cargaison_ferme'])->name('cargaison.ferme');
        Route::get('/get-cargaison-ferme-simexci', [ApmsColisController::class, 'get_cargaison_ferme'])->name('get.cargaison.ferme');
        Route::get('/list-vo-simexcil', [ApmsColisController::class, 'liste_vol'])->name('liste.vol');

        // Routes d'édition et mise à jour
        Route::get('/on-hold/{id}/edit', [ApmsColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit', [ApmsColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}', [ApmsColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}', [ApmsColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print', [ApmsColisController::class, 'print_facture'])->name('facture.colis.print');

        // Route pour fermer un contenaire
        Route::post('/contenaire-fermer', [ApmsColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');
        
        // Liste des conteneurs
        Route::get('/list-contenaire', [ApmsColisController::class, 'liste_contenaire'])->name('liste.contenaire');
        
        // Suppression d'un colis validé
        Route::delete('/colis/{id}', [ApmsColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');

        // CRUD classique sur colis
        Route::post('/store', [ApmsColisController::class, 'store'])->name('store'); 
        Route::get('/{coli}', [ApmsColisController::class, 'show'])->name('show'); 
        Route::get('/{coli}/edit', [ApmsColisController::class, 'edit'])->name('edit'); 
        Route::put('/{coli}', [ApmsColisController::class, 'update'])->name('update'); 
        Route::delete('/{coli}', [ApmsColisController::class, 'destroy'])->name('destroy');

        // Enregistrement des expéditeurs et destinataires
        Route::post('/store-expediteur', [ApmsColisController::class, 'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [ApmsColisController::class, 'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [ApmsColisController::class, 'search'])->name('search.expediteurs');

        // Création et stockage d'un colis
        Route::get('/create/colis', [ApmsColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis', [ApmsColisController::class, 'store_colis'])->name('store.colis');

        // Gestion du paiement et génération de QR code
        Route::get('/create/payement', [ApmsColisController::class, 'stepPayment'])->name('create.payement');
        Route::post('/store/payment', [ApmsColisController::class, 'storePayment'])->name('store.payment');
        Route::get('/generer/qrcode', [ApmsColisController::class, 'generer_qrcode'])->name('generer.qrcode');
    });

        // Groupe de routes pour la gestion du scan
        Route::prefix('ipms_scan')->name('ipms_scan.')->group(function(){
            Route::get('/en-entrepot-simexci', [ApmsScanController::class, 'entrepot'])->name('entrepot'); 
            Route::get('/en-chargement-simexci', [ApmsScanController::class, 'chargement'])->name('chargement'); 
            Route::get('/en-dechargement-simexci', [ApmsScanController::class, 'dechargement'])->name('dechargement'); 
            Route::get('/get-colis-entrepot-simexci', [ApmsScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
            Route::get('/get-colis-dechargement-simexci', [ApmsScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
            Route::get('/get-colis-chargement-simexci', [ApmsScanController::class, 'get_colis_charge'])->name('get.colis.charge');
            Route::post('/update-colis-status/entrepot-simexci', [ApmsScanController::class, 'updateColisEntrepot'])->name('update.colis.entrepot');
            Route::post('/update-colis-status/charge-simexci', [ApmsScanController::class, 'updateColisCharge'])->name('update.colis.charge');
            Route::post('/update-colis-status/decharge-simexci', [ApmsScanController::class, 'updateColisDecharge'])->name('update.colis.decharge');

            Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
            Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store');
        });

        // Groupe de routes pour la gestion du transport
        Route::prefix('ipms_transport')->name('agent_transport.')->group(function(){
            Route::get('/', [AgentTransportController::class, 'index'])->name('index'); 
            Route::get('/create', [AgentTransportController::class, 'create'])->name('create');
            Route::get('/show-chauffeur', [AgentTransportController::class, 'show_chauffeur'])->name('show.chauffeur');
            Route::get('/planing-chauffeur', [AgentTransportController::class, 'planing_chauffeur'])->name('planing.chauffeur');
            Route::get('/reference.auto/{query}', [AgentTransportController::class, 'reference_auto'])->name('reference.auto');
            Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
            Route::post('/store-chauffeur', [AgentTransportController::class, 'store_chauffeur'])->name('store.chauffeur'); 
            Route::post('/store-planification', [AgentTransportController::class, 'store_plannification'])->name('store.plannification'); 
            Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store'); 
        });

        // Groupe de routes pour les notifications de l'agent
        Route::prefix('agent_notification')->name('agent_notification.')->group(function(){
            Route::get('/', [NavAdminController::class, 'index'])->name('index');
            Route::get('/get-notifications', [NavAdminController::class, 'get_notifications'])->name('get.notifications');
            Route::post('/notification-markAsRead', [NavAdminController::class, 'markAsRead'])->name('markAsRead');
        });
});


Route::prefix('IPMS_SIMEXCI_ANGRE')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'IPMS_SIMEXCI_ANGRE_INDEX'])->name('IPMS_SIMEXCI_ANGRE.dashboard');

    // Groupe de routes pour les opérations sur les colis
    Route::prefix('ipms_angre_colis')->name('ipms_angre_colis.')->group(function(){
        Route::get('/', [ApmsAngreColisController::class, 'index'])->name('index'); 
        Route::get('/history-IPMS', [ApmsAngreColisController::class, 'history'])->name('history'); 
        Route::get('/on-dump-IPMS', [ApmsAngreColisController::class, 'dump'])->name('dump'); 
        Route::get('/get-colis-dump-IPMS', [ApmsAngreColisController::class, 'get_colis_dump'])->name('get.colis.dump');
       
        // Routes d'édition et mise à jour
        Route::get('/on-hold/{id}/edit-IPMS', [ApmsAngreColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit-IPMS', [ApmsAngreColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}', [ApmsAngreColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}', [ApmsAngreColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print-IPMS', [ApmsAngreColisController::class, 'print_facture'])->name('facture.colis.print');

        // CRUD classique sur colis
        Route::post('/store', [ApmsAngreColisController::class, 'store'])->name('store'); 
        Route::get('/{coli}', [ApmsAngreColisController::class, 'show'])->name('show'); 
        Route::get('/{coli}/edit', [ApmsAngreColisController::class, 'edit'])->name('edit'); 
        Route::put('/{coli}', [ApmsAngreColisController::class, 'update'])->name('update'); 
        Route::delete('/{coli}', [ApmsAngreColisController::class, 'destroy'])->name('destroy');

        // Enregistrement des expéditeurs et destinataires
        Route::post('/store-expediteur', [ApmsAngreColisController::class, 'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [ApmsAngreColisController::class, 'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [ApmsAngreColisController::class, 'search'])->name('search.expediteurs');
       
    });

    // Groupe de routes pour la gestion du scan
    Route::prefix('ipms_angre_scan')->name('ipms_angre_scan.')->group(function(){
        Route::get('/en-entrepot-IPMS', [ApmsAngreScanController::class, 'entrepot'])->name('entrepot'); 
        Route::get('/en-chargement-IPMS', [ApmsAngreScanController::class, 'chargement'])->name('chargement'); 
        Route::get('/en-dechargement-IPMS', [ApmsAngreScanController::class, 'dechargement'])->name('dechargement'); 
        Route::get('/get-colis-entrepot-IPMS', [ApmsAngreScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement-IPMS', [ApmsAngreScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-chargement-IPMS', [ApmsAngreScanController::class, 'get_colis_charge'])->name('get.colis.charge');
        Route::post('/update-colis-status/entrepot-IPMS', [ApmsAngreScanController::class, 'updateColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge-IPMS', [ApmsAngreScanController::class, 'updateColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge-IPMS', [ApmsAngreScanController::class, 'updateColisDecharge'])->name('update.colis.decharge');

        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store');
    });

    // Groupe de routes pour la gestion du transport
    Route::prefix('agent_transport')->name('agent_transport.')->group(function(){
        Route::get('/', [AgentTransportController::class, 'index'])->name('index'); 
        Route::get('/create', [AgentTransportController::class, 'create'])->name('create');
        Route::get('/show-chauffeur', [AgentTransportController::class, 'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur', [AgentTransportController::class, 'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/reference.auto/{query}', [AgentTransportController::class, 'reference_auto'])->name('reference.auto');
        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::post('/store-chauffeur', [AgentTransportController::class, 'store_chauffeur'])->name('store.chauffeur'); 
        Route::post('/store-planification', [AgentTransportController::class, 'store_plannification'])->name('store.plannification'); 
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store'); 
    });

    // Groupe de routes pour les notifications de l'agent
    Route::prefix('agent_notification')->name('agent_notification.')->group(function(){
        Route::get('/', [NavAdminController::class, 'index'])->name('index');
        Route::get('/get-notifications', [NavAdminController::class, 'get_notifications'])->name('get.notifications');
        Route::post('/notification-markAsRead', [NavAdminController::class, 'markAsRead'])->name('markAsRead');
    });
});


Route::prefix('AGENCE_CHINE')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'AGENCE_CHINE_INDEX'])->name('AGENCE_CHINE.dashboard');

    // Groupe de routes pour les opérations sur les colis
    Route::prefix('agent_colis')->name('agent_colis.')->group(function(){
        Route::get('/', [AgentColisController::class, 'index'])->name('index'); 
        Route::get('/on-hold', [AgentColisController::class, 'hold'])->name('hold'); 
        Route::get('/history', [AgentColisController::class, 'history'])->name('history'); 
        Route::get('/on-dump', [AgentColisController::class, 'dump'])->name('dump'); 
        Route::get('/create', [AgentColisController::class, 'create'])->name('create'); 
        Route::get('/get-colis', [AgentColisController::class, 'get_colis'])->name('getColis');
        Route::get('/get-colis-hold', [AgentColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/get-colis-dump', [AgentColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-colis-contenaire', [AgentColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/devis-hold', [AgentColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-devis-colis', [AgentColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/devis/{id}/edit', [AgentColisController::class, 'edit_qrcode'])->name('qrcode.edit');
        Route::get('/colis-valide', [AgentColisController::class, 'colis_valide'])->name('colis.valide');
        Route::get('/get-colis-valide', [AgentColisController::class, 'get_colis_valide'])->name('get.colis.valide');

        // Routes pour les cargaisons
        Route::get('/get-vol-colis', [AgentColisController::class, 'get_colis_vol'])->name('get.colis.vol');
        Route::get('/cargaison-ferme', [AgentColisController::class, 'cargaison_ferme'])->name('cargaison.ferme');
        Route::get('/get-cargaison-ferme', [AgentColisController::class, 'get_cargaison_ferme'])->name('get.cargaison.ferme');
        Route::get('/list-vol', [AgentColisController::class, 'liste_vol'])->name('liste.vol');

        // Routes d'édition et mise à jour
        Route::get('/on-hold/{id}/edit', [AgentColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit', [AgentColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}', [AgentColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}', [AgentColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print', [AgentColisController::class, 'print_facture'])->name('facture.colis.print');

        // Route pour fermer un contenaire
        Route::post('/contenaire-fermer', [AgentColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');
        
        // Liste des conteneurs
        Route::get('/list-contenaire', [AgentColisController::class, 'liste_contenaire'])->name('liste.contenaire');
        
        // Suppression d'un colis validé
        Route::delete('/colis/{id}', [AgentColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');

        // CRUD classique sur colis
        Route::post('/store', [AgentColisController::class, 'store'])->name('store'); 
        Route::get('/{coli}', [AgentColisController::class, 'show'])->name('show'); 
        Route::get('/{coli}/edit', [AgentColisController::class, 'edit'])->name('edit'); 
        Route::put('/{coli}', [AgentColisController::class, 'update'])->name('update'); 
        Route::delete('/{coli}', [AgentColisController::class, 'destroy'])->name('destroy');

        // Enregistrement des expéditeurs et destinataires
        Route::post('/store-expediteur', [AgentColisController::class, 'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire', [AgentColisController::class, 'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs', [AgentColisController::class, 'search'])->name('search.expediteurs');

        // Création et stockage d'un colis
        Route::get('/create/colis', [AgentColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis', [AgentColisController::class, 'store_colis'])->name('store.colis');

        // Gestion du paiement et génération de QR code
        Route::get('/create/payement', [AgentColisController::class, 'stepPayment'])->name('create.payement');
        Route::post('/store/payment', [AgentColisController::class, 'storePayment'])->name('store.payment');
        Route::get('/generer/qrcode', [AgentColisController::class, 'generer_qrcode'])->name('generer.qrcode');
    });

    // Groupe de routes pour la gestion du scan
    Route::prefix('agent_scan')->name('agent_scan.')->group(function(){
        Route::get('/en-entrepot', [AgentScanController::class, 'entrepot'])->name('entrepot'); 
        Route::get('/en-chargement', [AgentScanController::class, 'chargement'])->name('chargement'); 
        Route::get('/en-dechargement', [AgentScanController::class, 'dechargement'])->name('dechargement'); 
        Route::get('/get-colis-entrepot', [AgentScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement', [AgentScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-chargement', [AgentScanController::class, 'get_colis_charge'])->name('get.colis.charge');
        Route::post('/update-colis-status/entrepot', [AgentScanController::class, 'updateColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge', [AgentScanController::class, 'updateColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge', [AgentScanController::class, 'updateColisDecharge'])->name('update.colis.decharge');

        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store');
    });

    // Groupe de routes pour la gestion du transport
    Route::prefix('agent_transport')->name('agent_transport.')->group(function(){
        Route::get('/', [AgentTransportController::class, 'index'])->name('index'); 
        Route::get('/create', [AgentTransportController::class, 'create'])->name('create');
        Route::get('/show-chauffeur', [AgentTransportController::class, 'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur', [AgentTransportController::class, 'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/reference.auto/{query}', [AgentTransportController::class, 'reference_auto'])->name('reference.auto');
        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::post('/store-chauffeur', [AgentTransportController::class, 'store_chauffeur'])->name('store.chauffeur'); 
        Route::post('/store-planification', [AgentTransportController::class, 'store_plannification'])->name('store.plannification'); 
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store'); 
    });

    // Groupe de routes pour les notifications de l'agent
    Route::prefix('agent_notification')->name('agent_notification.')->group(function(){
        Route::get('/', [NavAdminController::class, 'index'])->name('index');
        Route::get('/get-notifications', [NavAdminController::class, 'get_notifications'])->name('get.notifications');
        Route::post('/notification-markAsRead', [NavAdminController::class, 'markAsRead'])->name('markAsRead');
    });
});








Route::get('/programme', function () {
    return view('admin.Programme.programme'); // Chemin correct : admin/RDV/rdv.blade.php
})->name('programme.index');
Route::post('/programme/chauffeur/store', [ProgrammeController::class, 'storeChauffeur'])->name('programme.chauffeur.store');
Route::post('/programme/store', [ProgrammeController::class, 'storeProgramme'])->name('programme.store');
Route::get('/programme/data', [ProgrammeController::class, 'data'])->name('programme.data');