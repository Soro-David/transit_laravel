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
use App\Models\Destinataire;
use Carbon\Carbon;



class AftController extends Controller
{
    //

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
 
         return view('AFT_LOUIS_BLERIOT.dashboard', compact('orders','colisData', 'moisNoms','colisParMois', 'customers_count', 'products_count','colisCount','totalPrixTransit','volCargaisonCount','conteneurCount'));
     }

  
    

    

    
     public function clients(Request $request)
     {
         $agenceName = 'AFT Agence Louis Bleriot';
     
         $clients = DB::table('expediteurs')
             ->select(
                 'nom',
                 'prenom',
                 'tel',
                 'email',
                 DB::raw("'expediteur' as type"),
                 'created_at'
             )
             ->where('agence', $agenceName) // Filtrer les expéditeurs par agence
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
                     ->where('agence', $agenceName) // Filtrer les destinataires par agence
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
     
         return view('AFT_LOUIS_BLERIOT.client.index', compact('uniqueClients'));
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

    return view('AFT_LOUIS_BLERIOT.client.edit', compact('client')); // Créer une vue "edit.blade.php"
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
    return redirect()->route('aft_client.index')->with('success', 'Client supprimé avec succès.');
}


    public function show($type_client, $id)
    {
        if ($type_client === 'expediteur') {
            $client = Expediteur::findOrFail($id);
        } else {
            $client = Destinataire::findOrFail($id);
        }

        return view('AFT_LOUIS_BLERIOT.client.show', compact('client', 'type_client'));
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
     return redirect()->route('aft_client.index')->with('success', 'Client mis à jour avec succès.');
}
}



