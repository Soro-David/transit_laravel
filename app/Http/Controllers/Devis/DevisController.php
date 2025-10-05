<?php

namespace App\Http\Controllers\Devis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Devis;
use App\Models\DevisItems;
use Yajra\DataTables\Facades\DataTables; 
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Les_colis;
use App\Models\Colis;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Paiement;
use App\Models\Article;
use App\Models\Invoice;
use App\Http\Requests\Devisrequest;

class DevisController extends Controller
{
    public function add_colis(Request $request)
    {
        $id = auth()->user()->getIdUSer();

        $user = User::findOrfail($id);

        $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->distinct()->pluck('pays_agence');

        $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
        $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();


        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        return view('customer.devis.create', compact(
            'agencesExpedition', 'agencesDestination', 'paysUniques',
            'client_expediteurs', 'client_destinataires','user'
        ));
    }

    public function store_colis(Devisrequest $request)
    {
        \Log::info('Données reçues:', $request->all());
    
        try {
            $devis = DB::transaction(function () use ($request) {
    
                // 1. On récupère les initiales à partir des données validées du formulaire
                $initialNom = mb_substr($request->nom_expediteur, 0, 1);
                $initialPrenom = mb_substr($request->prenom_expediteur, 0, 1);
                $initiales = strtoupper($initialNom . $initialPrenom);
    
                // 2. On génère un code unique en s'assurant qu'il n'existe pas déjà
                do {
                    $randomNumber = random_int(100000, 999999);
                    $reference = 'DV' . $randomNumber . $initiales;
                } while (Devis::where('reference', $reference)->exists());
    
                // Création du devis avec l'état 'en attente'
                $newDevis = Devis::create([
                    'reference' => $reference,
                    'mode_transit' => $request->mode_transit,
                    'pays_expedition' => $request->pays_expedition,
                    'agence_expedition' => $request->agence_expedition,
                    'agence_destination' => $request->agence_destination_societe, // Nom unifié
                    'nom_expediteur' => $request->nom_expediteur,
                    'prenom_expediteur' => $request->prenom_expediteur,
                    'email_expediteur' => $request->email_expediteur,
                    'tel_expediteur' => $request->tel_expediteur,
                    'adresse_expediteur' => $request->adresse_expediteur,
                    'devise' => $request->devise,
                    'user_id' => auth()->id(),
                    'etat' => 'en attente', // <-- Ajouté : état par défaut à l'enregistrement
                ]);
    
                // 2b. On boucle sur les colis envoyés par le formulaire pour les créer
                foreach ($request->service as $key => $service) {
                    $newDevis->items()->create([
                        'quantite_colis' => $request->quantite_colis[$key],
                        'service' => $service,
                        'valeur_colis' => $request->valeur_colis[$key] ?? null,
                        'type_colis' => $request->type_colis[$key],
                        'description_colis' => $request->description_colis[$key] ?? null,
                        'poids' => $request->poids[$key] ?? null,
                        'longueur' => $request->longueur[$key] ?? null,
                        'largeur' => $request->largeur[$key] ?? null,
                        'hauteur' => $request->hauteur[$key] ?? null,
                    ]);
                }
    
                return $newDevis; // On retourne le devis créé pour pouvoir l'utiliser après la transaction
            });
    
            return redirect()->route('customer_colis.hold')->with('success_popup', 'Devis créé avec succès !');
    
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du devis: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la soumission de votre devis. Veuillez réessayer.')->withInput();
        }
    }
    
    public function get_devis(Request $request)
    {
        if (! $request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
    
        $userId = auth()->id();
    
        // Permettre un filtre d'état optionnel via la requête, par défaut "En attente"
        $etatFilter = $request->get('etat', 'En attente');
        $etatFilterNormalized = mb_strtolower(trim($etatFilter), 'UTF-8');
    
        $query = Devis::select(
                'devis.id',
                'devis.reference',
                DB::raw('COUNT(devis_items.id) as nombre_items'),
                'devis.agence_expedition',
                DB::raw('CONCAT(devis.nom_expediteur, " ", devis.prenom_expediteur) as expediteur_nom_complet'),
                'devis.tel_expediteur',
                'devis.agence_destination',
                DB::raw('MAX(devis.updated_at) as last_updated_at'),
                'devis.etat'
            )
            ->leftJoin('devis_items', 'devis.id', '=', 'devis_items.devis_id')
            ->where('devis.user_id', $userId)
            // Filtrer par état (insensible à la casse). Par défaut "En attente".
            ->whereRaw('LOWER(devis.etat) = ?', [$etatFilterNormalized])
            ->groupBy(
                'devis.id',
                'devis.reference',
                'devis.agence_expedition',
                'devis.nom_expediteur',
                'devis.prenom_expediteur',
                'devis.tel_expediteur',
                'devis.agence_destination',
                'devis.etat'
            );
    
        return DataTables::of($query)
            // empêcher la recherche automatique problématique sur nombre_items (c'est un agrégat)
            ->filterColumn('nombre_items', function($query, $keyword) {
                $k = trim($keyword);
                if ($k === '') {
                    return;
                }
                if (is_numeric($k)) {
                    $query->havingRaw('COUNT(devis_items.id) = ?', [(int)$k]);
                } else {
                    $query->havingRaw('COUNT(devis_items.id) LIKE ?', ["%{$k}%"]);
                }
            })
            // recherche sur last_updated_at (agrégat MAX) : utiliser HAVING
            ->filterColumn('last_updated_at', function($query, $keyword) {
                $k = trim($keyword);
                if ($k === '') {
                    return;
                }
                $query->havingRaw('MAX(devis.updated_at) LIKE ?', ["%{$k}%"]);
            })
            // pour le champ concaténé expediteur_nom_complet, Yajra ne sait pas le mapper automatiquement ;
            // on transforme la recherche globale pour inclure le concat
            ->filter(function ($query) use ($request) {
                $search = $request->get('search')['value'] ?? null;
                if ($search === null || trim($search) === '') {
                    return;
                }
                $s = mb_strtolower(trim($search), 'UTF-8');
    
                $query->where(function($q) use ($s) {
                    $q->orWhereRaw('LOWER(devis.reference) LIKE ?', ["%{$s}%"])
                      ->orWhereRaw('LOWER(CONCAT(devis.nom_expediteur, " ", devis.prenom_expediteur)) LIKE ?', ["%{$s}%"])
                      ->orWhereRaw('LOWER(devis.agence_expedition) LIKE ?', ["%{$s}%"])
                      ->orWhereRaw('LOWER(devis.tel_expediteur) LIKE ?', ["%{$s}%"])
                      ->orWhereRaw('LOWER(devis.agence_destination) LIKE ?', ["%{$s}%"])
                      ->orWhereRaw('LOWER(devis.etat) LIKE ?', ["%{$s}%"]);
                });
            })
            ->addColumn('action', function ($row) {
                $viewUrl = route('customer_colis.devis.show', ['reference' => $row->reference]);
                $deleteBtn = '<button class="btn btn-sm btn-danger btn-delete-group" data-reference="'.$row->reference.'" title="Supprimer"><i class="fas fa-trash"></i></button>';
                $viewBtn = '<a href="'.$viewUrl.'" class="btn btn-sm btn-primary" title="Voir"><i class="fas fa-eye"></i></a>';
                return '<div class="btn-group" role="group">'.$viewBtn.$deleteBtn.'</div>';
            })
            ->editColumn('last_updated_at', function ($row) {
                return $row->last_updated_at ? \Carbon\Carbon::parse($row->last_updated_at)->format('d/m/Y H:i') : '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    

    /**
     * (optionnel) méthode show pour afficher un devis complet par référence
     * route name: customer_colis.devis.show
     */
    public function show_devis($reference)
    {
        $devis = Devis::where('reference', $reference)->with('items')->firstOrFail();
        return view('customer.devis.show', compact('devis'));
    }
    public function destroy(Request $request, $reference)
    {
        try {
            // Récupérer le devis par référence et appartenant à l'utilisateur authentifié
            $devis = Devis::where('reference', $reference)
                          ->where('user_id', auth()->id())
                          ->first();
    
            if (! $devis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Devis introuvable ou accès interdit.'
                ], 404);
            }
    
            DB::beginTransaction();
    
            // Supprimer les items liés (si la relation items() est définie)
            if (method_exists($devis, 'items')) {
                $devis->items()->delete();
            } else {
                // fallback : suppression via modèle DevisItem si nécessaire
                DevisItem::where('devis_id', $devis->id)->delete();
            }
    
            // Supprimer le devis
            $devis->delete();
    
            DB::commit();
    
            return response()->json([
                'success' => 'Le devis a été supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression devis: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du devis.'
            ], 500);
        }
    }
    public function holdDevis()
{
    $userId = auth()->id();
    $devisEnAttente = Devis::where('user_id', $userId)
                            ->where('etat', 'en attente')
                            ->get();

    return view('customer.devis.hold', compact('devisEnAttente'));
}
public function edit($reference)
{
    $devis = Devis::where('reference', $reference)
                  ->where('user_id', auth()->id())
                  ->with('items')
                  ->firstOrFail();

    $user = auth()->user();

    $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->distinct()->pluck('pays_agence');
    $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
    $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
    $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();

    $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
    $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

    return view('customer.devis.edit', compact(
        'devis', 'user', 'agencesExpedition', 'agencesDestination', 'paysUniques',
        'client_expediteurs', 'client_destinataires'
    ));
}
public function update(Request $request, $reference)
{
    $devis = Devis::where('reference', $reference)
                  ->where('user_id', auth()->id())
                  ->with('items')
                  ->firstOrFail();

    $validatedData = $request->validate([
        'mode_transit' => 'required|string|in:maritime,aerien',
        'pays_expedition' => 'required|string|max:100',
        'agence_expedition' => 'required|string|max:255',
        'agence_destination_societe' => 'required|string|max:255', 
        'nom_expediteur' => 'required|string|max:255',
        'prenom_expediteur' => 'required|string|max:255',
        'email_expediteur' => 'nullable|email|max:255',
        'tel_expediteur' => 'nullable|string|max:50',
        'adresse_expediteur' => 'required|string',
        'devise' => 'required|string|in:EUR,FCFA',
        'quantite_colis.*' => 'required|integer|min:1',
        'service.*' => 'required|string|max:255',
        'valeur_colis.*' => 'nullable|numeric|min:0',
        'type_colis.*' => 'required|string|in:standard,fragile',
    ]);

    try {
        DB::transaction(function() use ($request, $devis) {
            $email_expediteur = $request->email_expediteur ?: auth()->user()->email;

            // Mettre à jour le devis
            $devis->update([
                'mode_transit' => $request->mode_transit,
                'pays_expedition' => $request->pays_expedition,
                'agence_expedition' => $request->agence_expedition,
                'agence_destination' => $request->agence_destination_societe,
                'nom_expediteur' => $request->nom_expediteur,
                'prenom_expediteur' => $request->prenom_expediteur,
                'email_expediteur' => $email_expediteur,
                'tel_expediteur' => $request->tel_expediteur,
                'adresse_expediteur' => $request->adresse_expediteur,
                'devise' => $request->devise,
            ]);

            // Supprimer les items existants
            $devis->items()->delete();

            // Re-créer les items depuis le formulaire
            foreach ($request->service as $key => $service) {
                // Gestion des valeurs null selon mode_transit
                $poids = $request->mode_transit === 'maritime' ? null : $request->poids[$key] ?? null;
                $longueur = $request->mode_transit === 'aerien' ? null : $request->longueur[$key] ?? null;
                $largeur  = $request->mode_transit === 'aerien' ? null : $request->largeur[$key] ?? null;
                $hauteur  = $request->mode_transit === 'aerien' ? null : $request->hauteur[$key] ?? null;

                $devis->items()->create([
                    'quantite_colis' => $request->quantite_colis[$key],
                    'service' => $service,
                    'valeur_colis' => $request->valeur_colis[$key] ?? null,
                    'type_colis' => $request->type_colis[$key],
                    'description_colis' => $request->description_colis[$key] ?? null,
                    'poids' => $poids,
                    'longueur' => $longueur,
                    'largeur' => $largeur,
                    'hauteur' => $hauteur,
                ]);
            }
        });

        // Redirection avec message de succès stylé
        return redirect()->route('customer_colis.devis.show', $devis->reference)
                         ->with('success_popup', 'Devis mis à jour avec succès !');

    } catch (\Exception $e) {
        Log::error('Erreur lors de la mise à jour du devis: ' . $e->getMessage());
        return back()->with('error', 'Une erreur est survenue lors de la mise à jour du devis.')->withInput();
    }
}
public function history()
{
    $userId = auth()->id();

    // Récupère les devis de l'utilisateur connecté dont l'état est 'validé'
    // On charge également la relation 'items' pour pouvoir compter le nombre de colis
    $devis = Devis::where('user_id', $userId)
                    ->where('etat','!=', 'en attente')
                    ->where('etat','!=', 'annulé')
                    ->with('items') // Eager loading pour optimiser la requête
                    ->latest('updated_at') // Trie les résultats par date de mise à jour (les plus récents en premier)
                    ->get();

    // Retourne la vue en lui passant la liste des devis
    return view('customer.devis.history', compact('devis'));
}

public function confirmDevis(Request $request, $reference)
{
    // Valider les données de la requête
    $request->validate([
        'mode_de_retrait' => 'required|string|in:envoi,retrait',
    ]);

    try {
        $devis = Devis::where('reference', $reference)
                      ->where('user_id', auth()->id())
                      ->firstOrFail();

        // Mettre à jour le devis avec le mode de retrait et un nouvel état
        $devis->update([
            'mode_de_retrait' => $request->mode_de_retrait,
            'etat' => 'confirmé' // On change l'état pour refléter l'action
        ]);

        return response()->json(['success' => 'Devis confirmé avec succès !']);

    } catch (\Exception $e) {
        Log::error('Erreur confirmation devis: ' . $e->getMessage());
        return response()->json(['error' => 'Une erreur est survenue.'], 500);
    }
}

/**
 * Annule un devis validé.
 */
public function cancelDevis($reference)
{
    try {
        $devis = Devis::where('reference', $reference)
                      ->where('user_id', auth()->id())
                      ->firstOrFail();

        // Changer l'état du devis à 'annulé'
        $devis->update(['etat' => 'annulé']);

        return response()->json(['success' => 'Le devis a été annulé.']);

    } catch (\Exception $e) {
        Log::error('Erreur annulation devis: ' . $e->getMessage());
        return response()->json(['error' => 'Une erreur est survenue.'], 500);
    }
}

}