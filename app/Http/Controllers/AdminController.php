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
use App\Models\Agent;
use App\Models\Agence;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Colis;
use App\Services\CurrencyConverterService;
use App\Models\Expediteur;
use App\Models\Destinataire;
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
 
 
                     
      // Remplacer l'ancien calcul de $totalPrixTransit par :
      $converter = new CurrencyConverterService();

      $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
          ->with('agent.agence')
          ->get()
          ->sum(function ($colis) use ($converter) {
              // 1) Conversion EUR → FCFA si nécessaire
              if ($colis->devise === 'EUR') {
                  return $converter->convertEurToCfa($colis->prix_transit_colis);
              }
  
              // 2) Ancienne logique basée sur le pays de l'agence (France)
              if (
                  $colis->agent &&
                  $colis->agent->agence &&
                  $colis->agent->agence->pays_agence === 'France'
              ) {
                  // ici on repart de FCFA → EUR ? 
                  // ou EUR → FCFA selon votre besoin initial : 
                  // je garde votre ancienne formule :
                  return $colis->prix_transit_colis * CurrencyConverterService::FCFA_TO_EUR_RATE;
              }
  
              // 3) Par défaut, on renvoie tel quel (on suppose déjà en FCFA)
              return $colis->prix_transit_colis;
          });
                    
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
    public function accueil()
    {
        return view('accueil');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Créer un utilisateur dans la table 'users'
        $user = User::create([
            'first_name' => $request->nom,
            'last_name' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'agence_id' => $request->agence_id,
        ]);
        // dd($user->id);
        // Créer un agent et lier au user créé juste avant
        Agent::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agence_id' => $request->agence_id,
            'user_id' => $user->id,
        ]);
    
        return redirect()->back()->with('success', 'Agent ajouté avec succès !');
    }

    public function add_agent()
    {
        $agences = Agence::select('nom_agence', 'id')->get();
        return view('admin.gestion.agent.index', compact('agences'));
    }


public function get_users(Request $request) // Renommer en get_agents serait plus cohérent
{
    if ($request->ajax()) {
        // 1. On charge la relation 'agence' avec with() pour optimiser la requête.
        // On sélectionne toutes les colonnes de la table 'agents' explicitement.
        $data = Agent::with('agence')->select('agents.*');

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                // Vos URLs de routes
                $editUrl = route('agence.agent.edit', $row->id);
                $showUrl = route('agence.agent.show', $row->id);
                $deleteUrl = route('agence.agent.destroy', $row->id);

                return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                    <button class="btn btn-sm btn-danger delete-btn" data-url="' . $deleteUrl . '"><i class="fas fa-trash"></i></button>
                ';
            })
            // 2. On ajoute une nouvelle colonne pour le nom de l'agence
            ->addColumn('agence', function ($agent) {
                // On utilise l'opérateur "nullsafe" (?->) pour éviter une erreur si un agent n'a pas d'agence
                return $agent->agence?->nom_agence ?? 'N/A';
            })
            ->rawColumns(['action']) // Indique à DataTables que la colonne 'action' contient du HTML
            ->make(true);
    }
}

    public function clients(Request $request)
    {
        $clients = DB::table('expediteurs')
            ->select(
                'nom',
                'prenom',
                'tel',
                'email',
                DB::raw("'expediteur' as type"),
                'created_at'
            )
            ->unionAll(
                DB::table('destinataires')
                    ->select(
                        'nom',
                        'prenom',
                        'tel',
                        'email',
                        DB::raw("'destinataire' as type"), 
                        'created_at'
                    )
            )
            ->get();
    
        $groupedClients = $clients->groupBy(function ($client) {
            return $client->nom . '|' . $client->prenom . '|' . $client->tel . '|' . $client->email;
        });
    
        $uniqueClients = $groupedClients->map(function ($group) {
            $client = $group->first(); 
    
            $types = $group->pluck('type')->unique()->toArray();
    
            if (count($types) === 2) {
                $client->type_client = 'expediteur et destinataire'; 
            } else {
                $client->type_client = $types[0]; 
            }
    
            return $client;
        })->values(); 
    
        return view('admin.client.index', compact('uniqueClients'));
    }


public function edit($nom, $prenom, $tel, $email)
{
    
    $expediteur = Expediteur::where('nom', $nom)
                         ->where('prenom', $prenom)
                         ->where('tel', $tel)
                         ->where('email', $email)
                         ->first();

    $destinataire = Destinataire::where('nom', $nom)
                             ->where('prenom', $prenom)
                             ->where('tel', $tel)
                             ->where('email', $email)
                             ->first();

    $client = $expediteur ?? $destinataire; // Prend le premier trouvé

    if (!$client) {
        abort(404, 'Client non trouvé.'); // Gérer le cas où le client n'existe pas
    }

    return view('admin.client.edit', compact('client')); // Créer une vue "edit.blade.php"
}

public function destroy($nom, $prenom, $tel, $email)
{
  
    $expediteur = Expediteur::where('nom', $nom)
                         ->where('prenom', $prenom)
                         ->where('tel', $tel)
                         ->where('email', $email)
                         ->first();

    $destinataire = Destinataire::where('nom', $nom)
                             ->where('prenom', $prenom)
                             ->where('tel', $tel)
                             ->where('email', $email)
                             ->first();

    $client = $expediteur ?? $destinataire; // Prend le premier trouvé

    if (!$client) {
        abort(404, 'Client non trouvé.'); // Gérer le cas où le client n'existe pas
    }

    // Supprimer le client de la base de données
    // (Déterminer si c'est un expéditeur ou un destinataire avant de supprimer)
    if ($client instanceof Expediteur) {
        $client->delete();
    } elseif ($client instanceof Destinataire) {
        $client->delete();
    }

    // Rediriger vers la liste des clients avec un message de succès
    return redirect()->route('client.index')->with('success', 'Client supprimé avec succès.');
}


    public function show($type_client, $id)
    {
        if ($type_client === 'expediteur') {
            $client = Expediteur::findOrFail($id);
        } else {
            $client = Destinataire::findOrFail($id);
        }

        return view('admin.client.show', compact('client', 'type_client'));
    }

    public function update(Request $request, $nom, $prenom, $tel, $email)
{
     // Valider les données de la requête
     $validatedData = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'tel' => 'required|string|max:20',
        'email' => 'required|email|max:255',
    ]);
        // Rechercher le client dans la base de données (expéditeurs ou destinataires)
    // (Exemple simplifié - à adapter à votre logique de recherche)

    $expediteur = Expediteur::where('nom', $nom)
    ->where('prenom', $prenom)
    ->where('tel', $tel)
    ->where('email', $email)
    ->first();

    $destinataire = Destinataire::where('nom', $nom)
    ->where('prenom', $prenom)
    ->where('tel', $tel)
    ->where('email', $email)
    ->first();

    $client = $expediteur ?? $destinataire; // Prend le premier trouvé

    if (!$client) {
    abort(404, 'Client non trouvé.'); // Gérer le cas où le client n'existe pas
    }
       // Mettre à jour les informations du client
    $client->nom = $validatedData['nom'];
    $client->prenom = $validatedData['prenom'];
    $client->tel = $validatedData['tel'];
    $client->email = $validatedData['email'];
    $client->save();
     // Rediriger vers la liste des clients avec un message de succès
     return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
}

public function getTotalPrixTransit()
{
    $converter = new CurrencyConverterService();

    $total = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
        ->with('agent.agence')
        ->get()
        ->sum(function ($colis) use ($converter) {
            if ($colis->devise === 'EUR') {
                return $converter->convertEurToCfa($colis->prix_transit_colis);
            }

            if (
                $colis->agent &&
                $colis->agent->agence &&
                $colis->agent->agence->pays_agence === 'France'
            ) {
                return $colis->prix_transit_colis * CurrencyConverterService::FCFA_TO_EUR_RATE;
            }

            return $colis->prix_transit_colis;
        });

    return response()->json(['totalPrixTransit' => $total]);
}
}



