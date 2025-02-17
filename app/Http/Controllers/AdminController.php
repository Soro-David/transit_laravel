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
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Colis;
use App\Models\Expediteur;
// use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class AdminController extends Controller
{
    //

    public function __construct()
{
    $this->middleware('role:admin');
}

public function index()
{
    $orders = Order::with(['items', 'payments'])->get();
         $customers_count = Customer::count();
         $products_count = Product::count();
         $colisCount = Colis::where('etat', 'Validé')
                     ->count();
         $colisPrix = Colis::where('etat', 'Validé')
                            ->count();
 
 
                     
         $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
                     ->sum('prix_transit_colis');
         $volCargaisonCount = Colis::where('mode_transit', 'aérien')
                     ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                     ->count();
                 
         $conteneurCount = Colis::where('mode_transit', 'maritime')
                     ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                     ->count();
                 
                     $currentYear = now()->year;

        $colisParMois = Colis::select(
                            DB::raw('MONTH(created_at) as mois'),
                            DB::raw('COUNT(*) as total')
                        )
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
 
         return view('admin.dashboard', compact('orders','colisData', 'moisNoms','colisParMois', 'customers_count', 'products_count','colisCount','totalPrixTransit','volCargaisonCount','conteneurCount'));
     }

  
    public function gestion_agence()
    {
        return view('admin.gestion.agence.index');
    }


    public function store(userRequest $request)
    {
           
        User::create([
            'first_name' => $request->nom,
            'last_name' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'agence_id' => $request->agence_id,
        ]);
        return redirect()->back()->with('success', 'Gestionnaire ajouté avec succès !');
    }

    public function add_agent()
    {
        $agences = Agence::select('nom_agence', 'id')->get();
        return view('admin.gestion.agent.index', compact('agences')); 
    }

    public function get_users(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select(['id', 'first_name', 'email', 'role', 'created_at']);
            return DataTables::of($users)
                ->addColumn('action', function ($row) {
                    $editUrl = route('agence.agent.edit', ['id' => $row->id]);
                    $showUrl = route('agence.agent.show', ['id' => $row->id]);
                    $deleteUrl = route('agence.agent.destroy', ['id' => $row->id]);
                    return '
                        <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Edit" data-bs-target="#editModal">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modify" data-bs-target="#modifModal">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                                <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action']) 
                ->make(true);
        }
    }

}



