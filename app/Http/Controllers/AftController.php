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
use App\Services\InfobipSmsService;


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

  
    

    

    
    //  public function clients(Request $request)
    //  {
    //      $agenceName = 'AFT Agence Louis Bleriot';
     
    //      $clients = DB::table('expediteurs')
    //          ->select(
    //              'nom',
    //              'prenom',
    //              'tel',
    //              'email',
    //              DB::raw("'expediteur' as type"),
    //              'created_at'
    //          )
    //          ->where('agence', $agenceName) // Filtrer les expéditeurs par agence
    //          ->unionAll(
    //              DB::table('destinataires')
    //                  ->select(
    //                      'nom',
    //                      'prenom',
    //                      'tel',
    //                      'email',
    //                      DB::raw("'destinataire' as type"),
    //                      'created_at'
    //                  )
    //                  ->where('agence', $agenceName) // Filtrer les destinataires par agence
    //          )
    //          ->get();
     
    //      $groupedClients = $clients->groupBy(function ($client) {
    //          return $client->nom . '|' . $client->prenom . '|' . $client->tel . '|' . $client->email;
    //      });
     
    //      $uniqueClients = $groupedClients->map(function ($group) {
    //          $client = $group->first();
     
    //          $types = $group->pluck('type')->unique()->toArray();
     
    //          if (count($types) === 2) {
    //              $client->type_client = 'expediteur et destinataire';
    //          } else {
    //              $client->type_client = $types[0];
    //          }
     
    //          return $client;
    //      })->values();
     
    //      return view('AFT_LOUIS_BLERIOT.client.index', compact('uniqueClients'));
    //  }


    // public function edit($nom, $prenom, $tel, $email)
    // {
        
    //     $expediteur = Expediteur::where('nom', $nom)
    //                         ->where('prenom', $prenom)
    //                         ->where('tel', $tel)
    //                         ->where('email', $email)
    //                         ->first();

    //     $destinataire = Destinataire::where('nom', $nom)
    //                             ->where('prenom', $prenom)
    //                             ->where('tel', $tel)
    //                             ->where('email', $email)
    //                             ->first();

    //     $client = $expediteur ?? $destinataire; // Prend le premier trouvé

    //     if (!$client) {
    //         abort(404, 'Client non trouvé.'); // Gérer le cas où le client n'existe pas
    //     }

    //     return view('AFT_LOUIS_BLERIOT.client.edit', compact('client')); // Créer une vue "edit.blade.php"
    // }

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

//     public function update(Request $request, $nom, $prenom, $tel, $email)
// {
//      // Valider les données de la requête
//      $validatedData = $request->validate([
//         'nom' => 'required|string|max:255',
//         'prenom' => 'required|string|max:255',
//         'tel' => 'required|string|max:20',
//         'email' => 'required|email|max:255',
//     ]);
//         // Rechercher le client dans la base de données (expéditeurs ou destinataires)
//     // (Exemple simplifié - à adapter à votre logique de recherche)

//     $expediteur = Expediteur::where('nom', $nom)
//     ->where('prenom', $prenom)
//     ->where('tel', $tel)
//     ->where('email', $email)
//     ->first();

//     $destinataire = Destinataire::where('nom', $nom)
//     ->where('prenom', $prenom)
//     ->where('tel', $tel)
//     ->where('email', $email)
//     ->first();

//     $client = $expediteur ?? $destinataire; // Prend le premier trouvé

//     if (!$client) {
//     abort(404, 'Client non trouvé.'); // Gérer le cas où le client n'existe pas
//     }
//        // Mettre à jour les informations du client
//     $client->nom = $validatedData['nom'];
//     $client->prenom = $validatedData['prenom'];
//     $client->tel = $validatedData['tel'];
//     $client->email = $validatedData['email'];
//     $client->save();
//      // Rediriger vers la liste des clients avec un message de succès
//      return redirect()->route('aft_client.index')->with('success', 'Client mis à jour avec succès.');
// }

public function clients(Request $request)
{
    // Récupérer les expediteurs distincts avec leur user
    $activeUsers = Expediteur::where('agence', 'AFT Agence Louis Bleriot')
            ->distinct()
            ->with('user') // Assure-toi que la relation user existe dans le modèle Expediteur
            ->get(['user_id']);

    $clients = collect();

    foreach ($activeUsers as $expediteur) {
        $user = $expediteur->user; // relation User

        if (!$user) {
            continue; // au cas où un expediteur n'a pas de user lié
        }

        $clientData = [
            'id' => $user->id,
            'nom' => $user->last_name,   // à adapter selon tes colonnes
            'prenom' => $user->first_name,
            'tel' => $user->tel,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'is_active' => $user->is_active,
            'type' => '',
        ];

        $isExpediteur = $user->expediteur()->exists();
        $isDestinataire = $user->destinataire()->exists();

        if ($isExpediteur && $isDestinataire) {
            $clientData['type'] = 'expediteur et destinataire';
        } elseif ($isExpediteur) {
            $clientData['type'] = 'expediteur';
        } elseif ($isDestinataire) {
            $clientData['type'] = 'destinataire';
        } else {
            continue;
        }

        $clients->push((object) $clientData);
    }

    $uniqueClients = $clients->values();

    return view('AFT_LOUIS_BLERIOT.client.index', compact('uniqueClients'));
}
    public function edit($id)
    {
        $client = User::findOrFail($id);
        return view('AFT_LOUIS_BLERIOT.client.edit', compact('client'));
    }


    public function update(Request $request, $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            abort(404, 'Client non trouvé.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'required|string|max:20', // Assurez-vous du format de tel
            'email' => 'required|email|unique:users,email,' . $user->id,
            'adresse' => 'nullable|string|max:255',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'tel' => $request->tel,
            'email' => $request->email,
            'adresse' => $request->adresse,
            // Ne pas mettre à jour is_active ici, c'est pour la désactivation
        ]);

        // Si l'utilisateur est un expéditeur ou un destinataire, mettez à jour ces tables aussi
        if ($user->expediteur) {
            $user->expediteur->update([
                'nom' => $request->last_name,
                'prenom' => $request->first_name,
                'tel' => $request->tel,
                'email' => $request->email,
                // ... d'autres champs spécifiques à l'expediteur si nécessaire
            ]);
        }
        if ($user->destinataire) {
            $user->destinataire->update([
                'nom' => $request->last_name,
                'prenom' => $request->first_name,
                'tel' => $request->tel,
                'email' => $request->email,
                // ... d'autres champs spécifiques au destinataire si nécessaire
            ]);
        }

        return redirect()->route('aft_client.index')->with('success', 'Client mis à jour avec succès.');
    }


    public function deactivateAccount($id)
    {
        $client = User::findOrFail($id);
        $client->update(['is_active' => false]);
        return redirect()->route('aft_client.index')->with('success', 'Compte désactivé.');
    }

    public function toggleActivation($id)
    {
        $client = User::findOrFail($id);

        // Inverse l'état is_active
        $client->update(['is_active' => !$client->is_active]);

        $status = $client->is_active ? 'activé' : 'désactivé';

        return redirect()->route('aft_client.index')
                        ->with('success', "Compte {$status} avec succès.");
    }



    public function sendMessageToClient(Request $request, $tel,  InfobipSmsService $InfobipSmsService)
    {
        $request->validate(['message' => 'required|string|max:160']);

        $message = $request->input('message');
        $id = $tel;

        $phoneNumber = User::where('id', $id)->value('tel');
        // dd($phoneNumber);

        if ($InfobipSmsService->sendSms($phoneNumber, $message)) {
            return back()->with('success', 'Message envoyé au client.');
        } else {
            return back()->with('error', 'Échec de l\'envoi du message.');
        }
    }

    public function sendGlobalMessage(Request $request,  InfobipSmsService $InfobipSmsService)
    {
        $request->validate(['global_message' => 'required|string|max:160']);

        $message = $request->input('global_message');
        $successfulSends = 0;
        $failedSends = 0;

        // Récupérer les numéros de téléphone uniques de tous les utilisateurs actifs qui sont clients
        $phoneNumbers = User::where('is_active', true)
            ->where(function($query) {
                $query->whereHas('expediteur', function($q) {
                    $q->where('agence', 'AFT Agence Louis Bleriot');
                })
                ->orWhereHas('destinataire', function($q) {
                    $q->where('agence', 'AFT Agence Louis Bleriot');
                });
            })
            ->pluck('tel')
            ->unique()
            ->filter(function($number) {
                return preg_match('/^\+[1-9]\d{1,14}$/', $number);
            });


        foreach ($phoneNumbers as $phoneNumber) {
            if ($InfobipSmsService->sendSms($phoneNumber, $message)) {
                $successfulSends++;
            } else {
                $failedSends++;
            }
        }

        if ($successfulSends > 0) {
            $message = "Messages envoyés avec succès à {$successfulSends} clients.";
            if ($failedSends > 0) {
                $message .= " Échec pour {$failedSends} clients.";
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', "Aucun message n'a pu être envoyé. Échec pour {$failedSends} clients.");
        }
    }
}



