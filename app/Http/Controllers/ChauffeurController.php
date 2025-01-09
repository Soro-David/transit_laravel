<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Agence;
use App\Models\Chauffeur;

class ChauffeurController extends Controller
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
    

    public function store(Request $request)
{
    // Valider les données du formulaire
    $request->validate([
        'nom_chauffeur' => 'required|string|max:255',
        'email_chauffeur' => 'required|email|max:255',
        'tel_chauffeur' => 'required|string|max:255',
        'agence_expedition' => 'required|exists:agences,id', // Assurez-vous que l'agence existe
    ]);

    // Créer un nouveau chauffeur
    Chauffeur::create([
        'nom' => $request->nom_chauffeur,
        'email' => $request->email_chauffeur,
        'tel' => $request->tel_chauffeur,
        'agence_id' => $request->agence_expedition,  // Assurez-vous que le champ est bien l'ID
    ]);

    // Rediriger avec un message de succès
    return redirect()->route('transport.index')->with('success', 'Chauffeur ajouté avec succès!');
}
    
}
