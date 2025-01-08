<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;



class AdminController extends Controller
{
    //

    public function __construct()
{
    $this->middleware('role:admin');
}

public function index()
{
    // Récupérer les commandes avec les éléments et paiements associés
    $orders = Order::with(['items', 'payments'])->get();
    
    // Compter les clients et produits
    $customers_count = Customer::count();
    $products_count = Product::count();
    $agence = Agence::select('nom_agence')->first();
    // Créer un graphique
    $chart = new Chart;
    $chart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']);
    $chart->dataset('Revenue by Month', 'line', [10, 20, 30, 40, 50, 60])
        ->color('#ff0000')
        ->backgroundColor('rgba(255, 0, 0, 0.3)');

    // Calculer les revenus totaux et du jour
    $income = $orders->map(function($order) {
        return min($order->receivedAmount(), $order->total());
    })->sum();

    $income_today = $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')
        ->map(function($order) {
            return min($order->receivedAmount(), $order->total());
        })->sum();

    // Retourner la vue avec les données
    return view('admin.dashboard', [
        'orders_count' => $orders->count(),
        'income' => $income,
        'income_today' => $income_today,
        'customers_count' => $customers_count,
        'products_count' => $products_count,
        'chart' => $chart,
        'agence' => $agence,
          // Passer le graphique à la vue
    ]);
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



