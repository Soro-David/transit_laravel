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

        return view('admin.dashboard', [
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
    }

  
    public function gestion_agence()
    {
        return view('admin.gestion.agence');
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
        return view('admin.gestion.agent', compact('agences')); 
    }

    public function get_users(Request $request)
{
    if ($request->ajax()) {
        $users = User::select(['id', 'first_name', 'email', 'role', 'created_at']);
        return DataTables::of($users)
            ->addColumn('action', function ($row) {
                $editUrl = '/users/' . $row->id . '/edit';
                $deleteUrl = '/users/' . $row->id; // Route pour supprimer (à adapter)

                return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modify">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action']) // Permet de rendre le HTML
            ->make(true);
    }
}

}



