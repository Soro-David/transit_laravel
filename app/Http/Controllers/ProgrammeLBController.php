<?php
// app/Http/Controllers/ProgrammeLBController.php

namespace App\Http\Controllers;

use App\Models\Programme;
use App\Models\Agence;
use App\Models\ProgrammeItems;
use App\Models\Devis;
use App\Models\DevisItems;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;

class ProgrammeLBController extends Controller
{
    public function index()
    {
        return view('AFT_LOUIS_BLERIOT.transport.planing');
    }

    /**
     * Retourne les données nécessaires au front.
     * Normalise les informations utilisateur pour exposer `first_name` et `last_name`.
     */
    private function generateReferenceDepot($user, $chauffeur)
    {
        $initialAgent = strtoupper(substr($user->first_name, 0, 2) ?: 'AG');
        $nomChauffeur = strtoupper(substr($chauffeur->first_name, 0, 2) ?: 'CH');
        $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return $initialAgent . $randomNumber . $nomChauffeur;
    }

    /**
     * Retourne les données nécessaires au front
     */
   /**
 * Retourne les données nécessaires au front
 */
public function data()
{
    // Récupération des chauffeurs
    $rawChauffeurs = User::where('agence_id', 5)
        ->where('role', 'chauffeur')
        ->where('is_active', true)
        ->get(['id', 'first_name', 'last_name']);

    $chauffeurs = $rawChauffeurs->map(function ($c) {
        $first = $c->first_name ?? $c->nom ?? ($c->name ?? '');
        $last  = $c->last_name  ?? $c->prenom ?? '';
        return [
            'id' => $c->id,
            'first_name' => trim($first),
            'last_name' => trim($last),
        ];
    })->values();

    // Récupération des programmes (INCLUANT "à planifié")
    $programmes = Programme::with(['user', 'devis'])
        ->where(function($query) {
            $query->whereHas('user', function ($q) {
                $q->where('agence_id', 5)
                  ->where('role', 'chauffeur');
            })->orWhereNull('user_id'); // Inclure les programmes sans chauffeur (à planifié)
        })
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($programme) {
            // Déterminer la référence à afficher
            $programme->reference_a_afficher = $programme->reference_generee ?: $programme->reference_colis;

            // S'assurer que les données utilisateur sont bien formatées
            if ($programme->user) {
                $first = $programme->user->first_name ?? $programme->user->nom ?? '';
                $last = $programme->user->last_name ?? $programme->user->prenom ?? '';
                $programme->user->full_name = trim($first . ' ' . $last);
            }

            return $programme;
        });

    // Récupérer les références de devis confirmés
    $devisConfirmes = Devis::where('etat', 'confirmé')
        ->whereNotIn('reference', Programme::pluck('reference_colis')->toArray())
        ->pluck('reference');

    // CORRECTION : Récupérer uniquement les dépôts EFFECTUÉS pour récupération
    $depotsPourRecuperation = Programme::where('actions_a_faire', 'depot')
        ->where('etat_rdv', 'effectué') // ← CONDITION AJOUTÉE
        ->whereNotNull('reference_generee')
        ->whereNotIn('reference_generee', Programme::where('actions_a_faire', 'recuperation')->pluck('reference_colis')->toArray())
        ->pluck('reference_generee');

    $response = [
        'chauffeurs' => $chauffeurs,
        'programmes' => $programmes,
        'devisReferences' => $devisConfirmes,
        'depotsPourRecuperation' => $depotsPourRecuperation,
        'debug_info' => [
            'chauffeurs_count' => $chauffeurs->count(),
            'programmes_count' => $programmes->count(),
            'devis_count' => $devisConfirmes->count(),
            'depots_recuperables_count' => $depotsPourRecuperation->count(),
            'timestamp' => now()->toDateTimeString()
        ]
    ];

    return response()->json($response);
}
    /**
     * Création d'un dépôt avec référence générée
     */
    public function createDepot(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'quantite' => 'required|integer|min:1',
                'date_programme' => 'required|date',
                'user_id' => 'required|exists:users,id',
                'nom_expediteur' => 'required|string|max:255',
                'lieu_expedition' => 'required|string|max:255',
                'tel_expediteur' => 'required|string|max:20',
                'nature_du_colis' => 'required|string|max:255',
            ]);

            $user = auth()->user();
            $chauffeur = User::find($request->user_id);

            // Générer la référence
            $referenceGeneree = $this->generateReferenceDepot($user, $chauffeur);

            $programme = Programme::create([
                'quantite' => $request->quantite,
                'date_programme' => $request->date_programme,
                'user_id' => $request->user_id,
                'reference_generee' => $referenceGeneree,
                'type_reference' => 'generee',
                'actions_a_faire' => 'depot',
                'nom_expediteur' => $request->nom_expediteur,
                'lieu_expedition' => $request->lieu_expedition,
                'tel_expediteur' => $request->tel_expediteur,
                'nature_du_colis' => $request->nature_du_colis,
                'etat_rdv' => 'en attente',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dépôt programmé avec succès! Référence: ' . $referenceGeneree,
                'programme' => $programme,
                'reference_generee' => $referenceGeneree
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création dépôt: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Création d'une récupération
     */
    public function createMultipleDepot(Request $request)
{
    try {
        DB::beginTransaction();

        $request->validate([
            'programmes' => 'required|array|min:1',
            'programmes.*.quantite' => 'required|integer|min:1',
            'programmes.*.nom_expediteur' => 'required|string|max:255',
            'programmes.*.lieu_expedition' => 'required|string|max:255',
            'programmes.*.tel_expediteur' => 'required|string|max:20',
            'programmes.*.nature_du_colis' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'date_programme' => 'required|date',
        ]);

        $user = auth()->user();
        $chauffeur = User::find($request->user_id);
        $createdCount = 0;
        $failedCount = 0;
        $details = [];

        foreach ($request->programmes as $index => $programmeData) {
            try {
                // Générer la référence unique pour chaque dépôt
                $referenceGeneree = $this->generateReferenceDepot($user, $chauffeur);

                // Vérifier les doublons
                $existingProgramme = Programme::where('reference_generee', $referenceGeneree)
                    ->where('actions_a_faire', 'depot')
                    ->first();

                if ($existingProgramme) {
                    $details[] = [
                        'reference' => $referenceGeneree,
                        'status' => '❌ Doublon - existe déjà'
                    ];
                    $failedCount++;
                    continue;
                }

                // Création du programme de dépôt avec les nouvelles informations
                $programme = Programme::create([
                    'quantite' => $programmeData['quantite'],
                    'date_programme' => $request->date_programme,
                    'user_id' => $request->user_id,
                    'agent_id' => $user->id, // ID de l'agent connecté
                    'reference_generee' => $referenceGeneree,
                    'type_reference' => 'generee',
                    'actions_a_faire' => 'depot',
                    'nom_expediteur' => $programmeData['nom_expediteur'],
                    'lieu_expedition' => $programmeData['lieu_expedition'],
                    'tel_expediteur' => $programmeData['tel_expediteur'],
                    'nature_du_colis' => $programmeData['nature_du_colis'],
                    // Informations par défaut pour les dépôts
                    'mode_transit' => 'aerien', // Par défaut pour les dépôts
                    'agence_expedition' => 'AFT Agence Louis Bleriot', // Agence fixe
                    'devise' => 'EUR', // Devise par défaut
                    'etat_rdv' => 'en attente',
                ]);

                $createdCount++;
                $details[] = [
                    'reference' => $referenceGeneree,
                    'status' => '✅ Créé avec succès'
                ];

                Log::info("✅ Dépôt multiple créé avec ID: " . $programme->id . " - Référence: " . $referenceGeneree);

            } catch (\Exception $e) {
                $failedCount++;
                $details[] = [
                    'reference' => 'N/A',
                    'status' => '❌ Erreur: ' . $e->getMessage()
                ];
                Log::error("❌ Erreur création dépôt multiple {$index}: " . $e->getMessage());
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Traitement des dépôts terminé',
            'created_count' => $createdCount,
            'failed_count' => $failedCount,
            'details' => $details,
            'total_programmes' => count($request->programmes)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Erreur création dépôts multiples: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur globale: ' . $e->getMessage()
        ], 500);
    }
}

    public function createRecuperation(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $request->validate([
                'quantite' => 'required|integer|min:1',
                'date_programme' => 'required|date',
                'user_id' => 'required|exists:users,id',
                'nom_expediteur' => 'required|string|max:255',
                'lieu_expedition' => 'required|string|max:255',
                'tel_expediteur' => 'required|string|max:20',
                'nature_du_colis' => 'required|string|max:255',
                'type_reference' => 'required|in:devis,depot,manuel',
                'reference_input' => 'required|string|max:255',
            ]);
    // Dans la méthode createRecuperation, après la validation
$modeTransit = $request->mode_transit ?? 'aerien';

// Pour les articles, s'assurer que les dimensions sont cohérentes avec le mode transit
if ($modeTransit === 'aerien') {
    // Forcer les dimensions à 0 pour l'aérien
    if ($request->has('devis_items')) {
        $devisItemsData = json_decode($request->devis_items, true);
        foreach ($devisItemsData as &$item) {
            $item['longueur'] = 0;
            $item['largeur'] = 0;
            $item['hauteur'] = 0;
        }
        // Mettre à jour la request avec les données modifiées
        $request->merge(['devis_items' => json_encode($devisItemsData)]);
    }
}
            $referenceColis = $request->reference_input;
            $modificationsApportees = $request->has('modifications_apportees');
    
            // Génération de la référence
            $referenceGeneree = null;
            if ($request->type_reference === 'manuel' && empty($referenceColis)) {
                $user = auth()->user();
                $chauffeur = User::find($request->user_id);
                $initialAgent = strtoupper(substr($user->first_name, 0, 2) ?: 'AG');
                $nomChauffeur = strtoupper(substr($chauffeur->first_name, 0, 2) ?: 'CH');
                $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $referenceColis = $initialAgent . $randomNumber . $nomChauffeur;
            }
            
            // Toujours générer une référence -RE pour les récupérations
            $referenceGeneree = $referenceColis . '-RE';
    
            // ⚠️ VÉRIFICATION CRITIQUE : Empêcher les doublons
            $existingProgramme = Programme::where('reference_generee', $referenceGeneree)
                ->where('actions_a_faire', 'recuperation')
                ->first();
    
            if ($existingProgramme) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Une récupération avec la référence "' . $referenceGeneree . '" existe déjà. Impossible de créer un doublon.'
                ], 422);
            }
    
            // Vérification supplémentaire pour les devis : empêcher de créer plusieurs récupérations pour le même devis
            if ($request->type_reference === 'devis') {
                $existingDevisRecuperation = Programme::where('reference_colis', $referenceColis)
                    ->where('actions_a_faire', 'recuperation')
                    ->first();
    
                if ($existingDevisRecuperation) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Une récupération pour le devis "' . $referenceColis . '" existe déjà. Référence: ' . $existingDevisRecuperation->reference_generee
                    ], 422);
                }
            }
    
            // Création du programme
            $programme = Programme::create([
                'quantite'        => $request->quantite,
                'date_programme'  => $request->date_programme,
                'user_id'         => $request->user_id,
                'reference_colis'   => $referenceColis,
                'reference_generee' => $referenceGeneree,
                'type_reference'    => $request->type_reference,
                'actions_a_faire'   => 'recuperation',
                'nom_expediteur'    => $request->nom_expediteur,
                'lieu_expedition'   => $request->lieu_expedition,
                'tel_expediteur'    => $request->tel_expediteur,
                'nature_du_colis'   => $request->nature_du_colis,
                'etat_rdv'          => 'en attente',
            ]);
    
            Log::info("✅ Programme créé avec ID: " . $programme->id . " - Référence: " . $referenceGeneree);
    
            // CRÉATION DES ARTICLES - LOGIQUE CORRIGÉE
            $devisItemsCreated = false;
            
            // Cas 1: Type "devis" avec modifications
            if ($request->type_reference === 'devis' && $modificationsApportees && $request->has('devis_items')) {
                
                $devisItemsData = json_decode($request->devis_items, true);
                
                if (!empty($devisItemsData)) {
                    Log::info("📦 Création de nouveaux articles pour le programme ID: " . $programme->id);
    
                    foreach ($devisItemsData as $itemData) {
                        ProgrammeItems::create([
                            'programme_id'      => $programme->id,
                            'devis_id'          => null,
                            'quantite_colis'    => $itemData['quantite_colis'] ?? 1,
                            'service'           => $itemData['service'] ?? 'Service non spécifié',
                            'valeur_colis'      => $itemData['valeur_colis'] ?? 0,
                            'type_colis'        => $itemData['type_colis'] ?? 'Colis divers',
                            'description_colis' => $itemData['description_colis'] ?? '',
                            'poids'             => $itemData['poids'] ?? 0,
                            'longueur'          => $itemData['longueur'] ?? 0,
                            'largeur'           => $itemData['largeur'] ?? 0,
                            'hauteur'           => $itemData['hauteur'] ?? 0,
                        ]);
                    }
                    $devisItemsCreated = true;
                    Log::info("✅ " . count($devisItemsData) . " articles créés avec succès pour le programme ID: " . $programme->id);
                }
            }
            // Cas 2: Type "devis" SANS modifications - COPIER les items du devis original
            elseif ($request->type_reference === 'devis' && !$modificationsApportees) {
                $devis = Devis::where('reference', $referenceColis)->first();
                
                if ($devis && $devis->items->count() > 0) {
                    Log::info("📦 Copie des articles du devis original pour le programme ID: " . $programme->id);
                    
                    foreach ($devis->items as $originalItem) {
                        ProgrammeItems::create([
                            'programme_id'      => $programme->id,
                            'devis_id'          => $originalItem->devis_id,
                            'quantite_colis'    => $originalItem->quantite_colis,
                            'service'           => $originalItem->service,
                            'valeur_colis'      => $originalItem->valeur_colis,
                            'type_colis'        => $originalItem->type_colis,
                            'description_colis' => $originalItem->description_colis,
                            'poids'             => $originalItem->poids,
                            'longueur'          => $originalItem->longueur,
                            'largeur'           => $originalItem->largeur,
                            'hauteur'           => $originalItem->hauteur,
                        ]);
                    }
                    $devisItemsCreated = true;
                    Log::info("✅ " . $devis->items->count() . " articles copiés du devis original");
                }
            }
            // Cas 3: Types "depot" ou "manuel" - UTILISER les articles modifiés/ajoutés
            else {
                // Vérifier si des articles ont été modifiés/ajoutés
                if ($modificationsApportees && $request->has('devis_items')) {
                    $devisItemsData = json_decode($request->devis_items, true);
                    
                    if (!empty($devisItemsData)) {
                        Log::info("📦 Création d'articles personnalisés pour le programme ID: " . $programme->id);
    
                        foreach ($devisItemsData as $itemData) {
                            ProgrammeItems::create([
                                'programme_id'      => $programme->id,
                                'devis_id'          => null,
                                'quantite_colis'    => $itemData['quantite_colis'] ?? 1,
                                'service'           => $itemData['service'] ?? 'Transport standard',
                                'valeur_colis'      => $itemData['valeur_colis'] ?? 0,
                                'type_colis'        => $itemData['type_colis'] ?? ($request->nature_du_colis ?? 'Colis divers'),
                                'description_colis' => $itemData['description_colis'] ?? 'Article créé pour la récupération',
                                'poids'             => $itemData['poids'] ?? 0,
                                'longueur'          => $itemData['longueur'] ?? 0,
                                'largeur'           => $itemData['largeur'] ?? 0,
                                'hauteur'           => $itemData['hauteur'] ?? 0,
                            ]);
                        }
                        $devisItemsCreated = true;
                        Log::info("✅ " . count($devisItemsData) . " articles personnalisés créés pour le programme ID: " . $programme->id);
                    }
                } else {
                    // Si pas de modifications, créer un seul article avec les infos du formulaire
                    Log::info("📦 Création d'un article par défaut pour le programme ID: " . $programme->id);
                    
                    ProgrammeItems::create([
                        'programme_id'      => $programme->id,
                        'devis_id'          => null,
                        'quantite_colis'    => $request->quantite ?? 1,
                        'service'           => 'Transport standard',
                        'valeur_colis'      => 0,
                        'type_colis'        => $request->nature_du_colis ?? 'Colis divers',
                        'description_colis' => 'Article créé automatiquement pour la récupération',
                        'poids'             => 0,
                        'longueur'          => 0,
                        'largeur'           => 0,
                        'hauteur'           => 0,
                    ]);
                    $devisItemsCreated = true;
                }
            }
    
            DB::commit();
    
            return response()->json([
                'success'       => true,
                'message'       => 'Récupération programmée avec succès!' . ($devisItemsCreated ? ' Articles créés.' : ''),
                'programme'     => $programme,
                'items_created' => $devisItemsCreated,
                'reference_generee' => $referenceGeneree
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Erreur création récupération: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
    
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Récupère les informations d'un devis ou dépôt par référence
     */

   /**
 * Récupère les informations d'un devis ou dépôt par référence
 */
public function getReferenceInfo($reference)
{
    try {
        Log::info("Recherche référence: " . $reference);
 
        // Rechercher d'abord les devis confirmés
        $devis = Devis::where('reference', $reference)
            ->where('etat', 'confirmé')
            ->with('items')
            ->first();
 
        if ($devis) {
            $items = $devis->items;
            
            $itemsData = $items->map(function($item) {
                return [
                    'quantite_colis' => $item->quantite_colis,
                    'service' => $item->service,
                    'valeur_colis' => $item->valeur_colis,
                    'type_colis' => $item->type_colis,
                    'description_colis' => $item->description_colis,
                    'poids' => $item->poids,
                    'longueur' => $item->longueur,
                    'largeur' => $item->largeur,
                    'hauteur' => $item->hauteur
                ];
            });
 
            $natureColis = $items->first()->type_colis ?? 'Colis divers';
            $quantiteTotale = $items->sum('quantite_colis') ?? 1;
            
            Log::info("Devis confirmé trouvé: " . $devis->reference);
            
            return response()->json([
                'type' => 'devis',
                'existe' => true,
                'valide' => true,
                'etat_devis' => $devis->etat,
                'data' => [
                    'nom_expediteur' => trim($devis->nom_expediteur . ' ' . $devis->prenom_expediteur),
                    'prenom_expediteur' => $devis->prenom_expediteur,
                    'email_expediteur' => $devis->email_expediteur,
                    'tel_expediteur' => $devis->tel_expediteur,
                    'lieu_expedition' => $devis->adresse_expediteur,
                    'nature_du_colis' => $natureColis,
                    'quantite' => $quantiteTotale,
                    'mode_transit' => $devis->mode_transit,
                    'pays_expedition' => $devis->pays_expedition,
                    'agence_expedition' => $devis->agence_expedition,
                    'agence_destination' => $devis->agence_destination,
                    'devise' => $devis->devise,
                    'montant' => $devis->montant,
                    'mode_de_retrait' => $devis->mode_de_retrait,
                    'items' => $itemsData
                ],
                'message' => '✅ Devis confirmé trouvé - Remplissage automatique'
            ]);
        }

        // CORRECTION : Rechercher uniquement les dépôts EFFECTUÉS
        $depot = Programme::where('reference_generee', $reference)
            ->where('actions_a_faire', 'depot')
            ->where('etat_rdv', 'effectué') // ← AJOUT DE CETTE CONDITION IMPORTANTE
            ->with('items') // ← AJOUT pour récupérer les articles du dépôt
            ->first();

        if ($depot) {
            $estEffectue = $depot->etat_rdv === 'effectué';
            
            Log::info("Dépôt effectué trouvé: " . $depot->reference_generee . " - État: " . $depot->etat_rdv);
            
            // Récupérer les items du dépôt s'ils existent
            $itemsData = [];
            if ($depot->items && $depot->items->count() > 0) {
                $itemsData = $depot->items->map(function($item) {
                    return [
                        'quantite_colis' => $item->quantite_colis,
                        'service' => $item->service,
                        'valeur_colis' => $item->valeur_colis,
                        'type_colis' => $item->type_colis,
                        'description_colis' => $item->description_colis,
                        'poids' => $item->poids,
                        'longueur' => $item->longueur,
                        'largeur' => $item->largeur,
                        'hauteur' => $item->hauteur
                    ];
                })->toArray();
            }
            
            return response()->json([
                'type' => 'depot',
                'existe' => true,
                'valide' => $estEffectue,
                'data' => [
                    'nom_expediteur' => $depot->nom_expediteur,
                    'lieu_expedition' => $depot->lieu_expedition,
                    'tel_expediteur' => $depot->tel_expediteur,
                    'nature_du_colis' => $depot->nature_du_colis,
                    'quantite' => $depot->quantite,
                    'items' => $itemsData // ← INCLURE LES ARTICLES DU DÉPÔT
                ],
                'message' => $estEffectue ? 
                    '✅ Dépôt effectué trouvé - Récupération possible' : 
                    '⚠️ Dépôt trouvé mais pas encore effectué'
            ]);
        }

        // CORRECTION : Vérifier aussi si c'est un dépôt en attente pour informer l'utilisateur
        $depotEnAttente = Programme::where('reference_generee', $reference)
            ->where('actions_a_faire', 'depot')
            ->where('etat_rdv', 'en attente')
            ->first();

        if ($depotEnAttente) {
            Log::info("Dépôt en attente trouvé: " . $depotEnAttente->reference_generee);
            
            return response()->json([
                'type' => 'depot',
                'existe' => true,
                'valide' => false,
                'data' => [
                    'nom_expediteur' => $depotEnAttente->nom_expediteur,
                    'lieu_expedition' => $depotEnAttente->lieu_expedition,
                    'tel_expediteur' => $depotEnAttente->tel_expediteur,
                    'nature_du_colis' => $depotEnAttente->nature_du_colis,
                    'quantite' => $depotEnAttente->quantite
                ],
                'message' => '❌ Dépôt trouvé mais encore en attente - Impossible de récupérer'
            ]);
        }

        // Référence non trouvée - considérée comme manuelle
        Log::info("Référence non trouvée, considérée comme manuelle: " . $reference);
        return response()->json([
            'type' => 'manuel',
            'existe' => false,
            'valide' => false,
            'data' => null,
            'message' => 'ℹ️ Référence manuelle - Veuillez remplir les informations'
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur getReferenceInfo: " . $e->getMessage());
        return response()->json([
            'error' => $e->getMessage(),
            'existe' => false,
            'valide' => false
        ], 500);
    }
}
    public function getDevisInfo($reference)
    {
        try {
            $devis = Devis::where('reference', $reference)->first();

            if (!$devis) {
                return response()->json(null);
            }

            return response()->json([
                'expediteur' => [
                    'nom' => $devis->nom_expediteur,
                    'prenom' => $devis->prenom_expediteur,
                    'adresse' => $devis->adresse_expediteur,
                    'tel' => $devis->tel_expediteur
                ],
                'destinataire' => [
                    'nom' => 'À compléter',
                    'prenom' => '',
                    'adresse' => $devis->agence_destination,
                    'tel' => ''
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retourne un programme + liste de chauffeurs (normalisés)
     */
    public function edit($id)
    {
        $programme = Programme::with('user')
            ->whereHas('user', function($q) {
                $q->where('agence_id', 5)
                  ->where('role', 'chauffeur');
            })
            ->findOrFail($id);

        // Normaliser le user du programme
        $programmeUser = null;
        if ($programme->user) {
            $programmeUserFirst = $programme->user->first_name ?? $programme->user->nom ?? ($programme->user->name ?? '');
            $programmeUserLast  = $programme->user->last_name  ?? $programme->user->prenom ?? '';
            $programmeUser = [
                'id' => $programme->user->id,
                'first_name' => trim($programmeUserFirst),
                'last_name' => trim($programmeUserLast),
            ];
        }

        // Récupération et normalisation des chauffeurs
        $rawChauffeurs = User::where('agence_id', 5)
            ->where('role', 'chauffeur')
            ->where('is_active', true)
            ->get(['id', 'nom', 'prenom', 'first_name', 'last_name']);

        $chauffeurs = $rawChauffeurs->map(function ($c) {
            $first = $c->first_name ?? $c->nom ?? ($c->name ?? '');
            $last  = $c->last_name  ?? $c->prenom ?? '';
            return [
                'id' => $c->id,
                'first_name' => trim($first),
                'last_name' => trim($last),
            ];
        })->values();

        return response()->json([
            'programme' => array_merge($programme->toArray(), ['user' => $programmeUser]),
            'chauffeurs' => $chauffeurs
        ]);
    }

   
    
    public function checkProgrammeItems($programmeId)
{
    try {
        $programme = Programme::with('items')->findOrFail($programmeId);
        
        return response()->json([
            'programme' => $programme,
            'items_count' => $programme->items->count(),
            'items' => $programme->items,
            'has_programme_id' => $programme->items->every(function($item) {
                return !is_null($item->programme_id);
            })
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function ajoutDevis()
{
    return view('AFT_LOUIS_BLERIOT.transport.ajoutdevis');
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

    return view('AFT_LOUIS_BLERIOT.transport.ajoutdevis', compact(
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
                $reference = 'NA' . $randomNumber . $initiales;
            } while (Devis::where('reference', $reference)->exists());

            // 3. Créer directement dans la table programme avec référence -RE
            $referenceGeneree = $reference . '-RE';

            // Vérifier si une récupération existe déjà pour cette référence
            $existingProgramme = Programme::where('reference_generee', $referenceGeneree)->first();
            if ($existingProgramme) {
                throw new \Exception('Une récupération avec cette référence existe déjà.');
            }

            // 4. Création du programme directement
            $programme = Programme::create([
                'quantite' => $this->calculerQuantiteTotale($request),
                'date_programme' => null,
                'user_id' => null, // Pas de chauffeur attribué pour l'instant
                'agent_id' => auth()->id(), // <-- AJOUT : ID de l'agent connecté
                'reference_colis' => $reference,
                'reference_generee' => $referenceGeneree,
                'type_reference' => 'devis',
                'actions_a_faire' => 'recuperation',
                'nom_expediteur' => $request->nom_expediteur,
                'prenom_expediteur' => $request->prenom_expediteur,
                'agence_expedition' => $request->agence_expedition,
                'lieu_expedition' => $request->adresse_expediteur,
                'tel_expediteur' => $request->tel_expediteur,
                'nature_du_colis' => 'Colis divers',
                'etat_rdv' => 'à planifié',
                'mode_transit' => $request->mode_transit,
                'agence_destination' => $request->agence_destination_societe,
            ]);

            // 5. Créer les items du programme
            foreach ($request->service as $key => $service) {
                ProgrammeItems::create([
                    'programme_id' => $programme->id,
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

            // 6. Optionnel : Créer aussi dans la table devis si nécessaire
            $newDevis = Devis::create([
                'reference' => $reference,
                'mode_transit' => $request->mode_transit,
                'pays_expedition' => 'France',
                'agence_expedition' => 'AFT Agence Louis Bleriot',
                'agence_destination' => $request->agence_destination_societe,
                'nom_expediteur' => $request->nom_expediteur,
                'prenom_expediteur' => $request->prenom_expediteur,
                'email_expediteur' => $request->email_expediteur,
                'tel_expediteur' => $request->tel_expediteur,
                'adresse_expediteur' => $request->adresse_expediteur,
                'devise' => 'EUR',
                'user_id' => auth()->id(), // Ici, 'user_id' fait référence au créateur du devis, ce qui est correct.
                'etat' => 'à planifié',
            ]);

            // Créer les items du devis
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

            return [
                'programme' => $programme,
                'devis' => $newDevis
            ];
        });

        // Retourner une réponse JSON pour le traitement en AJAX
        return response()->json([
            'success' => true,
            'message' => 'Programme enregistré !',
            'reference_generee' => $devis['programme']->reference_generee,
            'programme' => $devis['programme'],
            'devis' => $devis['devis']
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur lors de la création du devis: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la soumission de votre devis. Veuillez réessayer.'
        ], 500);
    }
}
private function calculerQuantiteTotale($request)
{
    $quantiteTotale = 0;
    if (isset($request->quantite_colis)) {
        foreach ($request->quantite_colis as $quantite) {
            $quantiteTotale += intval($quantite);
        }
    }
    return $quantiteTotale > 0 ? $quantiteTotale : 1;
}
public function programmerDevis(Request $request, $reference)
{
    try {
        DB::beginTransaction();

        $request->validate([
            'date_programme' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ]);

        // Trouver le programme existant
        $programme = Programme::where('reference_generee', $reference)->first();

        if (!$programme) {
            return response()->json([
                'success' => false,
                'message' => 'Programme non trouvé'
            ], 404);
        }

        // Mettre à jour le programme avec la date et le chauffeur
        $programme->update([
            'date_programme' => $request->date_programme,
            'user_id' => $request->user_id,
            'etat_rdv' => 'en attente', // Changer l'état
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Récupération programmée avec succès!',
            'programme' => $programme
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur programmation devis: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Création d'une récupération après création de devis
 */
public function createRecuperationFromDevis(Request $request)
{
    try {
        DB::beginTransaction();

        $request->validate([
            'quantite' => 'required|integer|min:1',
            'date_programme' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'reference_devis' => 'required|string|max:255',
            'nom_expediteur' => 'required|string|max:255',
            'lieu_expedition' => 'required|string|max:255',
            'tel_expediteur' => 'required|string|max:20',
            'nature_du_colis' => 'required|string|max:255',
        ]);

        $referenceColis = $request->reference_devis;
        
        // Générer la référence avec -RE
        $referenceGeneree = $referenceColis . '-RE';

        // Vérifier si une récupération existe déjà pour ce devis
        $existingRecuperation = Programme::where('reference_colis', $referenceColis)
            ->where('actions_a_faire', 'recuperation')
            ->first();

        if ($existingRecuperation) {
            return response()->json([
                'success' => false,
                'message' => '❌ Une récupération pour ce devis existe déjà. Référence: ' . $existingRecuperation->reference_generee
            ], 422);
        }

        // Récupérer les informations du devis
        $devis = Devis::where('reference', $referenceColis)
            ->with('items')
            ->first();

        if (!$devis) {
            return response()->json([
                'success' => false,
                'message' => '❌ Devis non trouvé'
            ], 404);
        }

        // Création du programme de récupération
        $programme = Programme::create([
            'quantite'        => $request->quantite, // Utiliser la quantité calculée
            'date_programme'  => $request->date_programme,
            'user_id'         => $request->user_id,
            'reference_colis'   => $referenceColis,
            'reference_generee' => $referenceGeneree,
            'type_reference'    => 'devis',
            'actions_a_faire'   => 'recuperation',
            'nom_expediteur'    => $request->nom_expediteur,
            'lieu_expedition'   => $request->lieu_expedition,
            'tel_expediteur'    => $request->tel_expediteur,
            'nature_du_colis'   => $request->nature_du_colis,
            'etat_rdv'          => 'en attente',
        ]);

        // Copier les items du devis vers le programme
        if ($devis->items->count() > 0) {
            foreach ($devis->items as $originalItem) {
                ProgrammeItems::create([
                    'programme_id'      => $programme->id,
                    'devis_id'          => $originalItem->devis_id,
                    'quantite_colis'    => $originalItem->quantite_colis,
                    'service'           => $originalItem->service,
                    'valeur_colis'      => $originalItem->valeur_colis,
                    'type_colis'        => $originalItem->type_colis,
                    'description_colis' => $originalItem->description_colis,
                    'poids'             => $originalItem->poids,
                    'longueur'          => $originalItem->longueur,
                    'largeur'           => $originalItem->largeur,
                    'hauteur'           => $originalItem->hauteur,
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'success'       => true,
            'message'       => 'Récupération programmée avec succès! Référence: ' . $referenceGeneree,
            'programme'     => $programme,
            'reference_generee' => $referenceGeneree
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Erreur création récupération depuis devis: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Récupère la liste des chauffeurs
 */
public function getChauffeurs()
{
    try {
        $rawChauffeurs = User::where('agence_id', 5)
            ->where('role', 'chauffeur')
            ->where('is_active', true)
            ->get(['id', 'first_name', 'last_name']);

        $chauffeurs = $rawChauffeurs->map(function ($c) {
            $first = $c->first_name ?? $c->nom ?? ($c->name ?? '');
            $last  = $c->last_name  ?? $c->prenom ?? '';
            return [
                'id' => $c->id,
                'first_name' => trim($first),
                'last_name' => trim($last),
                'full_name' => trim($first . ' ' . $last)
            ];
        })->values();

        return response()->json([
            'success' => true,
            'chauffeurs' => $chauffeurs
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur récupération chauffeurs: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération des chauffeurs'
        ], 500);
    }
}
/**
 * Création de plusieurs récupérations en une fois
 */
public function createMultipleRecuperation(Request $request)
{
    try {
        DB::beginTransaction();

        $request->validate([
            'programmes' => 'required|array|min:1',
            'programmes.*.quantite' => 'required|integer|min:1',
            'programmes.*.nom_expediteur' => 'required|string|max:255',
            'programmes.*.lieu_expedition' => 'required|string|max:255',
            'programmes.*.tel_expediteur' => 'required|string|max:20',
            'programmes.*.nature_du_colis' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'date_programme' => 'required|date',
        ]);

        $user = auth()->user(); // Agent connecté
        $chauffeur = User::find($request->user_id);
        $createdCount = 0;
        $failedCount = 0;
        $details = [];

        foreach ($request->programmes as $index => $programmeData) {
            try {
                $typeReference = $programmeData['type_reference'] ?? 'manuel';
                $referenceColis = $programmeData['reference_input'] ?? '';
                $modificationsApportees = isset($programmeData['modifications_apportees']);

                // Variables pour stocker les informations du devis
                $modeTransit = 'aerien';
                $agenceExpedition = 'AFT Agence Louis Bleriot';
                $emailExpediteur = '';
                $devise = 'EUR';
                $agenceDestination = '';
                $prenomExpediteur = '';

                // GÉNÉRATION AUTOMATIQUE DE LA RÉFÉRENCE SI MANUEL OU VIDE
                if ($typeReference === 'manuel' || empty($referenceColis)) {
                    $initialAgent = strtoupper(substr($user->first_name, 0, 2) ?: 'AG');
                    $nomChauffeur = strtoupper(substr($chauffeur->first_name, 0, 2) ?: 'CH');
                    $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                    $referenceColis = $initialAgent . $randomNumber . $nomChauffeur;
                }
                
                // Si c'est un devis, récupérer les informations supplémentaires
                if ($typeReference === 'devis') {
                    $devis = Devis::where('reference', $referenceColis)->first();
                    if ($devis) {
                        $modeTransit = $devis->mode_transit ?? 'aerien';
                        $agenceExpedition = $devis->agence_expedition ?? 'AFT Agence Louis Bleriot';
                        $emailExpediteur = $devis->email_expediteur ?? '';
                        $devise = $devis->devise ?? 'EUR';
                        $agenceDestination = $devis->agence_destination ?? '';
                        $prenomExpediteur = $devis->prenom_expediteur ?? '';
                    }
                }
                
                // Toujours générer une référence -RE pour les récupérations
                $referenceGeneree = $referenceColis . '-RE';

                // Vérifier les doublons
                $existingProgramme = Programme::where('reference_generee', $referenceGeneree)
                    ->where('actions_a_faire', 'recuperation')
                    ->first();

                if ($existingProgramme) {
                    $details[] = [
                        'reference' => $referenceGeneree,
                        'status' => '❌ Doublon - existe déjà'
                    ];
                    $failedCount++;
                    continue;
                }
// Dans createMultipleRecuperation, après la vérification des doublons
if ($typeReference === 'devis') {
    // Vérifier si le devis existe et est à planifié
    $devisProgramme = Programme::where('reference_colis', $referenceColis)
        ->where('actions_a_faire', 'recuperation')
        ->where('etat_rdv', 'à planifié')
        ->first();
        
    if ($devisProgramme) {
        $details[] = [
            'reference' => $referenceGeneree,
            'status' => '❌ Cette référence existe déjà avec état "à planifié". Veuillez la programmer d\'abord.'
        ];
        $failedCount++;
        continue;
    }
}
                // Vérification supplémentaire pour les devis
                if ($typeReference === 'devis') {
                    $existingDevisRecuperation = Programme::where('reference_colis', $referenceColis)
                        ->where('actions_a_faire', 'recuperation')
                        ->first();

                    if ($existingDevisRecuperation) {
                        $details[] = [
                            'reference' => $referenceGeneree,
                            'status' => '❌ Doublon devis - récupération existe déjà'
                        ];
                        $failedCount++;
                        continue;
                    }
                }

                // Création du programme avec toutes les informations
                $programme = Programme::create([
                    'quantite'        => $programmeData['quantite'],
                    'date_programme'  => $request->date_programme,
                    'user_id'         => $request->user_id,
                    'agent_id'        => $user->id, // ID de l'agent connecté
                    'reference_colis'   => $referenceColis,
                    'reference_generee' => $referenceGeneree,
                    'type_reference'    => $typeReference,
                    'actions_a_faire'   => 'recuperation',
                    'nom_expediteur'    => $programmeData['nom_expediteur'],
                    'prenom_expediteur' => $prenomExpediteur,
                    'email_expediteur'  => $emailExpediteur,
                    'lieu_expedition'   => $programmeData['lieu_expedition'],
                    'tel_expediteur'    => $programmeData['tel_expediteur'],
                    'nature_du_colis'   => $programmeData['nature_du_colis'],
                    'mode_transit'      => $modeTransit,
                    'agence_expedition' => $agenceExpedition,
                    'agence_destination' => $agenceDestination,
                    'devise'            => $devise,
                    'etat_rdv'          => 'en attente',
                ]);

                Log::info("✅ Programme multiple créé avec ID: " . $programme->id . " - Référence: " . $referenceGeneree);

                // CRÉATION DES ARTICLES DANS programme_items
                $devisItemsCreated = false;
                
                // Cas 1: Type "devis" avec modifications
                if ($typeReference === 'devis' && $modificationsApportees && isset($programmeData['devis_items'])) {
                    
                    $devisItemsData = json_decode($programmeData['devis_items'], true);
                    
                    if (!empty($devisItemsData)) {
                        Log::info("📦 Création de nouveaux articles pour le programme multiple ID: " . $programme->id);

                        foreach ($devisItemsData as $itemData) {
                            ProgrammeItems::create([
                                'programme_id'      => $programme->id,
                                'quantite_colis'    => $itemData['quantite_colis'] ?? 1,
                                'service'           => $itemData['service'] ?? 'Service non spécifié',
                                'valeur_colis'      => $itemData['valeur_colis'] ?? 0,
                                'type_colis'        => $itemData['type_colis'] ?? 'Colis divers',
                                'description_colis' => $itemData['description_colis'] ?? '',
                                'poids'             => $itemData['poids'] ?? 0,
                                'longueur'          => $itemData['longueur'] ?? 0,
                                'largeur'           => $itemData['largeur'] ?? 0,
                                'hauteur'           => $itemData['hauteur'] ?? 0,
                                'montant'           => $itemData['valeur_colis'] ?? 0,
                            ]);
                        }
                        $devisItemsCreated = true;
                        Log::info("✅ " . count($devisItemsData) . " articles créés avec succès pour le programme multiple ID: " . $programme->id);
                    }
                }
                // Cas 2: Type "devis" SANS modifications - COPIER les items du devis original
                elseif ($typeReference === 'devis' && !$modificationsApportees) {
                    $devis = Devis::where('reference', $referenceColis)->first();
                    
                    if ($devis && $devis->items->count() > 0) {
                        Log::info("📦 Copie des articles du devis original pour le programme multiple ID: " . $programme->id);
                        
                        foreach ($devis->items as $originalItem) {
                            ProgrammeItems::create([
                                'programme_id'      => $programme->id,
                                'quantite_colis'    => $originalItem->quantite_colis,
                                'service'           => $originalItem->service,
                                'valeur_colis'      => $originalItem->valeur_colis,
                                'type_colis'        => $originalItem->type_colis,
                                'description_colis' => $originalItem->description_colis,
                                'poids'             => $originalItem->poids,
                                'longueur'          => $originalItem->longueur,
                                'largeur'           => $originalItem->largeur,
                                'hauteur'           => $originalItem->hauteur,
                                'montant'           => $originalItem->valeur_colis ?? 0,
                            ]);
                        }
                        $devisItemsCreated = true;
                        Log::info("✅ " . $devis->items->count() . " articles copiés du devis original pour le programme multiple");
                    }
                }
                // Cas 3: Types "depot" ou "manuel" - UTILISER les articles modifiés/ajoutés
                else {
                    // Vérifier si des articles ont été modifiés/ajoutés
                    if ($modificationsApportees && isset($programmeData['devis_items'])) {
                        $devisItemsData = json_decode($programmeData['devis_items'], true);
                        
                        if (!empty($devisItemsData)) {
                            Log::info("📦 Création d'articles personnalisés pour le programme multiple ID: " . $programme->id);

                            foreach ($devisItemsData as $itemData) {
                                ProgrammeItems::create([
                                    'programme_id'      => $programme->id,
                                    'quantite_colis'    => $itemData['quantite_colis'] ?? 1,
                                    'service'           => $itemData['service'] ?? 'Transport standard',
                                    'valeur_colis'      => $itemData['valeur_colis'] ?? 0,
                                    'type_colis'        => $itemData['type_colis'] ?? ($programmeData['nature_du_colis'] ?? 'Colis divers'),
                                    'description_colis' => $itemData['description_colis'] ?? 'Article créé pour la récupération',
                                    'poids'             => $itemData['poids'] ?? 0,
                                    'longueur'          => $itemData['longueur'] ?? 0,
                                    'largeur'           => $itemData['largeur'] ?? 0,
                                    'hauteur'           => $itemData['hauteur'] ?? 0,
                                    'montant'           => $itemData['valeur_colis'] ?? 0,
                                ]);
                            }
                            $devisItemsCreated = true;
                            Log::info("✅ " . count($devisItemsData) . " articles personnalisés créés pour le programme multiple ID: " . $programme->id);
                        }
                    } else {
                        // Si pas de modifications, créer un seul article avec les infos du formulaire
                        Log::info("📦 Création d'un article par défaut pour le programme multiple ID: " . $programme->id);
                        
                        ProgrammeItems::create([
                            'programme_id'      => $programme->id,
                            'quantite_colis'    => $programmeData['quantite'] ?? 1,
                            'service'           => 'Transport standard',
                            'valeur_colis'      => 0,
                            'type_colis'        => $programmeData['nature_du_colis'] ?? 'Colis divers',
                            'description_colis' => 'Article créé automatiquement pour la récupération',
                            'poids'             => 0,
                            'longueur'          => 0,
                            'largeur'           => 0,
                            'hauteur'           => 0,
                            'montant'           => 0,
                        ]);
                        $devisItemsCreated = true;
                    }
                }

                $createdCount++;
                $details[] = [
                    'reference' => $referenceGeneree,
                    'status' => '✅ Créé avec succès' . ($devisItemsCreated ? ' + articles' : '')
                ];

                Log::info("✅ Programme multiple traité avec succès: " . $referenceGeneree);

            } catch (\Exception $e) {
                $failedCount++;
                $details[] = [
                    'reference' => $referenceColis ?? 'N/A',
                    'status' => '❌ Erreur: ' . $e->getMessage()
                ];
                Log::error("❌ Erreur création programme multiple {$index}: " . $e->getMessage());
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Traitement des programmes terminé',
            'created_count' => $createdCount,
            'failed_count' => $failedCount,
            'details' => $details,
            'total_programmes' => count($request->programmes)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Erreur création programmes multiples: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur globale: ' . $e->getMessage()
        ], 500);
    }
}
public function destroy(Programme $programme)
{
    try {
        $programme->delete();

        return response()->json([
            'success' => true,
            'message' => 'Le programme a été supprimé avec succès.'
        ], 200);

    } catch (\Exception $e) {
        // Optionnel : enregistrer l'erreur pour le débogage
        // Log::error("Erreur de suppression du programme #{$programme->id}: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la suppression du programme.'
        ], 500);
    }
}
// Afficher la page d'édition
public function showEdit($slug)
{
    try {
        // EXTRACTION DE L'ID NUMÉRIQUE
        $id_numerique = explode('-', $slug)[0];
        
        Log::info("🔍 Tentative de chargement du programme ID: " . $id_numerique);
       
        // CORRECTION : Charger les ProgrammeItems au lieu de DevisItems
        $programme = Programme::with(['user', 'items']) // ✅ items() fait référence à ProgrammeItems
            ->where(function($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('agence_id', 5)
                      ->where('role', 'chauffeur');
                })
                ->orWhereNull('user_id');
            })
            ->whereIn('etat_rdv', ['en attente', 'à planifié'])
            ->findOrFail($id_numerique);
        
        // CORRECTION : Convertir la date en objet Carbon si c'est une string
        if ($programme->date_programme && is_string($programme->date_programme)) {
            $programme->date_programme = \Carbon\Carbon::parse($programme->date_programme);
        }
        
        Log::info("✅ Programme trouvé: " . $programme->reference_generee . " - État: " . $programme->etat_rdv);
        Log::info("📦 Nombre d'articles trouvés: " . ($programme->items ? $programme->items->count() : 0));
        
        // Récupérer les chauffeurs
        $chauffeurs = User::where('agence_id', 5)
            ->where('role', 'chauffeur')
            ->where('is_active', true)
            ->get(['id', 'first_name', 'last_name']);
        
        return view('AFT_LOUIS_BLERIOT.transport.planingedit', compact('programme', 'chauffeurs'));
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::error("❌ Programme non trouvé avec ID: " . ($id_numerique ?? 'N/A'));
        return redirect()->route('aftlb_transport.planing.chauffeur')
            ->with('error', 'Programme non trouvé ou déjà effectué');
            
    } catch (\Exception $e) {
        Log::error("❌ Erreur affichage page édition: " . $e->getMessage());
        Log::error("Stack trace: " . $e->getTraceAsString());
        
        return redirect()->route('aftlb_transport.planing.chauffeur')
            ->with('error', 'Une erreur est survenue: ' . $e->getMessage());
    }
}
// Mettre à jour un programme
public function updateProgramme(Request $request, $id)
{
    $id_numerique = explode('-', $id)[0];
    Log::info("🔄 Début de la mise à jour du programme ID: " . $id_numerique);

    try {
        $programme = Programme::findOrFail($id_numerique);

        if ($programme->etat_rdv === 'effectué') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de modifier un programme déjà effectué.'
            ], 422);
        }

        DB::beginTransaction();

        // Validation des données de base
        $request->validate([
            'quantite' => 'required|integer|min:1',
            'date_programme' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'actions_a_faire' => 'required|in:depot,recuperation,livraison',
            'nom_expediteur' => 'required|string|max:255',
            'lieu_expedition' => 'required|string|max:255',
            'tel_expediteur' => 'required|string|max:20',
            'nature_du_colis' => 'required|string|max:255',
        ]);

        // Mise à jour des informations du programme principal
        $programme->update([
            'quantite' => $request->quantite,
            'date_programme' => $request->date_programme,
            'user_id' => $request->user_id,
            'actions_a_faire' => $request->actions_a_faire,
            'nom_expediteur' => $request->nom_expediteur,
            'lieu_expedition' => $request->lieu_expedition,
            'tel_expediteur' => $request->tel_expediteur,
            'nature_du_colis' => $request->nature_du_colis,
            'etat_rdv' => 'en attente',
        ]);
        
        Log::info("✅ Programme principal ID " . $programme->id . " mis à jour.");

        // Gestion des articles
        $itemsData = null;
        
        if ($request->has('items_data') && !empty($request->items_data)) {
            $itemsData = json_decode($request->items_data, true);
            Log::info("📦 Format JSON détecté via items_data");
        } elseif ($request->has('items')) {
            // Format direct depuis le formulaire
            $itemsData = [];
            foreach ($request->items as $itemId => $itemArray) {
                $itemArray['id'] = $itemId;
                $itemsData[] = $itemArray;
            }
            Log::info("📦 Format formulaire détecté via items");
        }
        
        if (!empty($itemsData)) {
            Log::info("📦 Données des articles à traiter:", ['count' => count($itemsData)]);
            $this->updateProgrammeItemsData($programme->id, $itemsData);
        } else {
            Log::warning("⚠️ Aucune donnée d'article trouvée");
        }

        DB::commit();

        Log::info("✅✅ Mise à jour complète du programme " . $programme->reference_generee . " réussie !");

        return redirect()->route('aftlb_transport.planing.chauffeur')->with('success','programme modifier avec succes') ;   
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Erreur lors de la mise à jour du programme: " . $e->getMessage());
        Log::error("Stack trace: " . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue : ' . $e->getMessage()
        ], 500);
    }
}
// Supprimer un programme et ses items

public function updateProgrammeItems(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $id_numerique = explode('-', $id)[0];
        
        $programme = Programme::where(function($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('agence_id', 5)
                      ->where('role', 'chauffeur');
                })
                ->orWhereNull('user_id');
            })
            ->whereIn('etat_rdv', ['en attente', 'à planifié'])
            ->findOrFail($id_numerique);

        // Vérifier si le programme est déjà effectué
        if ($programme->etat_rdv === 'effectué') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de modifier les articles d\'un programme déjà effectué'
            ], 422);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'sometimes|exists:programme_items,id',
            'items.*.quantite_colis' => 'required|integer|min:1',
            'items.*.service' => 'required|string|max:255',
            'items.*.valeur_colis' => 'nullable|numeric|min:0',
            'items.*.type_colis' => 'required|string|max:255',
            'items.*.description_colis' => 'nullable|string',
            'items.*.poids' => 'nullable|numeric|min:0',
            'items.*.longueur' => 'nullable|numeric|min:0',
            'items.*.largeur' => 'nullable|numeric|min:0',
            'items.*.hauteur' => 'nullable|numeric|min:0',
        ]);

        $updatedItems = [];
        $totalQuantite = 0;

        // Mettre à jour ou créer les articles
        foreach ($request->items as $itemData) {
            if (isset($itemData['id'])) {
                // Mettre à jour l'article existant
                $item = ProgrammeItems::where('programme_id', $programme->id)
                    ->where('id', $itemData['id'])
                    ->first();

                if ($item) {
                    $item->update([
                        'quantite_colis' => $itemData['quantite_colis'],
                        'service' => $itemData['service'],
                        'valeur_colis' => $itemData['valeur_colis'] ?? 0,
                        'type_colis' => $itemData['type_colis'],
                        'description_colis' => $itemData['description_colis'] ?? null,
                        'poids' => $itemData['poids'] ?? 0,
                        'longueur' => $itemData['longueur'] ?? 0,
                        'largeur' => $itemData['largeur'] ?? 0,
                        'hauteur' => $itemData['hauteur'] ?? 0,
                    ]);
                    $updatedItems[] = $item;
                }
            } else {
                // Créer un nouvel article
                $item = ProgrammeItems::create([
                    'programme_id' => $programme->id,
                    'quantite_colis' => $itemData['quantite_colis'],
                    'service' => $itemData['service'],
                    'valeur_colis' => $itemData['valeur_colis'] ?? 0,
                    'type_colis' => $itemData['type_colis'],
                    'description_colis' => $itemData['description_colis'] ?? null,
                    'poids' => $itemData['poids'] ?? 0,
                    'longueur' => $itemData['longueur'] ?? 0,
                    'largeur' => $itemData['largeur'] ?? 0,
                    'hauteur' => $itemData['hauteur'] ?? 0,
                ]);
                $updatedItems[] = $item;
            }
            
            $totalQuantite += $itemData['quantite_colis'];
        }

        // Mettre à jour la quantité totale du programme
        $programme->update(['quantite' => $totalQuantite]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Articles mis à jour avec succès',
            'total_quantite' => $totalQuantite,
            'items_count' => count($updatedItems)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Erreur mise à jour articles: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}
// Nouvelle méthode pour gérer la mise à jour des articles
private function updateProgrammeItemsData($programmeId, $itemsData)
{
    try {
        $totalQuantite = 0;
        $existingItemIds = [];

        Log::info("📦 Début mise à jour articles pour programme: " . $programmeId);
        Log::info("📦 Nombre d'articles reçus: " . count($itemsData));
        Log::info("📦 Données complètes:", ['items' => $itemsData]);

        foreach ($itemsData as $index => $itemData) {
            Log::info("🔍 Traitement article index " . $index, ['item' => $itemData]);
            
            // Vérifier si c'est un nouvel article ou un article existant
            $isNewItem = !isset($itemData['id']) || 
                         is_null($itemData['id']) || 
                         str_starts_with(strval($itemData['id']), 'new-');
            
            if (!$isNewItem) {
                // Mettre à jour l'article existant
                $item = ProgrammeItems::where('programme_id', $programmeId)
                    ->where('id', $itemData['id'])
                    ->first();

                if ($item) {
                    Log::info("🔄 Mise à jour article existant ID: " . $item->id);
                    
                    $updateData = [
                        'quantite_colis' => $itemData['quantite_colis'] ?? 1,
                        'service' => $itemData['service'] ?? 'Service non spécifié',
                        'valeur_colis' => $itemData['valeur_colis'] ?? 0,
                        'type_colis' => $itemData['type_colis'] ?? 'standard',
                        'description_colis' => $itemData['description_colis'] ?? null,
                        'poids' => $itemData['poids'] ?? 0,
                        'longueur' => $itemData['longueur'] ?? 0,
                        'largeur' => $itemData['largeur'] ?? 0,
                        'hauteur' => $itemData['hauteur'] ?? 0,
                    ];
                    
                    Log::info("📝 Données de mise à jour:", $updateData);
                    $item->update($updateData);
                    
                    $existingItemIds[] = $item->id;
                    $totalQuantite += $itemData['quantite_colis'] ?? 1;
                    
                    Log::info("✅ Article mis à jour avec succès: " . $item->id);
                } else {
                    Log::warning("⚠️ Article non trouvé pour mise à jour ID: " . $itemData['id']);
                }
            } else {
                // Créer un nouvel article
                Log::info("🆕 Création nouvel article pour programme: " . $programmeId);
                
                $createData = [
                    'programme_id' => $programmeId,
                    'quantite_colis' => $itemData['quantite_colis'] ?? 1,
                    'service' => $itemData['service'] ?? 'Transport standard',
                    'valeur_colis' => $itemData['valeur_colis'] ?? 0,
                    'type_colis' => $itemData['type_colis'] ?? 'standard',
                    'description_colis' => $itemData['description_colis'] ?? 'Nouvel article créé',
                    'poids' => $itemData['poids'] ?? 0,
                    'longueur' => $itemData['longueur'] ?? 0,
                    'largeur' => $itemData['largeur'] ?? 0,
                    'hauteur' => $itemData['hauteur'] ?? 0,
                ];
                
                Log::info("📝 Données de création:", $createData);
                $item = ProgrammeItems::create($createData);
                
                $existingItemIds[] = $item->id;
                $totalQuantite += $itemData['quantite_colis'] ?? 1;
                
                Log::info("✅ Nouvel article créé ID: " . $item->id);
            }
        }

        // Supprimer les articles qui n'existent plus
        $itemsToDelete = ProgrammeItems::where('programme_id', $programmeId)
            ->whereNotIn('id', $existingItemIds)
            ->get();
            
        Log::info("🗑️ Articles à supprimer:", ['ids' => $itemsToDelete->pluck('id')->toArray()]);
        
        $deletedCount = ProgrammeItems::where('programme_id', $programmeId)
            ->whereNotIn('id', $existingItemIds)
            ->delete();

        Log::info("🗑️ Nombre d'articles supprimés: " . $deletedCount);

        // Mettre à jour la quantité totale du programme
        Programme::where('id', $programmeId)->update(['quantite' => $totalQuantite]);

        Log::info("📊 Quantité totale mise à jour: " . $totalQuantite);
        Log::info("✅ Fin mise à jour articles - Total: " . count($existingItemIds) . " articles");

        return $totalQuantite;

    } catch (\Exception $e) {
        Log::error("❌ Erreur dans updateProgrammeItemsData: " . $e->getMessage());
        Log::error("Stack trace: " . $e->getTraceAsString());
        throw $e;
    }
}
public function deleteProgrammeItem($programmeId, $itemId)
{
    try {
        $programme = Programme::where(function($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('agence_id', 5)
                      ->where('role', 'chauffeur');
                })
                ->orWhereNull('user_id');
            })
            ->whereIn('etat_rdv', ['en attente', 'à planifié'])
            ->findOrFail($programmeId);

        $item = ProgrammeItems::where('programme_id', $programme->id)
            ->where('id', $itemId)
            ->firstOrFail();

        $item->delete();

        // Recalculer la quantité totale
        $totalQuantite = ProgrammeItems::where('programme_id', $programme->id)->sum('quantite_colis');
        $programme->update(['quantite' => $totalQuantite]);

        return response()->json([
            'success' => true,
            'message' => 'Article supprimé avec succès',
            'total_quantite' => $totalQuantite
        ]);

    } catch (\Exception $e) {
        Log::error("❌ Erreur suppression article: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression'
        ], 500);
    }
}
public function showDepotPage()
{
    return view('AFT_LOUIS_BLERIOT.transport.depot');
}

/**
 * Affiche la page de création de récupération.
 */
public function showRecuperationPage()
{
    return view('AFT_LOUIS_BLERIOT.transport.recuperation');
}

/**
 * Affiche la page de création de livraison (à venir).
 */
public function showLivraisonPage()
{
    // Pour l'instant, vous pouvez créer une vue simple pour celle-ci.
    return view('AFT_LOUIS_BLERIOT.transport.livraison');
}
}