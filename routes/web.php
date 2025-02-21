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
use App\Http\Controllers\AftlbColisController; 
use App\Http\Controllers\AftlbScanController; 
use App\Http\Controllers\AftlbInvoiceController; 
use App\Http\Controllers\ChineInvoiceController;
use App\Http\Controllers\IpmsInvoiceController;
use App\Http\Controllers\IpmsAngreInvoiceController;
use App\Http\Controllers\AdminInvoiceController;
use App\Http\Controllers\ChineColisController; 
use App\Http\Controllers\ChineScanController; 
use App\Http\Controllers\ProgrammeLBController;
use App\Http\Controllers\AgentChineTransportController;
use App\Http\Controllers\AgentLBTransportController;
use App\Http\Controllers\AgentIPMSANGRETransportController;
// use App\Http\Controllers\ApmsAngreColisController; 
use App\Http\Controllers\ApmsAngreColisController;
use App\Http\Controllers\ApmsAngreScanController;
use App\Http\Controllers\ApmsColisController;
use App\Http\Controllers\ApmsScanController;
use App\Http\Controllers\ProgrammeChineController;
use App\Http\Controllers\RdvchineController;
use App\Http\Controllers\RdvipmxangreController;
use App\Http\Controllers\RdvlbController;


use App\Models\Colis;
use App\Http\Controllers\ProgrammeIPMXANGREController;
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
use App\Http\Controllers\NavAftlbController;
use App\Http\Controllers\ChauffeurAuthController;
use App\Http\Controllers\NavChineController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RdvController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\ChauffeurColisController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



Route::get('/', function () { return redirect('/login'); });
// Route::get('/agent', function () { return redirect('/login_admin'); });

Auth::routes();


Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');

    Route::get('/colis-admin/count', function () {
        $colisCount = Colis::where('etat', 'Validé')
                            ->count();
        return response()->json(['colisCount' => $colisCount]);
    })->name('admin_colis.count');

    Route::get('/colis-admin/prix-total', function () {
        $totalPrixTransit = Colis::where('etat', 'Validé')
            ->sum('prix_transit_colis');

        return response()->json(['totalPrixTransit' => $totalPrixTransit]);
    })->name('admin_colis.prix-total');


    Route::get('/colis-admin/vol-cargaison-count', function () {
        $count = Colis::where('mode_transit', 'aérien')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();

        return response()->json(['count' => $count]);
    })->name('admin_colis.vol-cargaison-count');

    Route::get('/colis-admin/conteneur-count', function () {
        $count = Colis::where('mode_transit', 'maritime')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();

        return response()->json(['count' => $count]);
    })->name('admin_colis.conteneur-count');

    Route::get('/colis-admin/valides-par-mois', function () {
        $currentYear = now()->year;

        $colisParMois = Colis::select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->where('etat', 'Validé')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->pluck('total', 'mois');

        // Initialiser un tableau avec 12 zéros pour chaque mois
        $data = array_fill(1, 12, 0);

        // Remplir le tableau avec les données récupérées
        foreach ($colisParMois as $mois => $total) {
            $data[$mois] = $total;
        }

        return response()->json(array_values($data));
    })->name('admin_colis.valides-par-mois');;


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

    Route::prefix('invoice')->name('invoice.')->group(function(){
        Route::get('/', [AdminInvoiceController::class, 'index'])->name('index'); 
        Route::get('/create/new/invoice', [AdminInvoiceController::class, 'create_invoice'])->name('create.invoice');
        Route::post('/store/new/invoice', [AdminInvoiceController::class, 'store_invoice'])->name('store.invoice');
        Route::get('/history/invoice', [AdminInvoiceController::class, 'historique_invoice'])->name('historique.invoice');
        Route::get('/edit/invoice', [AdminInvoiceController::class, 'edit_invoice'])->name('edit.invoice');
        Route::get('/show/invoice', [AdminInvoiceController::class, 'reference_auto'])->name('show.invoice');
        Route::get('/reference.auto/{query}', [AdminInvoiceController::class, 'reference_auto'])->name('reference.auto');
        Route::get('/get-invoice-fait',[AdminInvoiceController::class, 'get_invoice_historique'])->name('get.invoice.historique');

        });
        
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
    Route::prefix('admin/transport')->name('transport.')->group(function () {
        Route::get('/', [TransportController::class, 'index'])->name('index');
        Route::get('/chauffeur', [TransportController::class, 'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur', [TransportController::class, 'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/chauffeur/data', [TransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::post('/store-chauffeur', [TransportController::class, 'store_chauffeur'])->name('store.chauffeur');
        Route::get('/reference.auto/{query}', [TransportController::class, 'reference_auto'])->name('reference.auto');
        Route::get('/chauffeur/{id}/edit', [TransportController::class, 'editChauffeur'])->name('chauffeur.edit'); // Route pour récupérer les données pour l'édition
    Route::put('/chauffeur/{id}', [TransportController::class, 'updateChauffeur'])->name('chauffeur.update');  // Route pour mettre à jour le chauffeur
     Route::delete('/chauffeur/{id}', [TransportController::class, 'destroyChauffeur'])->name('chauffeur.destroy')   ->middleware('csrf');;
    // Route::delete('/chauffeur/{id}', [TransportController::class, 'destroyChauffeur'])->name('transport.chauffeur.destroy');
    });
    Route::get('/programme-planifie', function () {
        return view('admin.Programme.programme'); // Chemin correct : admin/RDV/rdv.blade.php
    })->name('programme.index');
    Route::post('/programme/chauffeur/store', [ProgrammeController::class, 'storeChauffeur'])->name('programme.chauffeur.store');
    Route::post('/programme/store', [ProgrammeController::class, 'storeProgramme'])->name('programme.store');
    Route::get('/programme/data', [ProgrammeController::class, 'data'])->name('programme.data');
    
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
        Route::delete('/programme-transport/{id}', [TransportController::class, 'delete_chauffeur'])->name('programme.delete');

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
    Route::get('/programme-index', [ProgrammeController::class, 'index'])->name('programme.index');
    Route::post('/programme/chauffeur/store', [ProgrammeController::class, 'storeChauffeur'])->name('programme.chauffeur.store');
    Route::post('/programme/store', [ProgrammeController::class, 'storeProgramme'])->name('programme.store');
    Route::get('/programme/data', [ProgrammeController::class, 'data'])->name('programme.data');
    Route::get('/programme/edit/{programme}', [ProgrammeController::class, 'edit']); // Route pour récupérer les données pour l'édition
    Route::put('/programme/update/{programme}', [ProgrammeController::class, 'update']); // Route pour la mise à jour
    Route::delete('/programme/delete/{programme}', [ProgrammeController::class, 'destroy']); // Route pour la suppression

    // Ajout des routes pour le programme de l'agent
       // Ajout des routes pour le RDV
    Route::get('/rdv', [RdvController::class, 'index'])->name('rdv.index');
    Route::get('/rdv/depot/data', [RdvController::class, 'depotData'])->name('rdv.depot.data');
    Route::get('/rdv/recuperation/data', [RdvController::class, 'recuperationData'])->name('rdv.recuperation.data');
   

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
            Route::get('/suivi-customer', [CustomerColisController::class,'suivi'])->name('suivi');
            Route::get('/facture', [CustomerColisController::class,'facture'])->name('facture');
            Route::get('/get-colis',[CustomerColisController::class, 'get_colis'])->name('get.colis');
            Route::get('/get-colis-suivi-customer',[CustomerColisController::class, 'get_colis_suivi'])->name('get.colis.suivi');
            Route::get('/get-colis-valide',[CustomerColisController::class, 'get_colis_valide'])->name('get.colis.valide');
            Route::get('/get-invoice',[CustomerColisController::class, 'get_facture'])->name('get.facture');
            Route::get('/facture/pdf', [CustomerColisController::class, 'telechargerPdf'])->name('facture.pdf');

    // route edit de paiement
            Route::get('/payment/{id}/edit', [CustomerColisController::class, 'edit_payement'])->name('payement.edit');
            Route::post('/store-payement{id}', [CustomerColisController::class, 'step_payement'])->name('store.payement');
           
            Route::get('/create/colis-aft-louis-b', [CustomerColisController::class, 'add_colis'])->name('create.colis');
            // Route::post('/store/colis-aft-louis-b', [CustomerColisController::class, 'store_colis'])->name('store.colis');
    



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


// ROUTE POUR INTERFACE AFT AGENCE LOUIS BLERIOT

Route::prefix('AFT_LOUIS_BLERIOT')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'AFT_LOUIS_BLERIOT_INDEX'])->name('AFT_LOUIS_BLERIOT.dashboard');


    Route::get('/colis/count', function () {
        $colisCount = Colis::where('etat', 'Validé')
                            ->whereHas('expediteur', function ($query) {
                                $query->where('agence', 'AFT Agence Louis Bleriot');
                            })
                            ->count();
        return response()->json(['colisCount' => $colisCount]);
    })->name('colis.count');

    Route::get('/colis/prix-total', function () {
        $totalPrixTransit = Colis::where('etat', 'Validé')
            ->whereHas('expediteur', function ($query) {
                $query->where('agence', 'AFT Agence Louis Bleriot');
            })
            ->sum('prix_transit_colis');

        return response()->json(['totalPrixTransit' => $totalPrixTransit]);
    })->name('colis.prix-total');


    Route::get('/colis/vol-cargaison-count', function () {
        $count = Colis::where('mode_transit', 'aérien')
            ->whereHas('expediteur', function ($query) {
                $query->where('agence', 'AFT Agence Louis Bleriot');
            })
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();

        return response()->json(['count' => $count]);
    })->name('colis.vol-cargaison-count');

    Route::get('/colis/conteneur-count', function () {
        $count = Colis::where('mode_transit', 'maritime')
            ->whereHas('expediteur', function ($query) {
                $query->where('agence', 'AFT Agence Louis Bleriot');
            })
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();

        return response()->json(['count' => $count]);
    })->name('colis.conteneur-count');

    Route::get('/colis/valides-par-mois', function () {
        $currentYear = now()->year;

        $colisParMois = Colis::select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->where('etat', 'Validé')
            ->whereHas('expediteur', function ($query) {
                $query->where('agence', 'AFT Agence Louis Bleriot');
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->pluck('total', 'mois');

        // Initialiser un tableau avec 12 zéros pour chaque mois
        $data = array_fill(1, 12, 0);

        // Remplir le tableau avec les données récupérées
        foreach ($colisParMois as $mois => $total) {
            $data[$mois] = $total;
        }

        return response()->json(array_values($data));
    })->name('colis.valides-par-mois');;

    // Groupe de routes pour les opérations sur les colis
    Route::prefix('aftlb_colis')->name('aftlb_colis.')->group(function(){
        Route::get('/', [AftlbColisController::class, 'index'])->name('index'); 
        Route::get('/on-hold-aft-louis-b', [AftlbColisController::class, 'hold'])->name('hold'); 
        Route::get('/history-aft-louis-b', [AftlbColisController::class, 'history'])->name('history'); 
        Route::get('/on-dump-aft-louis-b', [AftlbColisController::class, 'dump'])->name('dump'); 
        Route::get('/create-aft-louis-b', [AftlbColisController::class, 'create'])->name('create'); 
        Route::get('/get-colis-aft-louis-b', [AftlbColisController::class, 'get_colis'])->name('getColis');
        Route::get('/get-colis-hold-aft-louis-b', [AftlbColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/get-colis-dump-aft-louis-b', [AftlbColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-colis-contenaire-aft-louis-b', [AftlbColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/devis-hold-aftlb-louis-b', [AftlbColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-devis-colis-aft-louis-b', [AftlbColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/devis/{id}/edit-aft-louis-b', [AftlbColisController::class, 'edit_qrcode'])->name('qrcode.edit');
        Route::get('/colis-valide-aft-louis-b', [AftlbColisController::class, 'colis_valide'])->name('colis.valide');
        Route::get('/get-colis-valide-aft-louis-b', [AftlbColisController::class, 'get_colis_valide'])->name('get.colis.valide');

        // Routes pour les cargaisons
        Route::get('/get-vol-colis-aft-louis-b', [AftlbColisController::class, 'get_colis_vol'])->name('get.colis.vol');
        Route::get('/cargaison-ferme-aft-louis-b', [AftlbColisController::class, 'cargaison_ferme'])->name('cargaison.ferme');
        Route::get('/get-cargaison-ferme-aft-louis-b', [AftlbColisController::class, 'get_cargaison_ferme'])->name('get.cargaison.ferme');
        Route::get('/list-vol-aft-louis-b', [AftlbColisController::class, 'liste_vol'])->name('liste.vol');

        // Routes d'édition et mise à jour
        Route::get('/on-hold/{id}/edit-aft-louis-b', [AftlbColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit-aft-louis-b', [AftlbColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}-aft-louis-b', [AftlbColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}-aft-louis-b', [AftlbColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print-aft-louis-b', [AftlbColisController::class, 'print_facture'])->name('facture.colis.print');

        // Route pour fermer un contenaire
        Route::post('/contenaire-fermer-aft-louis-b', [AftlbColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');
        
        // Liste des conteneurs
        Route::get('/list-contenaire-aft-louis-b', [AftlbColisController::class, 'liste_contenaire'])->name('liste.contenaire');
        
        // Suppression d'un colis validé
        Route::delete('/colis/{id}-aft-louis-b', [AftlbColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');

        // CRUD classique sur colis
        Route::post('/store-aft-louis-b', [AftlbColisController::class, 'store'])->name('store'); 
        Route::get('/{coli}-aft-louis-b', [AftlbColisController::class, 'show'])->name('show'); 
        Route::get('/{coli}/edit-aft-louis-b', [AftlbColisController::class, 'edit'])->name('edit'); 
        Route::put('/{coli}-aft-louis-b', [AftlbColisController::class, 'update'])->name('update'); 
        Route::delete('/{coli}-aft-louis-b', [AftlbColisController::class, 'destroy'])->name('destroy');

        // Enregistrement des expéditeurs et destinataires
        Route::post('/store-expediteur-aft-louis-b', [AftlbColisController::class, 'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire-aft-louis-b', [AftlbColisController::class, 'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs-aft-louis-b', [AftlbColisController::class, 'search'])->name('search.expediteurs');

        // Création et stockage d'un colis
        Route::get('/create/colis-aft-louis-b', [AftlbColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis-aft-louis-b', [AftlbColisController::class, 'store_colis'])->name('store.colis');

        // Gestion du paiement et génération de QR code
        Route::get('/create/payement-aft-louis-b', [AftlbColisController::class, 'stepPayment'])->name('create.payement');
        Route::post('/store/payment-aft-louis-b', [AftlbColisController::class, 'storePayment'])->name('store.payment');
        Route::get('/generer/qrcode-aft-louis-b', [AftlbColisController::class, 'generer_qrcode'])->name('generer.qrcode');
    });

    // Groupe de routes pour la gestion du scan
    Route::prefix('aftlb_scan')->name('aftlb_scan.')->group(function(){
        Route::get('/en-entrepot-aft-louis-b', [AftlbScanController::class, 'entrepot'])->name('entrepot'); 
        Route::get('/en-chargement-aft-louis-b', [AftlbScanController::class, 'chargement'])->name('chargement'); 
        Route::get('/en-dechargement-aft-louis-b', [AftlbScanController::class, 'dechargement'])->name('dechargement'); 
        Route::get('/get-colis-entrepot-aft-louis-b', [AftlbScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement-aft-louis-b', [AftlbScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-chargement-aft-louis-b', [AftlbScanController::class, 'get_colis_charge'])->name('get.colis.charge');
        Route::post('/update-colis-status/entrepot-aft-louis-b', [AftlbScanController::class, 'updateColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge-aft-louis-b', [AftlbScanController::class, 'updateColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge-aft-louis-b', [AftlbScanController::class, 'updateColisDecharge'])->name('update.colis.decharge');

        Route::get('/chauffeur/data-aft-louis-b', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::match(['get', 'post-aft-louis-b'], '/store', [AgentTransportController::class, 'store'])->name('store');
    });

    Route::prefix('aftlb_transport')->name('aftlb_transport.')->group(function(){
        Route::get('/', [AgentLBTransportController::class, 'index'])->name('index'); 
        Route::get('/create-aft-louis-b', [AgentLBTransportController::class, 'create'])->name('create');
        Route::get('/show-chauffeur-aft-louis-b', [AgentLBTransportController::class, 'show_chauffeur'])->name('show.chauffeur');
        Route::get('/planing-chauffeur-aft-louis-b', [AgentLBTransportController::class, 'planing_chauffeur'])->name('planing.chauffeur');
        Route::get('/reference.auto/{query}-aft-louis-b', [AgentLBTransportController::class, 'reference_auto'])->name('reference.auto');
        Route::get('/chauffeur/data-aft-louis-b', [AgentLBTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::post('/store-chauffeur-aft-louis-b', [AgentLBTransportController::class, 'store_chauffeur'])->name('store.chauffeur'); 
        Route::post('/store-planification-aft-louis-b', [AgentLBTransportController::class, 'store_plannification'])->name('store.plannification'); 
        Route::match(['get', 'post'], '/store', [AgentLBTransportController::class, 'store'])->name('store'); 

        // Route pour afficher le formulaire de modification d'un chauffeur
        Route::get('/chauffeurs/{id}/edit', [AgentLBTransportController::class, 'edit'])->name('edit');

        // Route pour mettre à jour un chauffeur (méthode PUT)
        Route::put('/chauffeurs/{id}', [AgentLBTransportController::class, 'update'])->name('update');

         // Route pour supprimer un chauffeur (méthode DELETE)
        Route::delete('/chauffeurs/{id}', [AgentLBTransportController::class, 'destroy'])->name('destroy');
    });

    
    Route::prefix('programmelb')->name('lb_programme.')->group(function () {
        Route::get('/transport', [ProgrammeLBController::class, 'index'])->name('planing.index');
        Route::post('/chauffeur/store', [ProgrammeLBController::class, 'storeChauffeur'])->name('chauffeur.store');
        Route::post('/store', [ProgrammeLBController::class, 'storeProgramme'])->name('store');
        Route::get('/data', [ProgrammeLBController::class, 'data'])->name('data');
        Route::get('/edit/{programme}', [ProgrammeLBController::class, 'edit'])->name('edit');
        Route::put('/update/{programme}', [ProgrammeLBController::class, 'update'])->name('update');
        Route::delete('/delete/{programme}', [ProgrammeLBController::class, 'destroy'])->name('delete');
    });
    Route::prefix('rdvlb')->name('lb_rdv.')->group(function(){
        Route::get('/rdv', [Rdvlbcontroller::class, 'index'])->name('rdv.index');
        Route::get('/rdv/depot/data', [Rdvlbcontroller::class, 'depotData'])->name('rdv.depot.data');
        Route::get('/rdv/recuperation/data', [Rdvlbcontroller::class, 'recuperationData'])->name('rdv.recuperation.data');
    });
   

    // Groupe de routes pour les notifications de l'agent
    Route::prefix('aftlb_notification')->name('aftlb_notification.')->group(function() {
        Route::get('/', [NavAftlbController::class, 'index'])->name('index');
        Route::get('/get-notifications-aftlb', [NavAftlbController::class, 'get_notifications'])->name('get.notifications');
        Route::post('/notification-markAsRead-aftlb', [NavAftlbController::class, 'markAsRead'])->name('markAsRead');
    });
    
    Route::prefix('aftlb_invoice')->name('aftlb_invoice.')->group(function(){
        Route::get('/', [AftlbInvoiceController::class, 'index'])->name('index'); 
        Route::get('/create/new/invoice-aft-louis-b', [AftlbInvoiceController::class, 'create_invoice'])->name('create.invoice');
        Route::post('/store/new/invoice-aft-louis-b', [AftlbInvoiceController::class, 'store_invoice'])->name('store.invoice');
        Route::get('/history/invoice-aft-louis-b', [AftlbInvoiceController::class, 'historique_invoice'])->name('historique.invoice');
        Route::get('/edit/invoice-aft-louis-b', [AftlbInvoiceController::class, 'edit_invoice'])->name('edit.invoice');
        Route::get('/show/invoice-aft-louis-b', [AftlbInvoiceController::class, 'reference_auto'])->name('show.invoice');
        Route::get('/reference.auto/{query}-aft-louis-b', [AftlbInvoiceController::class, 'reference_auto'])->name('reference.auto');
        });
});


Route::prefix('IPMS_SIMEXCI')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'IPMS_SIMEXCI_INDEX'])->name('IPMS_SIMEXCI.dashboard');
    Route::get('/colis-ipms/count', function () {
        $colisCount = Colis::where('etat', 'Validé')
                            ->whereHas('expediteur', function ($query) {
                                $query->where('agence', 'IPMS-SIMEX-CI');
                            })
                            ->count();
        return response()->json(['colisCount' => $colisCount]);
    })->name('ipms_colis.count');

    Route::get('/colis-ipms/prix-total', function () {
        $totalPrixTransit = Colis::where('etat', 'Validé')
            ->whereHas('destinataire', function ($query) {
                $query->where('agence', 'IPMS-SIMEX-CI');
            })
            ->sum('prix_transit_colis');

        return response()->json(['totalPrixTransit' => $totalPrixTransit]);
    })->name('ipms_colis.prix-total');


    Route::get('/colis-ipms/vol-cargaison-count', function () {
        $count = Colis::where('mode_transit', 'aérien')
            ->whereHas('destinataire', function ($query) {
                $query->where('agence', 'IPMS-SIMEX-CI');
            })
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();

        return response()->json(['count' => $count]);
    })->name('ipms_colis.vol-cargaison-count');

    Route::get('/colis-ipms/conteneur-count', function () {
        $count = Colis::where('mode_transit', 'maritime')
            ->whereHas('destinataire', function ($query) {
                $query->where('agence', 'IPMS-SIMEX-CI');
            })
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();

        return response()->json(['count' => $count]);
    })->name('ipms_colis.conteneur-count');

    Route::get('/colis-ipms/valides-par-mois', function () {
        $currentYear = now()->year;

        $colisParMois = Colis::select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->where('etat', 'Validé')
            ->whereHas('destinataire', function ($query) {
                $query->where('agence', 'IPMS-SIMEX-CI');
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->pluck('total', 'mois');

        // Initialiser un tableau avec 12 zéros pour chaque mois
        $data = array_fill(1, 12, 0);

        // Remplir le tableau avec les données récupérées
        foreach ($colisParMois as $mois => $total) {
            $data[$mois] = $total;
        }

        return response()->json(array_values($data));
    })->name('ipms_colis.valides-par-mois');;
    // Groupe de routes pour les opérations sur les colis
    Route::prefix('ipms_colis')->name('ipms_colis.')->group(function(){
        Route::get('/', [ApmsColisController::class, 'index'])->name('index'); 
        Route::get('/on-dump-simexci', [ApmsColisController::class, 'dump'])->name('dump'); 
        Route::get('/on-suivi-simexci', [ApmsColisController::class, 'suivi'])->name('suivi'); 
        Route::get('/get-colis-hold-simexci', [ApmsColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/get-colis-dump/simexci', [ApmsColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-colis-suivi-simexci', [ApmsColisController::class, 'get_colis_suivi'])->name('get.colis.suivi');
       
        Route::get('/create/colis-simexci', [ApmsColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis-simexci', [ApmsColisController::class, 'store_colis'])->name('store.colis');
     
        // Routes d'édition et mise à jour
        Route::get('/on-hold/{id}/edit', [ApmsColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit', [ApmsColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}', [ApmsColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}', [ApmsColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print', [ApmsColisController::class, 'print_facture'])->name('facture.colis.print');
  
        // Suppression d'un colis validé
        Route::delete('/colis/{id}', [ApmsColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');

        // CRUD classique sur colis
        Route::post('/store', [ApmsColisController::class, 'store'])->name('store'); 
        Route::get('/{coli}', [ApmsColisController::class, 'show'])->name('show'); 
        Route::get('/{coli}/edit', [ApmsColisController::class, 'edit'])->name('edit'); 
        Route::put('/{coli}', [ApmsColisController::class, 'update'])->name('update'); 
        Route::delete('/{coli}', [ApmsColisController::class, 'destroy'])->name('destroy');

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
            Route::post('/update-colis-status/livre-simexci', [ApmsScanController::class, 'updateColisLivre'])->name('update.colis.livre');
            Route::get('/en-livre-simexci', [ApmsScanController::class, 'livre'])->name('livre');
            Route::get('/get-colis-livre-simexci', [ApmsScanController::class, 'get_colis_livre'])->name('get.colis.livre');
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
        Route::prefix('ipms_invoice')->name('ipms_invoice.')->group(function(){
            Route::get('/', [IpmsInvoiceController::class, 'index'])->name('index'); 
            Route::get('/create/new/invoice-aft--ipms', [IpmsInvoiceController::class, 'create_invoice'])->name('create.invoice');
            Route::post('/store/new/invoice-aft-ipms', [IpmsInvoiceController::class, 'store_invoice'])->name('store.invoice');
            Route::get('/history/invoice-aft-ipms', [IpmsInvoiceController::class, 'historique_invoice'])->name('historique.invoice');
            Route::get('/edit/invoice-aft-ipms', [IpmsInvoiceController::class, 'edit_invoice'])->name('edit.invoice');
            Route::get('/show/invoice-aft-ipms', [IpmsInvoiceController::class, 'reference_auto'])->name('show.invoice');
            Route::get('/reference.auto/{query}-ipms', [IpmsInvoiceController::class, 'reference_auto'])->name('reference.auto');
            });
});


Route::prefix('IPMS_SIMEXCI_ANGRE')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'IPMS_SIMEXCI_ANGRE_INDEX'])->name('IPMS_SIMEXCI_ANGRE.dashboard');
    Route::get('/colis-angre/count', function () {
        $colisCount = Colis::where('etat', 'Validé')
                            ->whereHas('destinataire', function ($query) {
                                $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                            })
                            ->count();
        return response()->json(['colisCount' => $colisCount]);
    })->name('ipms_angre_colis.count');
    
    Route::get('/colis/prix-total', function () {
        $totalPrixTransit = Colis::where('etat', 'Validé')
                ->whereHas('destinataire', function ($query) {
                    $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                })
            ->sum('prix_transit_colis');
        return response()->json(['totalPrixTransit' => $totalPrixTransit]);
    })->name('ipms_angre_colis.prix-total');
    
    
    Route::get('/colis-angre/vol-cargaison-count', function () {
        $count = Colis::where('mode_transit', 'aérien')
        ->whereHas('destinataire', function ($query) {
            $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
        })
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();
    
        return response()->json(['count' => $count]);
    })->name('ipms_angre_colis.vol-cargaison-count');
    
    Route::get('/colis-angre/conteneur-count', function () {
        $count = Colis::where('mode_transit', 'maritime')
        ->whereHas('destinataire', function ($query) {
            $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
        })
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count();
    
        return response()->json(['count' => $count]);
    })->name('ipms_angre_colis.conteneur-count');
    
    Route::get('/colis-angre/valides-par-mois', function () {
        $currentYear = now()->year;
    
        $colisParMois = Colis::select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->where('etat', 'Dechargé')
            ->whereHas('destinataire', function ($query) {
                $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->pluck('total', 'mois');
    
        // Initialiser un tableau avec 12 zéros pour chaque mois
        $data = array_fill(1, 12, 0);
    
        // Remplir le tableau avec les données récupérées
        foreach ($colisParMois as $mois => $total) {
            $data[$mois] = $total;
        }
    
        return response()->json(array_values($data));
    })->name('ipms_angre_colis.valides-par-mois');;
    // Groupe de routes pour les opérations sur les colis
    Route::prefix('ipms_angre_colis')->name('ipms_angre_colis.')->group(function(){
        Route::get('/', [ApmsAngreColisController::class, 'index'])->name('index'); 
        Route::get('/history-IPMS', [ApmsAngreColisController::class, 'history'])->name('history'); 
        Route::get('/on-hold-IPMS', [ApmsAngreColisController::class, 'hold'])->name('hold');
        Route::get('/on-dump-IPMS', [ApmsAngreColisController::class, 'dump'])->name('dump'); 
        Route::get('/on-suivi-IPMS', [ApmsAngreColisController::class, 'suivi'])->name('suivi'); 
        Route::get('/devis-hold-IPMS', [ApmsAngreColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-colis-dump-IPMS', [ApmsAngreColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-colis-suivi-IPMS', [ApmsAngreColisController::class, 'get_colis_suivi'])->name('get.colis.suivi');
        Route::get('/get-devis-colis-IPMS', [ApmsAngreColisController::class, 'get_devis_colis'])->name('get.devis.colis');
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
        Route::get('/devis/{id}/edit-IPMS', [ApmsAngreColisController::class, 'edit_qrcode'])->name('qrcode.edit');
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
        Route::get('/en-livre-IPMS', [ApmsAngreScanController::class, 'livre'])->name('livre'); 
        Route::get('/get-colis-entrepot-IPMS', [ApmsAngreScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement-IPMS', [ApmsAngreScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-livre-IPMS', [ApmsAngreScanController::class, 'get_colis_livre'])->name('get.colis.livre');
        Route::get('/get-colis-chargement-IPMS', [ApmsAngreScanController::class, 'get_colis_charge'])->name('get.colis.charge');
        Route::post('/update-colis-status/entrepot-IPMS', [ApmsAngreScanController::class, 'updateColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge-IPMS', [ApmsAngreScanController::class, 'updateColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge-IPMS', [ApmsAngreScanController::class, 'updateColisDecharge'])->name('update.colis.decharge');
        Route::post('/update-colis-status/livre-IPMS', [ApmsAngreScanController::class, 'updateColisLivre'])->name('update.colis.livre');

        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store');
    });

    // Groupe de routes pour la gestion du transport
    Route::prefix('IPMSANGRE_transport')->name('IPMSANGRE_transport.')->group(function () {
        Route::get('/', [AgentIPMSANGRETransportController::class, 'index'])->name('index');
        Route::get('/create', [AgentIPMSANGRETransportController::class, 'create'])->name('create');
        Route::get('/chauffeurs', [AgentIPMSANGRETransportController::class, 'show_chauffeur'])->name('chauffeurs.show');
        Route::get('/planification', [AgentIPMSANGRETransportController::class, 'planing_chauffeur'])->name('planification.show');
    
        // Autocomplete
        Route::get('/reference_auto/{query}', [AgentIPMSANGRETransportController::class, 'reference_auto'])->name('reference.auto');
        Route::get('/chauffeurs/{id}/edit', [AgentIPMSANGRETransportController::class, 'edit'])->name('chauffeurs.edit');
    
        // Route pour mettre à jour un chauffeur (méthode PUT)
        Route::put('/chauffeurs/{id}', [AgentIPMSANGRETransportController::class, 'update'])->name('chauffeurs.update');
        Route::delete('/chauffeurs/{id}', [AgentIPMSANGRETransportController::class, 'destroy'])->name('chauffeurs.destroy');
        // DataTables
        Route::get('/chauffeurs/data', [AgentIPMSANGRETransportController::class, 'get_chauffeur_list'])->name('chauffeurs.data');
    
        // CRUD Chauffeurs
        Route::post('/chauffeurs', [AgentIPMSANGRETransportController::class, 'store_chauffeur'])->name('chauffeurs.store');
        // Route::get('/rdv', [RdvController::class, 'index'])->name('rdv.index');
        // Route::get('/rdv/depot/data', [RdvController::class, 'depotData'])->name('rdv.depot.data');
        // Route::get('/rdv/recuperation/data', [RdvController::class, 'recuperationData'])->name('rdv.recuperation.data');
        // Plannification
        Route::post('/planification', [AgentIPMSANGRETransportController::class, 'store_plannification'])->name('planification.store');
        Route::match(['get', 'post'], '/store', [AgentIPMSANGRETransportController::class, 'store'])->name('store');
    });

    Route::prefix('programmeipmxangre')->name('ipmxangre_programme.')->group(function () {
        Route::get('/transport', [ProgrammeIPMXANGREController::class, 'index'])->name('planing.index');
        Route::post('/chauffeur/store', [ProgrammeIPMXANGREController::class, 'storeChauffeur'])->name('chauffeur.store');
        Route::post('/store', [ProgrammeIPMXANGREController::class, 'storeProgramme'])->name('store');
        Route::get('/data', [ProgrammeIPMXANGREController::class, 'data'])->name('data');
        Route::get('/edit/{programme}', [ProgrammeIPMXANGREController::class, 'edit'])->name('edit');
        Route::put('/update/{programme}', [ProgrammeIPMXANGREController::class, 'update'])->name('update');
        Route::delete('/delete/{programme}', [ProgrammeIPMXANGREController::class, 'destroy'])->name('delete');
    });
    Route::prefix('rdvipmx')->name('ipmx_rdv.')->group(function(){
        Route::get('/rdv', [RdvipmxangreController::class, 'index'])->name('rdv.index');
        Route::get('/rdv/depot/data', [RdvipmxangreController::class, 'depotData'])->name('rdv.depot.data');
        Route::get('/rdv/recuperation/data', [RdvipmxangreController::class, 'recuperationData'])->name('rdv.recuperation.data');
    });
    
    
    // Groupe de routes pour les notifications de l'agent
    Route::prefix('agent_notification')->name('agent_notification.')->group(function(){
        Route::get('/', [NavAdminController::class, 'index'])->name('index');
        Route::get('/get-notifications', [NavAdminController::class, 'get_notifications'])->name('get.notifications');
        Route::post('/notification-markAsRead', [NavAdminController::class, 'markAsRead'])->name('markAsRead');
    });
    Route::prefix('ipms_angre_invoice')->name('ipms_angre_invoice.')->group(function(){
        Route::get('/', [IpmsAngreInvoiceController::class, 'index'])->name('index'); 
        Route::get('/create/new/invoice-ipms-angre', [IpmsAngreInvoiceController::class, 'create_invoice'])->name('create.invoice');
        Route::post('/store/new/invoice-aft-ipms-angre', [IpmsAngreInvoiceController::class, 'store_invoice'])->name('store.invoice');
        Route::get('/edit/invoice-ipms-angre', [IpmsAngreInvoiceController::class, 'edit_invoice'])->name('edit.invoice');
        });
});


Route::prefix('AGENCE_CHINE')->middleware(['auth', 'role:agent'])->group(function () {
    // Route principale pour l'interface de l'agence AFT Agence Louis Bleriot
    Route::get('/', [AgentController::class, 'AGENCE_CHINE_INDEX'])->name('AGENCE_CHINE.dashboard');

    Route::get('/colis/count', function () {
        $colisCount = Colis::where('etat', 'Validé')
                            ->whereHas('expediteur', function ($query) {
                                $query->where('agence', 'Agence de Chine');
                            })
                            ->count();
        return response()->json(['colisCount' => $colisCount]);
    })->name('chine_colis.count');
    
    Route::get('/colis/prix-total', function () {
        $totalPrixTransit = Colis::where('etat', 'Validé')
            ->whereHas('expediteur', function ($query) {
                $query->where('agence', 'Agence de Chine');
            })
            ->sum('prix_transit_colis');
    
        return response()->json(['totalPrixTransit' => $totalPrixTransit]);
    })->name('chine_colis.prix-total');
    
    
    Route::get('/colis/vol-cargaison-count', function () {
        $count = Colis::where('mode_transit', 'aérien')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->whereHas('expediteur', function ($query) {
                $query->where('agence', 'Agence de Chine');
            })
            ->count();
    
        return response()->json(['count' => $count]);
    })->name('chine_colis.vol-cargaison-count');
    
    Route::get('/colis/conteneur-count', function () {
        $count = Colis::where('mode_transit', 'maritime')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->whereHas('expediteur', function ($query) {
                $query->where('agence', 'AFT Agence Louis Bleriot');
            })
            ->count();
    
        return response()->json(['count' => $count]);
    })->name('chine_colis.conteneur-count');
    
    Route::get('/colis/valides-par-mois', function () {
        $currentYear = now()->year;
    
        $colisParMois = Colis::select(
                    DB::raw('MONTH(created_at) as mois'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('created_at', $currentYear)
                ->where('etat', 'Validé')
                ->whereHas('expediteur', function ($query) {
                    $query->where('nom', 'Agence de Chine');
                })
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get()
                ->pluck('total', 'mois');    
        })->name('chine_colis.valides-par-mois');
    // Groupe de routes pour les opérations sur les colis
    Route::prefix('chine_colis')->name('chine_colis.')->group(function(){
        Route::get('/', [ChineColisController::class, 'index'])->name('index'); 
        Route::get('/on-hold-aft_chine', [ChineColisController::class, 'hold'])->name('hold'); 
        Route::get('/history-aft_chine', [ChineColisController::class, 'history'])->name('history'); 
        Route::get('/on-dump-aft_chine', [ChineColisController::class, 'dump'])->name('dump'); 
        Route::get('/create-aft_chine', [ChineColisController::class, 'create'])->name('create'); 
        Route::get('/get-colis-aft_chine', [ChineColisController::class, 'get_colis'])->name('getColis');
        Route::get('/get-colis-hold-aft_chine', [ChineColisController::class, 'get_colis_hold'])->name('get.colis.hold');
        Route::get('/get-colis-dump-aft_chine', [ChineColisController::class, 'get_colis_dump'])->name('get.colis.dump');
        Route::get('/get-colis-contenaire-aft_chine', [ChineColisController::class, 'get_colis_contenaire'])->name('get.colis.contenaire');
        Route::get('/devis-hold-aft_chine', [ChineColisController::class, 'devis_hold'])->name('devis.hold');
        Route::get('/get-devis-colis-aft_chine', [ChineColisController::class, 'get_devis_colis'])->name('get.devis.colis');
        Route::get('/devis/{id}/edit-aft_chine', [ChineColisController::class, 'edit_qrcode'])->name('qrcode.edit');
        Route::get('/colis-valide-aft_chine', [ChineColisController::class, 'colis_valide'])->name('colis.valide');
        Route::get('/get-colis-valide-aft_chine', [ChineColisController::class, 'get_colis_valide'])->name('get.colis.valide');

        // Routes pour les cargaisons
        Route::get('/get-vol-colis-aft_chine', [ChineColisController::class, 'get_colis_vol'])->name('get.colis.vol');
        Route::get('/cargaison-ferme-aft_chine', [ChineColisController::class, 'cargaison_ferme'])->name('cargaison.ferme');
        Route::get('/get-cargaison-ferme-aft_chine', [ChineColisController::class, 'get_cargaison_ferme'])->name('get.cargaison.ferme');
        Route::get('/list-vol-aft_chine', [ChineColisController::class, 'liste_vol'])->name('liste.vol');

        // Routes d'édition et mise à jour
        Route::get('/on-hold/{id}/edit-aft_chine', [ChineColisController::class, 'edit_hold'])->name('hold.edit');
        Route::get('/on-valide/{id}/edit-aft_chine', [ChineColisController::class, 'edit_colis_valide'])->name('valide.edit');
        Route::put('/on-hold/{id}-aft_chine', [ChineColisController::class, 'update_hold'])->name('hold.update');
        Route::put('/on-valide/{id}-aft_chine', [ChineColisController::class, 'update_colis_valide'])->name('valide.update');
        Route::get('/colis-facture/{id}/print-aft_chine', [ChineColisController::class, 'print_facture'])->name('facture.colis.print');

        // Route pour fermer un contenaire
        Route::post('/contenaire-fermer-aft_chine', [ChineColisController::class, 'contenaire_fermer'])->name('contenaire.fermer');
        
        // Liste des conteneurs
        Route::get('/list-contenaire-aft_chine', [ChineColisController::class, 'liste_contenaire'])->name('liste.contenaire');
        
        // Suppression d'un colis validé
        Route::delete('/colis/{id}-aft_chine', [ChineColisController::class, 'destroy_colis_valide'])->name('destroy.colis.valide');

        // CRUD classique sur colis
        Route::post('/store-aft_chine', [ChineColisController::class, 'store'])->name('store'); 
        Route::get('/{coli}-aft_chine', [ChineColisController::class, 'show'])->name('show'); 
        Route::get('/{coli}/edit-aft_chine', [ChineColisController::class, 'edit'])->name('edit'); 
        Route::put('/{coli}-aft_chine', [ChineColisController::class, 'update'])->name('update'); 
        Route::delete('/{coli}-aft_chine', [ChineColisController::class, 'destroy'])->name('destroy');

        // Enregistrement des expéditeurs et destinataires
        Route::post('/store-expediteur-aft_chine', [ChineColisController::class, 'store_expediteur'])->name('store.expediteur'); 
        Route::post('/store-destinataire-aft_chine', [ChineColisController::class, 'store_destinataire'])->name('store.destinataire'); 
        Route::get('/search-expediteurs-aft_chine', [ChineColisController::class, 'search'])->name('search.expediteurs');

        // Création et stockage d'un colis
        Route::get('/create/colis-aft_chine', [ChineColisController::class, 'add_colis'])->name('create.colis');
        Route::post('/store/colis-aft_chine', [ChineColisController::class, 'store_colis'])->name('store.colis');

        // Gestion du paiement et génération de QR code
        Route::get('/create/payement-aft_chine', [ChineColisController::class, 'stepPayment'])->name('create.payement');
        Route::post('/store/payment-aft_chine', [ChineColisController::class, 'storePayment'])->name('store.payment');
        Route::get('/generer/qrcode-aft_chine', [ChineColisController::class, 'generer_qrcode'])->name('generer.qrcode');
    });

    // Groupe de routes pour la gestion du scan
    Route::prefix('chine_scan')->name('chine_scan.')->group(function(){
        Route::get('/en-entrepot-aft_chine', [ChineScanController::class, 'entrepot'])->name('entrepot'); 
        Route::get('/en-chargement-aft_chine', [ChineScanController::class, 'chargement'])->name('chargement'); 
        Route::get('/en-dechargement-aft_chine', [ChineScanController::class, 'dechargement'])->name('dechargement'); 
        Route::get('/get-colis-entrepot-aft_chine', [ChineScanController::class, 'get_colis_entrepot'])->name('get.colis.entrepot');
        Route::get('/get-colis-dechargement-aft_chine', [ChineScanController::class, 'get_colis_decharge'])->name('get.colis.decharge');
        Route::get('/get-colis-chargement-aft_chine', [ChineScanController::class, 'get_colis_charge'])->name('get.colis.charge');
        Route::post('/update-colis-status/entrepot-aft_chine', [ChineScanController::class, 'updateColisEntrepot'])->name('update.colis.entrepot');
        Route::post('/update-colis-status/charge-aft_chine', [ChineScanController::class, 'updateColisCharge'])->name('update.colis.charge');
        Route::post('/update-colis-status/decharge-aft_chine', [ChineScanController::class, 'updateColisDecharge'])->name('update.colis.decharge');

        Route::get('/chauffeur/data', [AgentTransportController::class, 'get_chauffeur_list'])->name('get.chauffeur.list');
        Route::match(['get', 'post'], '/store', [AgentTransportController::class, 'store'])->name('store');
    });

   // Groupe de routes pour la gestion du transport de l'agence Chine
Route::prefix('chine_transport')->name('chine_transport.')->group(function () {
    Route::get('/', [AgentChineTransportController::class, 'index'])->name('index');
    Route::get('/create', [AgentChineTransportController::class, 'create'])->name('create');
    Route::get('/chauffeurs', [AgentChineTransportController::class, 'show_chauffeur'])->name('chauffeurs.show');
    // Route::get('/planification', [AgentChineTransportController::class, 'planing_chauffeur'])->name('planification.show');

    // Autocomplete
    Route::get('/reference_auto/{query}', [AgentChineTransportController::class, 'reference_auto'])->name('reference.auto');
    Route::get('/chauffeurs/{id}/edit', [AgentChineTransportController::class, 'edit'])->name('chauffeurs.edit');

    // Route pour mettre à jour un chauffeur (méthode PUT)
    Route::put('/chauffeurs/{id}', [AgentChineTransportController::class, 'update'])->name('chauffeurs.update');
    Route::delete('/chauffeurs/{id}', [AgentChineTransportController::class, 'destroy'])->name('chauffeurs.destroy');
    // DataTables
    Route::get('/chauffeurs/data', [AgentChineTransportController::class, 'get_chauffeur_list'])->name('chauffeurs.data');

    // CRUD Chauffeurs
    Route::post('/chauffeurs', [AgentChineTransportController::class, 'store_chauffeur'])->name('chauffeurs.store');
    
    Route::post('/planification', [AgentChineTransportController::class, 'store_plannification'])->name('planification.store');
    Route::match(['get', 'post'], '/store', [AgentChineTransportController::class, 'store'])->name('store');
});
 Route::prefix('programmechine')->name('chine_programme.')->group(function () {
        Route::get('/transport', [ProgrammeChineController::class, 'index'])->name('planing.index');
        Route::post('/chauffeur/store', [ProgrammeChineController::class, 'storeChauffeur'])->name('chauffeur.store');
        Route::post('/store', [ProgrammeChineController::class, 'storeProgramme'])->name('store');
        Route::get('/data', [ProgrammeChineController::class, 'data'])->name('data');
        Route::get('/edit/{programme}', [ProgrammeChineController::class, 'edit'])->name('edit');
        Route::put('/update/{programme}', [ProgrammeChineController::class, 'update'])->name('update');
        Route::delete('/delete/{programme}', [ProgrammeChineController::class, 'destroy'])->name('delete');
    });

 // Ajout des routes pour le RDV
 Route::prefix('chinrdv')->name('rdv_chine.')->group(function(){
    Route::get('/rdv', [RdvchineController::class, 'index'])->name('rdv.index');
    Route::get('/rdv/depot/data', [RdvchineController::class, 'depotData'])->name('rdv.depot.data');
    Route::get('/rdv/recuperation/data', [RdvchineController::class, 'recuperationData'])->name('rdv.recuperation.data');
 });

 

    // Groupe de routes pour les notifications de l'agent
    Route::prefix('chine_notification')->name('chine_notification.')->group(function() {
        Route::get('/', [NavChineController::class, 'index'])->name('index');
        Route::get('/get-notifications-chine', [NavChineController::class, 'get_notifications'])->name('get.notifications');
        Route::post('/notification-markAsRead-chine', [NavChineController::class, 'markAsRead'])->name('markAsRead');
    });
    
    Route::prefix('chine_invoice')->name('chine_invoice.')->group(function(){
        Route::get('/', [ChineInvoiceController::class, 'index'])->name('index'); 
        Route::get('/create/new/invoice-chine', [ChineInvoiceController::class, 'create_invoice'])->name('create.invoice');
        Route::post('/store/new/invoice-chine', [ChineInvoiceController::class, 'store_invoice'])->name('store.invoice');
        Route::get('/history/invoice--chine', [ChineInvoiceController::class, 'historique_invoice'])->name('historique.invoice');
        Route::get('/edit/invoice-chine', [ChineInvoiceController::class, 'edit_invoice'])->name('edit.invoice');
        Route::get('/show/invoice-chine', [ChineInvoiceController::class, 'reference_auto'])->name('show.invoice');
        Route::get('/reference.auto/{query}-chine', [ChineInvoiceController::class, 'reference_auto'])->name('reference.auto');
        });
});









//ROUTE POUR ROLE CHAUFFEUR

Route::prefix('chauffeur')->middleware(['auth', 'role:chauffeur'])->group(function () {
    // Route::get('/login', [ChauffeurAuthController::class, 'index'])->name('chauffeur.login');
//    Route::post('/login', [ChauffeurAuthController::class, 'login'])->name('chauffeur.login.post');
   Route::get('/dashboard', [ChauffeurAuthController::class, 'dashboard'])->name('chauffeur.dashboard');
   Route::get('/programme-chauffeur', [ChauffeurColisController::class, 'index'])->name('chauffeur.programme.index');
   Route::patch('/chauffeur/{programme}/update-etat', [ChauffeurColisController::class, 'updateEtatRdv'])->name('chauffeur.programme.updateEtatRdv');
});
Route::get('/admin/colis/getColisInfo/{reference_colis}', [ColisController::class, 'getColisInfo']);