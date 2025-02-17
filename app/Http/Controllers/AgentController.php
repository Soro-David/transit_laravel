<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Agence;
use App\Models\Colis;
use App\Models\Expediteur;
// use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgentController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('role:agent');
    }

    public function index()
    {
        // dd(request());
        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();
        $products_count = Product::count();

        return view('agent.dashboard', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count,
            'products_count' => $products_count
        ]);
        return view('agent.dashboard');
    }


    public function AFT_LOUIS_BLERIOT_INDEX()
    {
        // dd(request());
        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();
        $products_count = Product::count();
        $colisCount = Colis::where('etat', 'Validé')
                    ->whereHas('expediteur', function($query) {
                        $query->where('agence', 'AFT Agence Louis Bleriot');
                    })
                    ->count();
        $colisPrix = Colis::where('etat', 'Validé')
                    ->whereHas('expediteur', function($query) {
                        $query->where('agence', 'AFT Agence Louis Bleriot');
                    })
                    ->count();


                    
        $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
                    ->whereHas('expediteur', function ($query) {
                        $query->where('agence', 'AFT Agence Louis Bleriot');
                    })
                    ->sum('prix_transit_colis');
        $volCargaisonCount = Colis::where('mode_transit', 'aérien')
                    ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                    ->whereHas('expediteur', function ($query) {
                        $query->where('agence', 'AFT Agence Louis Bleriot');
                    })
                    ->count();
                
        $conteneurCount = Colis::where('mode_transit', 'maritime')
                    ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                    ->whereHas('expediteur', function ($query) {
                        $query->where('agence', 'AFT Agence Louis Bleriot');
                    })
                    ->count();
                
        $currentYear = Carbon::now()->year;

        $colisParMois = Colis::select(
                        DB::raw('MONTH(created_at) as mois'),
                        DB::raw('COUNT(*) as total')
                    )->whereHas('expediteur', function ($query) {
                        $query->where('agence', 'AFT Agence Louis Bleriot');
                    })
                    ->whereYear('created_at', $currentYear)
                    ->groupBy(DB::raw('MONTH(created_at)'))
                    ->orderBy(DB::raw('MONTH(created_at)'))
                    ->pluck('total', 'mois')
                    ->toArray();

        $moisNoms = ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
            $colisData = [];
                       
            for ($i = 1; $i <= 12; $i++) {
                $colisData[] = $colisParMois[$i] ?? 0;
            }
            // dd($colisData);        

        return view('AFT_LOUIS_BLERIOT.dashboard', compact('orders','colisData', 'moisNoms','colisParMois', 'customers_count', 'products_count','colisCount','totalPrixTransit','volCargaisonCount','conteneurCount'));
    }

    public function IPMS_SIMEXCI_INDEX()
    {
        $orders = Order::with(['items', 'payments'])->get();
         $customers_count = Customer::count();
         $products_count = Product::count();
         $colisCount = Colis::where('etat', 'Validé')
                     ->whereHas('destinataire', function($query) {
                         $query->where('agence', 'IPMS-SIMEX-CI');
                     })
                     ->count();
         $colisPrix = Colis::where('etat', 'Validé')
                            ->whereHas('destinataire', function($query) {
                                $query->where('agence', 'IPMS-SIMEX-CI');
                            })
                            ->count();
 
 
                     
         $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
                            ->whereHas('destinataire', function($query) {
                                $query->where('agence', 'IPMS-SIMEX-CI');
                            })
                     ->sum('prix_transit_colis');
         $volCargaisonCount = Colis::where('mode_transit', 'aérien')
                     ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                     ->whereHas('destinataire', function($query) {
                        $query->where('agence', 'IPMS-SIMEX-CI');
                    })
                     ->count();
                 
         $conteneurCount = Colis::where('mode_transit', 'maritime')
                     ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                     ->whereHas('destinataire', function($query) {
                        $query->where('agence', 'IPMS-SIMEX-CI');
                    })
                     ->count();
                 
         $currentYear = Carbon::now()->year;
 
         $colisParMois = Colis::select(
                        DB::raw('MONTH(created_at) as mois'),
                        DB::raw('COUNT(*) as total')
                    )->whereHas('destinataire', function($query) {
                        $query->where('agence', 'IPMS-SIMEX-CI');
                    })
                    ->whereYear('created_at', $currentYear)
                    ->groupBy(DB::raw('MONTH(created_at)'))
                    ->orderBy(DB::raw('MONTH(created_at)'))
                    ->pluck('total', 'mois')
                    ->toArray();

         $moisNoms = ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
             $colisData = [];
                        
             for ($i = 1; $i <= 12; $i++) {
                 $colisData[] = $colisParMois[$i] ?? 0;
             }
             // dd($colisData);        
 
         return view('IPMS_SIMEXCI.dashboard', compact('orders','colisData', 'moisNoms','colisParMois', 'customers_count', 'products_count','colisCount','totalPrixTransit','volCargaisonCount','conteneurCount'));
     }


    public function IPMS_SIMEXCI_ANGRE_INDEX()
    {
        // dd(request());
         // dd(request());
         $orders = Order::with(['items', 'payments'])->get();
         $customers_count = Customer::count();
         $products_count = Product::count();
         $colisCount = Colis::where('etat', 'Validé')
                     ->whereHas('destinataire', function($query) {
                         $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                     })
                     ->count();
         $colisPrix = Colis::where('etat', 'Validé')
                            ->whereHas('destinataire', function($query) {
                                $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                            })
                            ->count();
 
 
                     
         $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
                            ->whereHas('destinataire', function($query) {
                                $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                            })
                     ->sum('prix_transit_colis');
         $volCargaisonCount = Colis::where('mode_transit', 'aérien')
                     ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                     ->whereHas('destinataire', function($query) {
                        $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                    })
                     ->count();
                 
         $conteneurCount = Colis::where('mode_transit', 'maritime')
                     ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                     ->whereHas('destinataire', function($query) {
                        $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                    })
                     ->count();
                 
         $currentYear = Carbon::now()->year;
 
         $colisParMois = Colis::select(
                        DB::raw('MONTH(created_at) as mois'),
                        DB::raw('COUNT(*) as total')
                    )->whereHas('destinataire', function($query) {
                        $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                    })
                    ->whereYear('created_at', $currentYear)
                    ->groupBy(DB::raw('MONTH(created_at)'))
                    ->orderBy(DB::raw('MONTH(created_at)'))
                    ->pluck('total', 'mois')
                    ->toArray();

         $moisNoms = ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
             $colisData = [];
                        
             for ($i = 1; $i <= 12; $i++) {
                 $colisData[] = $colisParMois[$i] ?? 0;
             }
             // dd($colisData);        
 
         return view('IPMS_SIMEXCI_ANGRE.dashboard', compact('orders','colisData', 'moisNoms','colisParMois', 'customers_count', 'products_count','colisCount','totalPrixTransit','volCargaisonCount','conteneurCount'));
     }

    public function AGENCE_CHINE_INDEX()
{
    // Fetch necessary data
    $orders = Order::with(['items', 'payments'])->get();
    $customers_count = Customer::count();
    $products_count = Product::count();
    $colisCount = Colis::where('etat', 'Validé')
                        ->whereHas('expediteur', function ($query) {
                            $query->where('agence', 'Agence de Chine');
                        })->count();

    $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
                             ->whereHas('expediteur', function ($query) {
                                 $query->where('agence', 'Agence de Chine');
                             })->sum('prix_transit_colis');

    $volCargaisonCount = Colis::where('mode_transit', 'aérien')
                              ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                              ->whereHas('expediteur', function ($query) {
                                  $query->where('agence', 'Agence de Chine');
                              })->count();

    $conteneurCount = Colis::where('mode_transit', 'maritime')
                           ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                           ->whereHas('expediteur', function ($query) {
                               $query->where('agence', 'Agence de Chine');
                           })->count();

    $currentYear = Carbon::now()->year;

    // Fetch data grouped by month
    $colisParMois = Colis::select(
                    DB::raw('MONTH(created_at) as mois'),
                    DB::raw('COUNT(*) as total')
                )->whereHas('expediteur', function ($query) {
                    $query->where('agence', 'Agence de Chine');})
                ->whereYear('created_at', $currentYear)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->pluck('total', 'mois')
                ->toArray();

    // Initialize an array for months with data
    $colisData = [];
    for ($i = 1; $i <= 12; $i++) {
        $colisData[] = $colisParMois[$i] ?? 0;
    }

    // Pass data to the view
    return view('AGENCE_CHINE.dashboard', compact(
        'orders', 'colisData', 'customers_count', 'products_count', 
        'colisCount', 'totalPrixTransit', 'volCargaisonCount', 'conteneurCount'
    ));
}

}
