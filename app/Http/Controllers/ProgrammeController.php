<?php
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
class ProgrammeController extends Controller
{
    public function index()
    {
        return view('admin.transport.planing');
    }

    public function data()
    {
        // Récupération des chauffeurs (tous, sans filtre agence pour admin)
        $rawChauffeurs = User::where('role', 'chauffeur')
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

        // Récupération des programmes (tous, sans filtre agence pour admin)
        $programmes = Programme::with(['user', 'devis'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($programme) {
                $programme->reference_a_afficher = $programme->reference_generee ?: $programme->reference_colis;

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

        // Dépôts pour récupération
        $depotsPourRecuperation = Programme::where('actions_a_faire', 'depot')
            ->where('etat_rdv', 'effectué')
            ->whereNotNull('reference_generee')
            ->whereNotIn('reference_generee', Programme::where('actions_a_faire', 'recuperation')->pluck('reference_colis')->toArray())
            ->pluck('reference_generee');

        return response()->json([
            'chauffeurs' => $chauffeurs,
            'programmes' => $programmes,
            'devisReferences' => $devisConfirmes,
            'depotsPourRecuperation' => $depotsPourRecuperation,
        ]);
    }

    /**
     * Pages dédiées
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

            // Générer la référence pour admin
            $referenceGeneree = $this->generateReferenceDepotAdmin($user, $chauffeur);

            // Vérifier les doublons
            $existingProgramme = Programme::where('reference_generee', $referenceGeneree)
                ->where('actions_a_faire', 'depot')
                ->first();

            if ($existingProgramme) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Un dépôt avec cette référence existe déjà.'
                ], 422);
            }

            // Création du programme de dépôt admin
            $programme = Programme::create([
                'quantite' => $request->quantite,
                'date_programme' => $request->date_programme,
                'user_id' => $request->user_id,
                'agent_id' => $user->id,
                'reference_generee' => $referenceGeneree,
                'type_reference' => 'generee',
                'actions_a_faire' => 'depot',
                'nom_expediteur' => $request->nom_expediteur,
                'lieu_expedition' => $request->lieu_expedition,
                'tel_expediteur' => $request->tel_expediteur,
                'nature_du_colis' => $request->nature_du_colis,
                'mode_transit' => 'aerien',
                'agence_expedition' => 'Admin Central',
                'devise' => 'EUR',
                'etat_rdv' => 'en attente',
                'is_admin' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dépôt créé avec succès!',
                'reference_generee' => $referenceGeneree,
                'programme' => $programme
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création dépôt admin: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du dépôt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Création de dépôt multiple pour admin (EXISTE DÉJÀ DANS VOTRE CODE)
     * Cette méthode existe déjà dans votre contrôleur, je la laisse pour référence
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
                    // Générer la référence pour admin
                    $referenceGeneree = $this->generateReferenceDepotAdmin($user, $chauffeur);

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

                    // Création du programme de dépôt admin
                    $programme = Programme::create([
                        'quantite' => $programmeData['quantite'],
                        'date_programme' => $request->date_programme,
                        'user_id' => $request->user_id,
                        'agent_id' => $user->id,
                        'reference_generee' => $referenceGeneree,
                        'type_reference' => 'generee',
                        'actions_a_faire' => 'depot',
                        'nom_expediteur' => $programmeData['nom_expediteur'],
                        'lieu_expedition' => $programmeData['lieu_expedition'],
                        'tel_expediteur' => $programmeData['tel_expediteur'],
                        'nature_du_colis' => $programmeData['nature_du_colis'],
                        'mode_transit' => 'aerien',
                        'agence_expedition' => 'Admin Central',
                        'devise' => 'EUR',
                        'etat_rdv' => 'en attente',
                        'is_admin' => true,
                    ]);

                    $createdCount++;
                    $details[] = [
                        'reference' => $referenceGeneree,
                        'status' => '✅ Créé avec succès'
                    ];

                } catch (\Exception $e) {
                    $failedCount++;
                    $details[] = [
                        'reference' => 'N/A',
                        'status' => '❌ Erreur: ' . $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traitement des dépôts admin terminé',
                'created_count' => $createdCount,
                'failed_count' => $failedCount,
                'details' => $details,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création dépôts multiples admin: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur globale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génération de référence pour admin (EXISTE DÉJÀ DANS VOTRE CODE)
     */
    private function generateReferenceDepotAdmin($user, $chauffeur)
    {
        $initialAdmin = strtoupper(substr($user->first_name, 0, 2) ?: 'AD');
        $nomChauffeur = strtoupper(substr($chauffeur->first_name, 0, 2) ?: 'CH');
        $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return $initialAdmin . $randomNumber . $nomChauffeur;
    }

    /**
     * Page dédiée aux dépôts
     */
    public function showDepotPage()
    {
        return view('admin.transport.depot');
    }
    public function showRecuperationPage()
    {
        return view('admin.transport.recuperation');
    }

    public function showLivraisonPage()
    {
        return view('admin.transport.livraison');
    }

    public function ajoutDevis()
    {
        return view('admin.transport.ajoutdevis');
    }

    /**
     * Récupère les chauffeurs
     */
    public function getChauffeurs()
    {
        try {
            $rawChauffeurs = User::where('role', 'chauffeur')
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
            Log::error("Erreur récupération chauffeurs admin: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des chauffeurs'
            ], 500);
        }
    }
    public function store_devis(Request $request)
    {
        \Log::info('Données reçues admin:', $request->all());

            try {
            $devis = DB::transaction(function () use ($request) {
                // 1. Génération de référence pour admin
                $initialNom = mb_substr($request->nom_expediteur, 0, 1);
                $initialPrenom = mb_substr($request->prenom_expediteur, 0, 1);
                $initiales = strtoupper($initialNom . $initialPrenom);

                do {
                    $randomNumber = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $reference = 'AD' . $randomNumber . $initiales; // AD pour Admin
                } while (Devis::where('reference', $reference)->exists());

                // 2. Création du programme avec référence -RE
                $referenceGeneree = $reference . '-RE';

                // Vérifier si une récupération existe déjà
                $existingProgramme = Programme::where('reference_generee', $referenceGeneree)->first();
                if ($existingProgramme) {
                    throw new \Exception('Une récupération avec cette référence existe déjà.');
                }

                // 3. Création du programme
                $programme = Programme::create([
                    'quantite' => $this->calculerQuantiteTotale($request),
                    'date_programme' => null,
                    'user_id' => null,
                    'agent_id' => auth()->id(),
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
                    'is_admin' => true, // Marquer comme créé par admin
                ]);

                dd($programme);

                // 4. Créer les items du programme
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

                // 5. Création dans la table devis
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
                    'user_id' => auth()->id(),
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

            return response()->json([
                'success' => true,
                'message' => 'Programme enregistré !',
                'reference_generee' => $devis['programme']->reference_generee,
                'programme' => $devis['programme'],
                'devis' => $devis['devis']
            ]);

        } catch (\Exception $e) {
            dd( $e);
            Log::error('Erreur lors de la création du devis admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la soumission de votre devis. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Programmation d'un devis pour admin
     */
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
                'etat_rdv' => 'en attente',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Récupération programmée avec succès!',
                'programme' => $programme
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur programmation devis admin: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

   
   
   

    /**
     * Calcul de la quantité totale
     */
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

            $referenceColis = $request->reference_input;
            $modificationsApportees = $request->has('modifications_apportees');

            // Génération de la référence
            $referenceGeneree = null;
            if ($request->type_reference === 'manuel' && empty($referenceColis)) {
                $user = auth()->user();
                $chauffeur = User::find($request->user_id);
                $initialAdmin = strtoupper(substr($user->first_name, 0, 2) ?: 'AD');
                $nomChauffeur = strtoupper(substr($chauffeur->first_name, 0, 2) ?: 'CH');
                $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $referenceColis = $initialAdmin . $randomNumber . $nomChauffeur;
            }
            
            // Toujours générer une référence -RE pour les récupérations
            $referenceGeneree = $referenceColis . '-RE';

            // Vérifier les doublons
            $existingProgramme = Programme::where('reference_generee', $referenceGeneree)
                ->where('actions_a_faire', 'recuperation')
                ->first();

            if ($existingProgramme) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Une récupération avec la référence "' . $referenceGeneree . '" existe déjà. Impossible de créer un doublon.'
                ], 422);
            }

            // Vérification supplémentaire pour les devis
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
                'is_admin'          => true, // Marquer comme créé par admin
            ]);

            Log::info("✅ Programme admin créé avec ID: " . $programme->id . " - Référence: " . $referenceGeneree);

            // CRÉATION DES ARTICLES
            $devisItemsCreated = false;
            
            // Cas 1: Type "devis" avec modifications
            if ($request->type_reference === 'devis' && $modificationsApportees && $request->has('devis_items')) {
                
                $devisItemsData = json_decode($request->devis_items, true);
                
                if (!empty($devisItemsData)) {
                    Log::info("📦 Création de nouveaux articles pour le programme admin ID: " . $programme->id);

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
                    Log::info("✅ " . count($devisItemsData) . " articles créés avec succès pour le programme admin ID: " . $programme->id);
                }
            }
            // Cas 2: Type "devis" SANS modifications - COPIER les items du devis original
            elseif ($request->type_reference === 'devis' && !$modificationsApportees) {
                $devis = Devis::where('reference', $referenceColis)->first();
                
                if ($devis && $devis->items->count() > 0) {
                    Log::info("📦 Copie des articles du devis original pour le programme admin ID: " . $programme->id);
                    
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
                        Log::info("📦 Création d'articles personnalisés pour le programme admin ID: " . $programme->id);

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
                        Log::info("✅ " . count($devisItemsData) . " articles personnalisés créés pour le programme admin ID: " . $programme->id);
                    }
                } else {
                    // Si pas de modifications, créer un seul article avec les infos du formulaire
                    Log::info("📦 Création d'un article par défaut pour le programme admin ID: " . $programme->id);
                    
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
            Log::error("❌ Erreur création récupération admin: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
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

            $user = auth()->user();
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
                    $agenceExpedition = 'Admin Central';
                    $emailExpediteur = '';
                    $devise = 'EUR';
                    $agenceDestination = '';
                    $prenomExpediteur = '';

                    // GÉNÉRATION AUTOMATIQUE DE LA RÉFÉRENCE SI MANUEL OU VIDE
                    if ($typeReference === 'manuel' || empty($referenceColis)) {
                        $initialAdmin = strtoupper(substr($user->first_name, 0, 2) ?: 'AD');
                        $nomChauffeur = strtoupper(substr($chauffeur->first_name, 0, 2) ?: 'CH');
                        $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                        $referenceColis = $initialAdmin . $randomNumber . $nomChauffeur;
                    }
                    
                    // Si c'est un devis, récupérer les informations supplémentaires
                    if ($typeReference === 'devis') {
                        $devis = Devis::where('reference', $referenceColis)->first();
                        if ($devis) {
                            $modeTransit = $devis->mode_transit ?? 'aerien';
                            $agenceExpedition = $devis->agence_expedition ?? 'Admin Central';
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
                        'agent_id'        => $user->id,
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
                        'is_admin'          => true,
                    ]);

                    Log::info("✅ Programme multiple admin créé avec ID: " . $programme->id . " - Référence: " . $referenceGeneree);

                    // CRÉATION DES ARTICLES DANS programme_items
                    $devisItemsCreated = false;
                    
                    // Cas 1: Type "devis" avec modifications
                    if ($typeReference === 'devis' && $modificationsApportees && isset($programmeData['devis_items'])) {
                        
                        $devisItemsData = json_decode($programmeData['devis_items'], true);
                        
                        if (!empty($devisItemsData)) {
                            Log::info("📦 Création de nouveaux articles pour le programme multiple admin ID: " . $programme->id);

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
                            Log::info("✅ " . count($devisItemsData) . " articles créés avec succès pour le programme multiple admin ID: " . $programme->id);
                        }
                    }
                    // Cas 2: Type "devis" SANS modifications - COPIER les items du devis original
                    elseif ($typeReference === 'devis' && !$modificationsApportees) {
                        $devis = Devis::where('reference', $referenceColis)->first();
                        
                        if ($devis && $devis->items->count() > 0) {
                            Log::info("📦 Copie des articles du devis original pour le programme multiple admin ID: " . $programme->id);
                            
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
                            Log::info("✅ " . $devis->items->count() . " articles copiés du devis original pour le programme multiple admin");
                        }
                    }
                    // Cas 3: Types "depot" ou "manuel" - UTILISER les articles modifiés/ajoutés
                    else {
                        // Vérifier si des articles ont été modifiés/ajoutés
                        if ($modificationsApportees && isset($programmeData['devis_items'])) {
                            $devisItemsData = json_decode($programmeData['devis_items'], true);
                            
                            if (!empty($devisItemsData)) {
                                Log::info("📦 Création d'articles personnalisés pour le programme multiple admin ID: " . $programme->id);

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
                                Log::info("✅ " . count($devisItemsData) . " articles personnalisés créés pour le programme multiple admin ID: " . $programme->id);
                            }
                        } else {
                            // Si pas de modifications, créer un seul article avec les infos du formulaire
                            Log::info("📦 Création d'un article par défaut pour le programme multiple admin ID: " . $programme->id);
                            
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

                    Log::info("✅ Programme multiple admin traité avec succès: " . $referenceGeneree);

                } catch (\Exception $e) {
                    $failedCount++;
                    $details[] = [
                        'reference' => $referenceColis ?? 'N/A',
                        'status' => '❌ Erreur: ' . $e->getMessage()
                    ];
                    Log::error("❌ Erreur création programme multiple admin {$index}: " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traitement des programmes admin terminé',
                'created_count' => $createdCount,
                'failed_count' => $failedCount,
                'details' => $details,
                'total_programmes' => count($request->programmes)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Erreur création programmes multiples admin: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur globale: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getReferenceInfo($reference)
    {
        try {
            Log::info("Recherche référence admin: " . $reference);
    
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
    
            // Rechercher les dépôts effectués
            $depot = Programme::where('reference_generee', $reference)
                ->where('actions_a_faire', 'depot')
                ->where('etat_rdv', 'effectué')
                ->with('items')
                ->first();
    
            if ($depot) {
                $estEffectue = $depot->etat_rdv === 'effectué';
                
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
                        'items' => $itemsData
                    ],
                    'message' => $estEffectue ? 
                        '✅ Dépôt effectué trouvé - Récupération possible' : 
                        '⚠️ Dépôt trouvé mais pas encore effectué'
                ]);
            }
    
            // Référence non trouvée - considérée comme manuelle
            return response()->json([
                'type' => 'manuel',
                'existe' => false,
                'valide' => false,
                'data' => null,
                'message' => 'ℹ️ Référence manuelle - Veuillez remplir les informations'
            ]);
    
        } catch (\Exception $e) {
            Log::error("Erreur getReferenceInfo admin: " . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'existe' => false,
                'valide' => false
            ], 500);
        }
    }
    public function showEdit($slug)
    {
        try {
            // Extraire l'ID du slug (format: "123-aft-louis-b")
            $id = intval(explode('-', $slug)[0]);
            
            $programme = Programme::with(['items', 'user'])->findOrFail($id);
            $chauffeurs = User::where('role', 'chauffeur')
                ->where('is_active', true)
                ->get(['id', 'first_name', 'last_name']);

            return view('admin.transport.planingedit', compact('programme', 'chauffeurs'));
            
        } catch (\Exception $e) {
            Log::error("Erreur showEdit: " . $e->getMessage());
            abort(404, 'Programme non trouvé');
        }
    }

    /**
     * Mettre à jour un programme
     */
    public function updateProgramme(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $programme = Programme::findOrFail($id);

            // Validation des données de base
            $request->validate([
                'date_programme' => 'nullable|date',
                'user_id' => 'nullable|exists:users,id',
                'actions_a_faire' => 'required|in:depot,recuperation,livraison',
                'nom_expediteur' => 'required|string|max:255',
                'tel_expediteur' => 'required|string|max:20',
                'lieu_expedition' => 'required|string',
                'nature_du_colis' => 'required|string|max:255',
                'quantite' => 'required|integer|min:1',
            ]);

            // Mettre à jour le programme
            $programme->update([
                'date_programme' => $request->date_programme,
                'user_id' => $request->user_id,
                'actions_a_faire' => $request->actions_a_faire,
                'nom_expediteur' => $request->nom_expediteur,
                'tel_expediteur' => $request->tel_expediteur,
                'lieu_expedition' => $request->lieu_expedition,
                'nature_du_colis' => $request->nature_du_colis,
                'quantite' => $request->quantite,
            ]);

            // Traitement des articles
            if ($request->has('items_data')) {
                $itemsData = json_decode($request->items_data, true);
                
                foreach ($itemsData as $itemData) {
                    if (isset($itemData['id']) && $itemData['id']) {
                        // Mettre à jour l'article existant
                        ProgrammeItems::where('id', $itemData['id'])
                            ->where('programme_id', $programme->id)
                            ->update([
                                'service' => $itemData['service'],
                                'type_colis' => $itemData['type_colis'],
                                'quantite_colis' => $itemData['quantite_colis'],
                                'valeur_colis' => $itemData['valeur_colis'] ?? 0,
                                'poids' => $itemData['poids'] ?? 0,
                                'longueur' => $itemData['longueur'] ?? 0,
                                'largeur' => $itemData['largeur'] ?? 0,
                                'hauteur' => $itemData['hauteur'] ?? 0,
                                'description_colis' => $itemData['description_colis'] ?? '',
                            ]);
                    } else {
                        // Créer un nouvel article
                        ProgrammeItems::create([
                            'programme_id' => $programme->id,
                            'service' => $itemData['service'],
                            'type_colis' => $itemData['type_colis'],
                            'quantite_colis' => $itemData['quantite_colis'],
                            'valeur_colis' => $itemData['valeur_colis'] ?? 0,
                            'poids' => $itemData['poids'] ?? 0,
                            'longueur' => $itemData['longueur'] ?? 0,
                            'largeur' => $itemData['largeur'] ?? 0,
                            'hauteur' => $itemData['hauteur'] ?? 0,
                            'description_colis' => $itemData['description_colis'] ?? '',
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Programme mis à jour avec succès!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur updateProgramme: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un article d'un programme
     */
    public function deleteProgrammeItem($programmeId, $itemId)
    {
        try {
            $item = ProgrammeItems::where('id', $itemId)
                ->where('programme_id', $programmeId)
                ->firstOrFail();

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Article supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur deleteProgrammeItem: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
    public function edit(Programme $programme) // Étape 1: Utiliser le Route-Model Binding de Laravel
    {
        // Étape 2: Charger la relation avec le chauffeur pour être sûr qu'elle est incluse dans le JSON
        $programme->load('chauffeur');
        
        // Étape 3: Récupérer la liste de TOUS les chauffeurs pour le menu déroulant du modal
        $chauffeurs = Chauffeur::all();
        
        // Étape 4: Retourner la réponse JSON avec les variables maintenant définies
        return response()->json([
            'programme' => $programme,
            'chauffeurs' => $chauffeurs
        ]);
    }

    public function update(Request $request, Programme $programme)
{
    $rules = [
        'date_programme' => 'nullable|date',
        'chauffeur_id' => 'nullable|exists:chauffeurs,id', 
        'actions_a_faire' => 'nullable|in:depot,recuperation,livraison',
    ];

    $request->validate($rules);

    // Vérifier si la référence du colis a été modifiée
    if ($request->has('reference_colis') && $request->reference_colis != $programme->reference_colis) {
        $programmeExistant = Programme::where('reference_colis', $request->reference_colis)
            ->where('id', '!=', $programme->id)
            ->first();

        if ($programmeExistant) {
            return response()->json([
                'success' => false,
                'message' => "Le colis avec la référence {$request->reference_colis} est déjà attribué"
            ], 422);
        }
    }

    // Mettre à jour uniquement les champs fournis
    $updated = false;
    
    if ($request->has('date_programme') && $request->date_programme != $programme->date_programme) {
        $programme->date_programme = $request->date_programme;
        $updated = true;
    }
    if ($request->has('chauffeur_id') && $request->chauffeur_id != $programme->chauffeur_id) {
        $programme->chauffeur_id = $request->chauffeur_id;
        $updated = true;
    }
    if ($request->has('nature_du_colis')) {
        $programme->nature_du_colis = $request->nature_du_colis;
        $updated = true;
    }
    
    if ($request->has('actions_a_faire') && $request->actions_a_faire != $programme->actions_a_faire) {
        $programme->actions_a_faire = $request->actions_a_faire;
        $updated = true;
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
public function destroy(Programme $programme)
{
    try {
        $programme->delete();
        return response()->json(['success' => true, 'message' => 'Programme supprimé avec succès']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
    }
}
    public function exportPDF()
{
    $programmes = Programme::with('chauffeur')
        ->orderByDesc('date_programme')
        ->get()
        ->map(function ($programme) {
            $programme->Adresse_expedition = $programme->lieu_expedition;
            $programme->Adresse_destination = $programme->lieu_destination;
            return $programme;
        });

    $pdf = PDF::loadView('admin.Programme.pdf', compact('programmes'));
    return $pdf->download('programmes-list.pdf');
    
}
}