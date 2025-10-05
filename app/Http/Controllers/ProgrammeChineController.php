<?php
namespace App\Http\Controllers;

use App\Models\Programme;
use Illuminate\Http\Request;
use App\Models\Colis;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Devis;
use App\Models\Agence;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
class ProgrammeChineController extends Controller
{
    public function index()
    {
        return view('AGENCE_CHINE.transport.planing');
    }

    public function data()
    {
        // Récupérer les utilisateurs avec le rôle chauffeur de l'agence 7
        $chauffeurs = User::where('agence_id', 7)
            ->where('role', 'chauffeur')
            ->where('is_active', true)
            ->get();
        
        // Récupérer les programmes associés aux chauffeurs de l'agence 7
        $programmes = Programme::whereHas('user', function ($query) {
                $query->where('agence_id', 7)
                      ->where('role', 'chauffeur');
            })
            ->with('user')
            ->orderByDesc('date_programme')
            ->get()
            ->map(function ($programme) {
                return [
                    'id' => $programme->id,
                    'date_programme' => $programme->date_programme,
                    'user' => $programme->user,
                    'user_id' => $programme->user_id,
                    'reference_colis' => $programme->reference_colis,
                    'nature_du_colis' => $programme->nature_du_colis,
                    'actions_a_faire' => $programme->actions_a_faire,
                    'nom_expediteur' => $programme->nom_expediteur,
                    'Adresse_expedition' => $programme->lieu_expedition,
                    'tel_expediteur' => $programme->tel_expediteur,
                    'nom_destinataire' => $programme->nom_destinataire,
                    'tel_destinataire' => $programme->tel_destinataire,
                    'Adresse_destination' => $programme->lieu_destination,
                    'etat_rdv' => $programme->etat_rdv
                ];
            });

        // Récupérer les colis valides non attribués
        $colisValides = Colis::where('etat', 'Validé')
            ->whereNotIn('reference_colis', Programme::pluck('reference_colis')->toArray())
            ->with('expediteur', 'destinataire')
            ->get();

        return response()->json([
            'chauffeurs' => $chauffeurs,
            'programmes' => $programmes,
            'colisValides' => $colisValides
        ]);
    }

    public function store(Request $request)
    {
        \Log::info("Début de la création du programme Chine", $request->all());
        
        try {
            // Validation
            $request->validate([
                'date_programme' => 'required|date',
                'user_id' => 'required|exists:users,id',
                'actions_a_faire.*' => 'required|in:depot,recuperation,livraison',
            ]);

            $dateProgramme = $request->date_programme;
            $userId = $request->user_id;
            $referencesColis = $request->input('reference_colis', []);
            $natureDuColis = $request->input('nature_du_colis', []);
            $actionsAFaire = $request->input('actions_a_faire', []);
            $nomExpediteurs = $request->input('nom_expediteur', []);
            $adresseExpeditions = $request->input('Adresse_expedition', []);
            $telExpediteurs = $request->input('tel_expediteur', []);
            $nomDestinataires = $request->input('nom_destinataire', []);
            $telDestinataires = $request->input('tel_destinataire', []);
            $adresseDestinations = $request->input('Adresse_destination', []);

            \Log::info("Données récupérées Chine:", [
                'date_programme' => $dateProgramme,
                'user_id' => $userId,
                'actions_a_faire' => $actionsAFaire,
                'references_colis' => $referencesColis
            ]);

            DB::beginTransaction();

            $programmesCrees = [];

            foreach ($actionsAFaire as $index => $action) {
                $referenceColis = $referencesColis[$index] ?? null;
                
                \Log::info("Traitement de l'entrée $index Chine", [
                    'action' => $action,
                    'reference_colis' => $referenceColis
                ]);

                // Vérification de l'unicité de la référence colis
                if (!empty($referenceColis)) {
                    $programmeExistant = Programme::where('reference_colis', $referenceColis)->first();
                    if ($programmeExistant) {
                        throw new \Exception("Le colis $referenceColis est déjà attribué");
                    }
                }
                
                if ($action === 'recuperation') {
                    // Pour une récupération, les champs peuvent être vides
                    $programme = Programme::create([
                        'date_programme' => $dateProgramme,
                        'user_id' => $userId,
                        'reference_colis' => $referenceColis,
                        'nature_du_colis' => $natureDuColis[$index] ?? null,
                        'actions_a_faire' => $action,
                        'nom_expediteur' => $nomExpediteurs[$index] ?? null,
                        'lieu_expedition' => $adresseExpeditions[$index] ?? null,
                        'tel_expediteur' => $telExpediteurs[$index] ?? null,
                        'nom_destinataire' => $nomDestinataires[$index] ?? null,
                        'tel_destinataire' => $telDestinataires[$index] ?? null,
                        'lieu_destination' => $adresseDestinations[$index] ?? null,
                        'etat_rdv' => 'en attente',
                    ]);
                    
                    $programmesCrees[] = $programme;
                    \Log::info("Programme de récupération créé Chine", ['id' => $programme->id]);

                } else {
                    // Pour dépôt et livraison, la référence colis est obligatoire
                    if (empty($referenceColis)) {
                        throw new \Exception("La référence colis est obligatoire pour l'action '$action'");
                    }

                    $programmeExistant = Programme::where('reference_colis', $referenceColis)->first();
                    if ($programmeExistant) {
                        throw new \Exception("Le colis $referenceColis est déjà attribué");
                    }

                    $colis = Colis::where('reference_colis', $referenceColis)->with('expediteur', 'destinataire')->first();
                    if (!$colis) {
                        throw new \Exception("Le colis $referenceColis n'existe pas");
                    }

                    $nomExpediteur = $colis->expediteur->nom . ' ' . $colis->expediteur->prenom;
                    $nomDestinataire = $colis->destinataire->nom . ' ' . $colis->destinataire->prenom;

                    $programme = Programme::create([
                        'date_programme' => $dateProgramme,
                        'user_id' => $userId,
                        'reference_colis' => $referenceColis,
                        'nature_du_colis' => $natureDuColis[$index] ?? null,
                        'actions_a_faire' => $action,
                        'nom_expediteur' => $nomExpediteur,
                        'lieu_expedition' => $colis->expediteur->adresse,
                        'tel_expediteur' => $colis->expediteur->tel,
                        'nom_destinataire' => $nomDestinataire,
                        'tel_destinataire' => $colis->destinataire->tel,
                        'lieu_destination' => $colis->destinataire->adresse,
                        'etat_rdv' => 'en attente',
                    ]);
                    
                    $programmesCrees[] = $programme;
                    \Log::info("Programme de $action créé Chine", ['id' => $programme->id]);
                }
            }

            DB::commit();
            
            \Log::info("Programmes créés avec succès Chine", [
                'nombre' => count($programmesCrees),
                'ids' => array_map(function($p) { return $p->id; }, $programmesCrees)
            ]);

            return redirect()->back()->with('success', count($programmesCrees) . ' programme(s) créé(s) avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur création programmes Chine: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Vérifier que le programme appartient à l'agence 7 via l'utilisateur
        $programme = Programme::whereHas('user', function($q) {
                $q->where('agence_id', 7)
                  ->where('role', 'chauffeur');
            })
            ->findOrFail($id);

        // Récupérer les utilisateurs avec rôle chauffeur de l'agence 7
        $chauffeurs = User::where('agence_id', 7)
            ->where('role', 'chauffeur')
            ->where('is_active', true)
            ->get();
        
        return response()->json([
            'programme' => $programme,
            'chauffeurs' => $chauffeurs
        ]);
    }

    public function update(Request $request, $id)
    {
        $programme = Programme::whereHas('user', function($q) {
                $q->where('agence_id', 7)
                  ->where('role', 'chauffeur');
            })
            ->findOrFail($id);

        $action = $request->has('actions_a_faire') 
            ? $request->actions_a_faire 
            : $programme->actions_a_faire;

        $rules = [
            'date_programme' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'actions_a_faire' => 'nullable|in:depot,recuperation,livraison',
        ];

        if ($action === 'recuperation') {
            $rules['reference_colis'] = 'nullable';
        } else {
            $rules['reference_colis'] = 'nullable|exists:colis,reference_colis';
        }
        
        $request->validate($rules);

        if ($action !== 'recuperation' && 
            $request->has('reference_colis') && 
            $request->reference_colis != $programme->reference_colis) 
        {
            $programmeExistant = Programme::where('reference_colis', $request->reference_colis)
                ->where('id', '!=', $programme->id)
                ->first();

            if ($programmeExistant) {
                return response()->json([
                    'success' => false,
                    'message' => "Le colis {$request->reference_colis} est déjà attribué"
                ], 422);
            }
        }

        $updated = false;
        $fields = ['date_programme', 'user_id', 'reference_colis', 'actions_a_faire'];
        
        foreach ($fields as $field) {
            if ($request->has($field) && $request->$field != $programme->$field) {
                $programme->$field = $request->$field;
                $updated = true;
            }
        }

        if ($updated) {
            $programme->save();
            return response()->json([
                'success' => true,
                'message' => 'Programme mis à jour avec succès'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Aucune modification détectée'
        ]);
    }

    public function destroy($id)
    {
        $programme = Programme::whereHas('user', function($q) {
                $q->where('agence_id', 7)
                  ->where('role', 'chauffeur');
            })
            ->findOrFail($id);
            
        try {
            $programme->delete();
            return response()->json(['success' => true, 'message' => 'Programme supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }
    public function ajoutDevis()
    {
        return view('AGENCE_CHINE.transport.ajoutdevis');
    }

    public function add_devis(Request $request)
    {
        $id = auth()->user()->getIdUSer();
        $user = User::findOrfail($id);

        // Ces données ne sont plus nécessaires dans la vue mais gardons-les au cas où
        $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->distinct()->pluck('pays_agence');
        $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
        $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();

        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        return view('AGENCE_CHINE.transport.ajoutdevis', compact(
            'agencesExpedition', 'agencesDestination', 'paysUniques',
            'client_expediteurs', 'client_destinataires','user'
        ));
    }

    public function store_devis(Request $request)
    {
        \Log::info('Données reçues:', $request->all());

        try {
            $devis = DB::transaction(function () use ($request) {

                // 1. On récupère les initiales à partir des données validées du formulaire
                $initialNom = mb_substr($request->nom_expediteur, 0, 1);
                $initialPrenom = mb_substr($request->prenom_expediteur, 0, 1);
                $initiales = strtoupper($initialNom . $initialPrenom);

                // 2. On génère un code unique avec le format NA000000NC (sans -RE)
                do {
                    $randomNumber = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $reference = 'NA' . $randomNumber . $initiales; // Suppression du "-RE"
                } while (Devis::where('reference', $reference)->exists());

                // Création du devis avec les données fixes pour la Chine
                $newDevis = Devis::create([
                    'reference' => $reference,
                    'mode_transit' => $request->mode_transit,
                    'pays_expedition' => 'Chine', // Fixe pour Chine
                    'agence_expedition' => 'Agence de Chine', // Fixe pour Chine
                    'agence_destination' => $request->agence_destination_societe,
                    'nom_expediteur' => $request->nom_expediteur,
                    'prenom_expediteur' => $request->prenom_expediteur,
                    'email_expediteur' => $request->email_expediteur,
                    'tel_expediteur' => $request->tel_expediteur,
                    'adresse_expediteur' => $request->adresse_expediteur,
                    'devise' => 'FCFA', // Devise fixée à FCFA pour Chine
                    'user_id' => auth()->id(),
                    'etat' => 'confirmé', // État à confirmé
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

                return $newDevis;
            });

            // REDIRECTION CORRIGÉE : rediriger vers la même page (ajoutDevis)
            return redirect()->route('chine_programme.ajoutDevis')->with('success_popup', 'Devis créé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du devis: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la soumission de votre devis. Veuillez réessayer.')->withInput();
        }
    }
}