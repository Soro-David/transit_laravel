<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Devis; // Assurez-vous d'importer le modèle Devis
use App\Models\DevisItems; // Assurez-vous d'importer le modèle DevisItem si vous en avez besoin pour des détails
// use SimpleSoftwareIO\QrCode\Facades\QrCode; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Les_colis;
use App\Models\Colis;
use App\Models\Agent;
// use App\Models\Bateaux;
use App\Models\Bateaux;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Article;
use Endroid\QrCode\QrCode;

use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Infobip\Api\SmsApi;
use Infobip\Configuration;
use Infobip\Models\SmsAdvancedTextualRequest;
use Infobip\Models\SmsDestination;
use Infobip\Models\SmsTextualMessage;
use App\Services\InfobipService; 
use Barryvdh\DomPDF\Facade;
use PDF;
use Illuminate\Support\Collection; 
use Illuminate\Support\Facades\Mail;
use App\Mail\ColisValidatedMail;

use App\Services\InfobipSmsService;
use App\Services\InfobipEmailService;



class AftlbColisController extends Controller
{

    public function liste_ballon()
    {
        // Récupérer uniquement les bateaux non récupérés
        $ballons = Bateaux::select('id', 'reference_bateau', 'date_arriver', 'reference_conteneur')
                        ->where('recuperer', '!=', 'oui')
                        ->where('agence_destination', 'AFT Agence Louis Bleriot')
                        ->where('type', '=', 'ballon')
                        ->get();
        return view('AFT_LOUIS_BLERIOT.ballon.liste_ballon', compact('ballons'));
    }

    public function store_bateaux(Request $request)
    {
        // dd($request->all());
        try {
            // Validation des données
            $request->validate([
                'reference_bateau' => 'required|unique:bateaux,reference_bateau',
                'reference_conteneur' => 'required',
                'type' => 'required',
                'date_arrive' => 'required|date',
                'compagnie' => 'required|string',
                'agence_destination' => 'required|string',
                // 'agence_expedition' => 'required|string',
            ],
            [
                'reference_bateau.required' => 'La référence du bateau est obligatoire.',
                'reference_bateau.unique' => 'La référence du bateau doit être unique.',
                'reference_conteneur.required' => 'La référence du conteneur est obligatoire.',
                'type.required' => 'Le type de véhicule est obligatoire.',
                'date_arrive.required' => 'La date d\'arrivée est obligatoire.',
                'date_arrive.date' => 'La date d\'arrivée doit être une date valide.',
                'compagnie.required' => 'Le nom de la compagnie est obligatoire.',
                'compagnie.string' => 'Le nom de la compagnie doit être une chaîne de caractères.',
                'agence_destination.required' => 'L\'agence de destination est obligatoire.',
                'agence_destination.string' => 'L\'agence de destination doit être une chaîne de caractères.',
            ]);

            // dd($request);
            // Création du bateau
            $bateau = Bateaux::create([
                'reference_bateau' => $request->reference_bateau,
                'reference_conteneur' => $request->reference_conteneur,
                'type' => $request->type,
                'date_arriver' => $request->date_arrive,
                'compagnie' => $request->compagnie,
                'agence_destination' => $request->agence_destination,
                'agence_expedition' => $request->agence_expedition,
                'nom_bateau' => $request->nom_bateau ?? null,
                'numero_bateau' => $request->numero_bateau ?? null,
                'nom_ballon' => $request->nom_ballon ?? null,
                'numero_ballon' => $request->numero_ballon ?? null,
            ]);

            return redirect()->back()->with('success', 'Bateau créé avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite : ' . $e->getMessage())->withInput();
        }
    }

    public function autocompleteProduit(Request $request)
    {
        $query = $request->get('query');
        $produits = Produit::where('categorie', 'Colis')
            ->where('description', 'like', '%' . $query . '%')
            ->limit(15)
            ->get(['id', 'description', 'prix']);

        return response()->json($produits);
    }

    public function autocompleteService(Request $request)
    {
        $query = $request->get('query');
        $services = Produit::where('categorie', 'Service')
            ->where('description', 'like', '%' . $query . '%')
            ->limit(15)
            ->get(['id', 'description', 'prix']);

        return response()->json($services);
    }

    public function storeProduit(Request $request)
    {
        $messages = [
            'description.required' => 'La description est requise.',
            'categorie.required' => 'Le champ catégorie est requis.',
            'prix.required' => 'Le prix est requis.',
            'agence.required' => 'L\'agence est requise.',
        ];

        $request->validate([
            'description' => 'required|string|max:255',
            'categorie' => 'required|string|in:Colis',
            'prix' => 'required|numeric|min:0',
            'agence' => 'required|string|max:255',
        ], $messages);

        Produit::create($request->all());

        return response()->json(['message' => 'Produit ajouté avec succès !'], 201);
    }

    public function storeService(Request $request)
    {
        $messages = [
            'description.required' => 'La description est requise.',
            'categorie.required' => 'Le champ catégorie est requis.',
            'prix.required' => 'Le prix est requis.',
            'agence.required' => 'L\'agence est requise.',
        ];

        $request->validate([
            'description' => 'required|string|max:255',
            'categorie' => 'required|string|in:Service',
            'prix' => 'required|numeric|min:0',
            'agence' => 'required|string|max:255',
        ], $messages);

        Produit::create($request->all());

        return response()->json(['message' => 'Service ajouté avec succès !'], 201);
    }


    public function create(Request $request)
    {
        // Récupération des agences et des clients
        $agences = Agence::select('nom_agence', 'id')->get();
        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        // Redirection vers la vue
        return view('AFT_LOUIS_BLERIOT.colis.add', compact('agences', 'client_expediteurs', 'client_destinataires'));
    }

    private function generateParcelReference()
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception("Utilisateur non connecté.");
        }

        $firstLetterNom = strtoupper(substr($user->last_name, 0, 1));
        $firstLetterPrenom = strtoupper(substr($user->first_name, 0, 1));
        $monthLetter = strtoupper(now()->format('F')[0]); // Première lettre du mois en anglais ('J' pour January, 'F' for February...)

        $increment = 1;
        $baseReference = "{$firstLetterNom}{$firstLetterPrenom}-{$monthLetter}-{$increment}";

        // Boucle pour trouver la première référence non utilisée pour cette combinaison utilisateur/mois
        while (DB::table('colis')->where('reference_colis', $baseReference)->exists()) {
            $increment++;
            $baseReference = "{$firstLetterNom}{$firstLetterPrenom}-{$monthLetter}-{$increment}";
        }

        return $baseReference; 
    }
   


    private function generateReferenceContenaire()
    {
        $agence = 'AFT Agence Louis Bleriot';
        // Récupérer la dernière référence enregistrée
        $lastReference = DB::table('colis')
            ->where('agence', $agence)
            ->whereNotNull('reference_contenaire')
            ->orderByDesc('id')
            ->value('reference_contenaire');

        if ($lastReference) {
            // Extraire le numéro (tout ce qui vient après "TC")
            $lastNumber = (int) str_replace('TC', '', $lastReference);
            $newNumber = $lastNumber + 1;
        } else {
            // Premier conteneur
            $newNumber = 1;
        }

        return "TC" . $newNumber;
    }


    private function generateReferenceVol()
    {
        // Récupérer la dernière référence enregistrée
        $agence = 'AFT Agence Louis Bleriot';
        $lastReference = DB::table('colis')
            ->where('agence', $agence)
            ->whereNotNull('reference_vol')
            ->orderByDesc('id')
            ->value('reference_vol');

        if ($lastReference) {
            // Extraire le numéro (tout ce qui vient après "A")
            $lastNumber = (int) str_replace('A', '', $lastReference);
            $newNumber = $lastNumber + 1;
        } else {
            // Premier vol
            $newNumber = 1;
        }

        return "A" . $newNumber;
    }

private function generateReferenceParMode(string $mode_transit)
{
    $user = Auth::user();
    if (!$user) {
        throw new \Exception("Utilisateur non connecté.");
    }

    $agence = 'AFT Agence Louis Bleriot'; // ou récupérer dynamiquement si nécessaire
    // Initiales de l'utilisateur (ex: SE)
    $initiales = strtoupper(
        substr($user->last_name ?? 'X', 0, 1) .
        substr($user->first_name ?? 'X', 0, 1)
    );

    // Vérifier le dernier colis pour ce mode
    $dernierColis = DB::table('colis')
        ->where('mode_transit', $mode_transit)
        ->where('agence', $agence)
        ->orderByDesc('id')
        ->first();


        // dd($dernierColis);
    if ($dernierColis && $dernierColis->etat === 'Fermé') {
        // Si le dernier est fermé => reset
        $nextIdRef = 1;
    } else {
        // Sinon on continue l'incrémentation
        $lastColis = DB::table('colis')
            ->where('mode_transit', $mode_transit)
            ->where('agence', $agence)
            ->orderByDesc('id') 
            ->first();

        $lastIdRef = $lastColis ? $lastColis->id_reference : null;

        $nextIdRef = ($lastIdRef ?? 0) + 1;

    }

    // Déterminer la bonne référence conteneur/vol
    if ($mode_transit === 'maritime') {
        $contenaireRef = DB::table('colis')
            ->where('mode_transit', $mode_transit)
            ->where('agence', $agence)
            ->where('etat', '!=', 'Fermé')
            ->orderByDesc('id')
            ->value('reference_contenaire') ?? $this->generateReferenceContenaire();
    } elseif ($mode_transit === 'aerien') {
        $contenaireRef = DB::table('colis')
            ->where('mode_transit', $mode_transit)
            ->where('agence', $agence)
            ->where('etat', '!=', 'Fermé')
            ->orderByDesc('id')
            ->value('reference_vol') ?? $this->generateReferenceVol();
    } else {
        throw new \Exception("Mode de transit invalide : $mode_transit");
    }

    // Format final de la référence
    $numero = str_pad($nextIdRef, 4, '0', STR_PAD_LEFT);
    $contenaireRef = is_array($contenaireRef) ? ($contenaireRef[0] ?? 'UNKNOWN') : $contenaireRef;
    $reference = "{$initiales}-{$numero}-{$contenaireRef}";

    return [
        'reference_colis' => $reference,
        'id_reference' => $nextIdRef,
        'reference_contenaire' => $contenaireRef
    ];
}


    public function genererReferenceSelonMode($mode)
    {
        try {
            $ref = $this->generateReferenceParMode($mode);
            return response()->json($ref);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function add_colis(Request $request)
    {
        $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->distinct()->pluck('pays_agence');
        $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
        $agencesExpedition = Agence::where('nom_agence', 'AFT Agence Louis Bleriot')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();
        // dd($agences);
        // Génère juste les références, sans enregistrer encore dans la base
        // $referenceColis = $this->generateReferenceParMode();
        $referenceColis_maritime = $this->generateReferenceParMode('maritime');
        $referenceColis_aerien = $this->generateReferenceParMode('aerien');
        // dd($referenceColis_aerien, $referenceColis_maritime);
        return view('AFT_LOUIS_BLERIOT.colis.add_colis', compact(
            'agencesExpedition', 'agencesDestination', 'paysUniques', 'referenceColis_maritime','referenceColis_aerien'
        ));
    }

    
    
    
    private function getOrCreateContenaireReference()
    {
        $alphabet = range('A', 'Z');
        $maxPerContenaire = 50; // Exemple : 50 colis par conteneur
        foreach ($alphabet as $letter) {
            for ($i = 1; $i <= 5; $i++) {
                $reference = "{$letter}{$i}";

                // Compter combien de colis ont ce conteneur
                $count = DB::table('colis')
                    ->where('reference_contenaire', $reference)
                    ->count();

                if ($count < $maxPerContenaire) {
                    return $reference;
                }
            }
        }

        throw new \Exception("Plus de références de contenaires disponibles.");
    }


    public function contenaire_fermer(Request $request)
    {
    
        // dd($request);
        try {
            // Démarrez une transaction de base de données pour garantir l'atomicité
            DB::beginTransaction();
    
            $agence = 'AFT Agence Louis Bleriot';

            $colis = Colis::where('etat', 'Chargé')
                ->where('mode_transit', 'maritime')
                ->whereHas('expediteur', function ($query) use ($agence) {
                    $query->where('agence', $agence);
                })
                ->get();
    
            $count = $colis->count();
            if ($count === 0) {
                return redirect()->back()->with('warning', 'Aucun colis avec l’état Chargé et un mode de transit Maritime.');
            }
    
            // Générer une référence unique pour le conteneur
            $referenceContenaire = $this->generateReferenceContenaire();
    
            // Mise à jour des enregistrements
            $updatedCount = Colis::where('etat', 'Chargé')
            ->where('mode_transit', 'maritime')
            ->whereHas('expediteur', function ($q) use ($agence) {
                $q->where('agence', $agence);
            })
            ->update([
                'etat'               => 'Fermé',
                'reference_contenaire' => $referenceContenaire,
            ]);
        
    
            // Valider que la mise à jour a affecté le nombre attendu d'enregistrements
            if ($updatedCount !== $count) {
                DB::rollBack(); // Annulez la transaction si la mise à jour n'est pas cohérente
                return redirect()->back()->with('error', 'Erreur lors de la mise à jour des colis. Veuillez réessayer.');
            }
    
            // Commit la transaction
            DB::commit();
    
            // Retourner un message de succès avec le nombre de colis traités
            return redirect()->back()->with('success', "$updatedCount colis ont été enregistrés dans le conteneur avec succès.");
    
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    
    public function vol_fermer(Request $request)
    {
        try {
            DB::beginTransaction();
            $agence = 'AFT Agence Louis Bleriot';
    
            // Compter le nombre de colis UNIQUEMENT pour cette agence
            $count = Colis::where('etat', 'Chargé')
                ->where('mode_transit', 'aerien')
                ->whereHas('expediteur', function ($query) use ($agence) {
                    // ---- LA CORRECTION EST ICI ----
                    // Il faut utiliser la variable $agence, pas la chaîne de caractères 'agence'
                    $query->where('agence', $agence);
                })
                ->count();
    
            if ($count === 0) {
                DB::rollBack(); // On annule la transaction même ici pour être propre
                return redirect()->back()->with('warning', 'Aucun colis avec l’état Chargé et un mode de transit Aérien pour cette agence.');
            }
    
            $referenceVol = $this->generateReferenceVol(); 
    
            // Mettre à jour les colis en appliquant LE MÊME FILTRE D'AGENCE
            $updatedCount = Colis::where('etat', 'Chargé')
                ->where('mode_transit', 'aerien')
                ->whereHas('expediteur', function ($q) use ($agence) {
                    $q->where('agence', $agence);
                })
                ->update([
                    'etat' => 'Fermé', 
                    'reference_vol' => $referenceVol // Ce champ est utilisé pour les vols et les conteneurs
                ]);
    
            // Cette validation fonctionnera maintenant correctement car $count et $updatedCount seront identiques
            if ($updatedCount !== $count) {
                DB::rollBack(); 
                return redirect()->back()->with('error', 'Erreur de cohérence lors de la mise à jour des colis. Veuillez réessayer.');
            }
    
            DB::commit();
    
            return redirect()->back()->with('success', "$updatedCount colis ont été fermés dans le ballon (Réf: $referenceVol) avec succès.");
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la fermeture du vol: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }




    public function store_colis(Request $request)
    {
        try {
            $validated = $request->all();

            // dd($validated); 
            $request->session()->put('step1', $validated);

            // dd(session('step1'));
            return redirect()->route('aftlb_colis.generer.qrcode');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement du colis : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du colis. Veuillez réessayer.');
        }
    }

    public function stepPayment()
    {
        // Récupérer les données de l'étape 1 depuis  la session
        $step1Data = session('step1');

        // Vérifier si les données existent et contiennent les prix
        if (!$step1Data || !isset($step1Data['prix']) || !is_array($step1Data['prix'])) {
            // Rediriger vers la première étape avec une erreur si les données sont manquantes
            // Remplacez 'route.vers.etape1' par le nom réel de votre route pour l'étape 1
            return redirect()->route('aftlb_colis.create.colis')->with('error', 'Données de colis manquantes ou invalides. Veuillez recommencer.');
        }

        // Calculer le montant total en additionnant tous les prix du tableau 'prix'
        $totalPrice = collect($step1Data['prix'])->sum();

        // Optionnel mais recommandé : stocker aussi le total en session pour usage ultérieur
        session(['step1.total_prix' => $totalPrice]);

        // Retourner la vue de paiement en lui passant le montant total calculé
    return view('AFT_LOUIS_BLERIOT.colis.add.payement', [
        'totalPrice' => $totalPrice
    ]);
        
    }
    public function storePayment(Request $request)
{
    try {
        $validatedData = $request->validate([
            // Ici tu peux ajouter tes règles de validation, par ex:
            // 'mode_payement' => 'required|string',
            // 'numero_compte' => 'nullable|string',
            // 'montant_reçu' => 'required|numeric',
        ]);

        // Stocker les données en session
        session(['step2' => $request->only([
            'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
            'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu',
        ])]);

        // dd(session('step1'), session('step2')); // décommenter pour debug

        return response()->json([
            'success' => true,
            'redirect' => route('aftlb_colis.generer.qrcode'),
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(), // Retourne les erreurs de validation
        ], 422);
    } catch (\Exception $e) {
        // dd($e->getMessage());
        // Log l’erreur pour le debug côté serveur
        \Log::error('Erreur storePayment: '.$e->getMessage());

        return response()->json([
            'success' => false,
            // dd($e->getMessage()),
            'message' => 'Une erreur interne est survenue. Veuillez réessayer plus tard.',
        ], 500);
    }
}



    /**
     * Génère les colis, le QR Code, et finalise la transaction.
     *
     * @param Request $request
     * @param InfobipSmsService $smsService
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function generer_qrcode(Request $request, InfobipSmsService $smsService)
    {
        // Fusionner les données des étapes précédentes stockées en session
        $data = array_merge(
            session('step1', []),
            session('step2', [])
        );

        // dd( $data);
        // Validation de base pour s'assurer que les données nécessaires sont présentes
        if (empty($data) || !isset($data['quantite_colis']) || !is_array($data['quantite_colis'])) {
            Log::error('Données de session invalides ou manquantes pour generer_qrcode.', ['session_data' => $data]);
            return redirect()->back()->with('error', 'Les données de la session sont invalides ou incomplètes. Veuillez recommencer.');
        }

        // Définition des statuts par défaut
        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'Validé';

        $telephone_user = $data['tel_expediteur'] ?? null;

        if ($telephone_user) {
            $user = User::where('tel', $telephone_user)
                ->orderByDesc('id')
                ->first();
            // dd($user);
        } else {
            $user = 'null';
        }
        $user_id = $user->id;

        // dd($user_id);

        // Préparation des numéros de téléphone complets (avec indicatif)
        $expediteurCountryCode = $data['country_code_expediteur'] ?? '';
        $expediteurPhoneNumber = $data['tel_expediteur'] ?? $data['tel_expediteur_societe'] ?? '';
        $expediteurTel = trim($expediteurCountryCode . $expediteurPhoneNumber);

        $destinataireCountryCode = $data['country_code_destinataire'] ?? $data['country_code_particulier'] ?? '';
        $destinatairePhoneNumber = $data['tel_destinataire'] ?? $data['tel_destinataire_societe'] ?? '';
        $destinataireTel = trim($destinataireCountryCode . $destinatairePhoneNumber);

        // Préparation des données pour la création de l'expéditeur et du destinataire
        $expediteurData = [
            'nom' => $data['nom_expediteur'] ?? $data['nom_expediteur_societe'] ?? '',
            'prenom' => $data['prenom_expediteur'] ?? '',
            'email' => $data['email_expediteur'] ?? $data['email_expediteur_societe'] ?? '',
            'tel' => $expediteurTel,
            'user_id' => $user_id,
            'agence' => $data['agence_expediteur_societe'] ?? $data['agence_expediteur'] ?? '',
            'lieu_expedition' => $data['adresse_expediteur_societe'] ?? $data['adresse_expediteur'] ?? 'null',
        ];

        $destinataireData = [
            'nom' => $data['nom_destinataire'] ?? $data['nom_destinataire_societe'] ?? '',
            'prenom' => $data['prenom_destinataire'] ?? '',
            'email' => $data['email_destinataire'] ?? $data['email_destinataire_societe'] ?? '',
            'tel' => $destinataireTel,
            'agence' => $data['agence_destinataire_societe'] ?? $data['agence_destinataire'] ?? '',
            'lieu_destination' => $data['adresse_destinataire_societe'] ?? $data['adresse_destinataire_particulier'] ?? 'null',
        ];

        // Création de l'expéditeur et du destinataire en base de données
        try {
            $expediteur = Expediteur::create($expediteurData);
            $destinataire = Destinataire::create($destinataireData);
        } catch (\Exception $e) {
            Log::error('Erreur création Expediteur/Destinataire: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde des informations expéditeur/destinataire.');
        }
        // dd($data);
        // Logique de gestion du paiement
        $payementDataSession = session('step1', []);



        // -------------------------------
        //  CALCUL DU MONTANT TOTAL ESTIMÉ
        // -------------------------------
        $quantites = collect($data['quantite_colis'] ?? []);
        $prixUnitaires = collect($data['prix'] ?? []);

        // Montant total des produits (quantité × prix)
        $montantProduits = $quantites
            ->zip($prixUnitaires)
            ->map(fn($pair) => ((float)($pair[0]) ?: 0) * ((float)($pair[1]) ?: 0))
            ->sum();

        // Montant du service éventuel
        $montantService = collect($data['prix_service'] ?? [])->sum();

        // Total général
        $montantTotalEstime = $montantProduits + $montantService;


        // -------------------------------
        // 💰 GESTION DU MODE DE PAIEMENT
        // -------------------------------
        $modePaiement = $payementDataSession['mode_payement'] ?? null;
        $montantPaiementTransaction = 0;

        switch ($modePaiement) {
            case 'cash':
                $montantPaiementTransaction = (float)($payementDataSession['montant_reçu'] ?? 0);
                break;

            case 'delivery':
                $montantPaiementTransaction = 0; // Payé à la livraison
                break;

            case 'bank':
                $montantPaiementTransaction = (float)($payementDataSession['montant_bank'] ?? 0);
                break;

            case 'cheque':
                $montantPaiementTransaction = (float)($payementDataSession['montant_cheque'] ?? 0);
                break;

            default:
                $montantPaiementTransaction = 0;
                break;
        }


        // -------------------------------
        // 🧾 DÉTERMINATION DU STATUT DE PAIEMENT
        // -------------------------------
        $statutPaiementGlobal = 'non payé';

        if ($modePaiement === 'delivery') {
            // Le client paiera à la livraison
            $statutPaiementGlobal = 'non payé';

        } elseif ($modePaiement === 'cash') {
            if ($montantPaiementTransaction <= 0) {
                $statutPaiementGlobal = 'non payé';
            } elseif ($montantPaiementTransaction < $montantTotalEstime) {
                $statutPaiementGlobal = 'partiellement payé';
            } else {
                $statutPaiementGlobal = 'payé';
            }

        } 


        // -------------------------------
        // 🧠 RÉSULTATS POUR CONTRÔLE
        // -------------------------------
        // dd([
        //     'mode_paiement' => $modePaiement,
        //     'montant_total_estime' => $montantTotalEstime,
        //     'montant_paiement' => $montantPaiementTransaction,
        //     'statut_paiement' => $statutPaiementGlobal,
        // ]);

        $agentId = Auth::check() ? Auth::user()->agent?->id : null;
        $transactionId = $request->input('cinetpay_transaction_id') ?? $payementDataSession['transaction_id'] ?? ('MANUAL-' . uniqid());

        // Création de l'enregistrement de paiement principal
        $paiementPrincipal = null;
        try {
            $basePaiementData = [
                'methode_paiement' => $modePaiement,
                'operateur' => $payementDataSession['mobile_operateur'] ?? null,
                'banque' => $payementDataSession['bank_nom_banque'] ?? null,
                'NumeroPaiement' => $payementDataSession['mobile_numero_tel'] ?? $payementDataSession['bank_numero_compte'] ?? null,
                'id_transaction' => $transactionId,
                'statut_paiement' => $statutPaiementGlobal,
                'date_validation' => now(),
                'expediteur_id' => $expediteur->id,
                'agent_id' => $agentId,
                'montant' => $montantTotalEstime,
                'montant_paye' => $montantPaiementTransaction,
                'colis_id' => null, // Non lié à un colis spécifique au départ
            ];
            $paiementPrincipal = Paiement::create($basePaiementData);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création du paiement principal : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement.');
        }

        // --- SECTION DE GÉNÉRATION DE LA RÉFÉRENCE DU COLIS ET DE LA RÉFÉRENCE INTERNE (id_reference) ---
        $referenceColisPrincipale = null;
        $id_reference_next_available = 1; // Initialisation par défaut pour le premier colis si aucun n'existe
        $message = null; // Message spécial pour signaler un nouveau conteneur

        $mode_transit = $data['mode_transit'] ?? '';
        $agence = $data['agence_expediteur'] ?? 'AFT Agence Louis Bleriot';
        $reference_colis_fournie = $data['reference_colis_maritime'] ?? $data['reference_colis_aerien'] ?? null;

        // Récupérer le dernier colis enregistré pour ce mode de transit et cette agence
        $dernierColis = DB::table('colis')
            ->where('mode_transit', $mode_transit)
            ->where('agence', $agence)
            ->orderByDesc('id')
            ->first();

        if (!$dernierColis) {
            // Cas 1: Aucun colis précédent pour ce mode de transit et agence.
            // On commence un nouveau conteneur avec la référence fournie.
            $referenceColisPrincipale = $reference_colis_fournie;
            $id_reference_next_available = 1;
        } else {
            // Cas 2: Un colis précédent existe. Déterminer si on continue le conteneur existant ou en crée un nouveau.

            // Condition pour continuer le conteneur existant :
            // 1. L'état du dernier colis n'est PAS 'Fermé'.
            // 2. La référence fournie par l'utilisateur est IDENTIQUE à la référence du dernier colis.
            if (isset($dernierColis->etat) && $dernierColis->etat !== 'Fermé' && $reference_colis_fournie !== $dernierColis->reference_colis) {
                // Scénario 2.1: Le conteneur précédent est ouvert et l'utilisateur veut continuer le même conteneur.
                // On utilise la référence existante du dernier colis.
                $referenceColisPrincipale = $reference_colis_fournie;

                // On doit trouver le plus grand 'id_reference' pour CE conteneur spécifique
                // pour s'assurer de ne pas avoir de doublons et d'incrémenter correctement.
                $maxIdRefForCurrentContainer = DB::table('colis')
                    ->where('reference_colis', $dernierColis->reference_colis)
                    ->max('id_reference');
                
                $id_reference_next_available = ($maxIdRefForCurrentContainer ?? 0) + 1;

            } else {
                // Scénario 2.2: Le conteneur précédent est fermé OU la référence fournie est différente.
                // Cela signifie qu'on démarre un NOUVEAU conteneur avec la référence fournie.
                $referenceColisPrincipale = $reference_colis_fournie;
                $id_reference_next_available = 1; // L'id_reference recommence à 1 pour ce nouveau conteneur.

                // Vérifier si le suffixe du conteneur a changé pour afficher un message
                $dernierParts = explode('-', $dernierColis->reference_colis); // Ex: AE-0001-TC1
                $nouveauParts = explode('-', $reference_colis_fournie); // Ex: AE-0001-TC2

                if (isset($dernierParts[2]) && isset($nouveauParts[2]) && $nouveauParts[2] !== $dernierParts[2]) {
                    $message = "⚠️ Vous commencez un nouveau conteneur : {$nouveauParts[2]}";
                }
            }
        }
        
        // --- FIN DE LA SECTION DE LOGIQUE DE RÉFÉRENCE ---
        // À ce stade, $referenceColisPrincipale et $id_reference_next_available sont correctement définis
        // pour le premier colis à créer dans ce lot.

        $colisEnregistres = [];
        $erreursCreation = [];

        // Boucle sur chaque ligne d'article pour créer les colis correspondants
        foreach ($data['quantite_colis'] as $index => $quantite_pour_ligne_article) {
            $quantite_pour_ligne_article = (int)$quantite_pour_ligne_article;
            if ($quantite_pour_ligne_article <= 0) continue;

            $hauteur = $data['hauteur'][$index] ?? null;
            $largeur = $data['largeur'][$index] ?? null;
            $longueur = $data['longueur'][$index] ?? null;
            $dimension_result = (isset($hauteur, $largeur, $longueur)) ? "{$hauteur}x{$largeur}x{$longueur}" : null;
            
            $prixUnitairePourCetteLigne = (float)($data['prix'][$index] ?? 0);
        
            // Créer un colis pour chaque unité physique de la quantité spécifiée
            for ($i = 1; $i <= $quantite_pour_ligne_article; $i++) {
                $colisItemData = [
                    'reference_colis' => $referenceColisPrincipale,
                    'id_reference' => $id_reference_next_available, // Utilise l'ID de référence actuel
                    'agence' => $agence,
                    'devise' => 'EUR',
                    'quantite_colis' => 1, // Chaque enregistrement représente un colis physique unique
                    'service' => $data['service'] ?? null,
                    'montant_service' => $data['prix_service'] ?? null,
                    'produit' => $data['produit'][$index] ?? null,
                    'prix_transit_colis' => $prixUnitairePourCetteLigne,
                    'poids_colis' => $data['poids'][$index] ?? null,
                    'mode_transit' => $mode_transit,
                    'status' => $statutPaiementGlobal, 
                    'etat' => $data['etat'],
                    'type_colis' => $data['type_colis'][$index] ?? null,
                    'dimension_result' => $dimension_result,
                    'description_colis' => $data['description_colis'][$index] ?? null,
                    'expediteur_id' => $expediteur->id,
                    'destinataire_id' => $destinataire->id,
                    'agent_id' => $agentId,
                    'qr_code_path' => null,
                ];

                try {
                    $colisModel = Colis::create($colisItemData);
                    
                    // Assigner le paiement principal au premier colis créé s'il n'est pas déjà lié
                    if ($paiementPrincipal && !$paiementPrincipal->colis_id) {
                        $paiementPrincipal->update(['colis_id' => $colisModel->id]);
                    }

                    // Génération du QR Code pour ce colis individuel
                    $qrData = [
                        'ID' => $colisModel->id,
                        'Ref' => $colisModel->reference_colis,
                        'Etat' => $colisModel->etat,
                        'Exp' => optional($expediteur)->nom,
                        'Dest' => optional($destinataire)->nom . '/' . optional($destinataire)->tel,
                        'Agence' => optional($destinataire)->agence,
                    ];
                    $qrCodeContent = implode("\n", array_map(fn($k, $v) => "$k: $v", array_keys($qrData), array_values($qrData)));

                    $qrCode = new QrCode($qrCodeContent);
                    $writer = new PngWriter();
                    $pngData = $writer->write($qrCode)->getString();

                    // Nettoyage de la référence pour le nom de fichier
                    $safeRef = preg_replace('/[^A-Za-z0-9\-_]/', '_', $colisModel->reference_colis);
                    $filePath = 'qrcodes/colis_' . $safeRef . '_id' . $colisModel->id . '_item' . $colisModel->id_reference . '.png';
                    $fullPath = public_path($filePath);
                    
                    File::ensureDirectoryExists(dirname($fullPath));
                    file_put_contents($fullPath, $pngData);

                    $colisModel->update(['qr_code_path' => $filePath]);
                    $colisEnregistres[] = $colisModel->fresh();

                    // $id_reference_next_available++; 
                } catch (\Exception $e) {
                    Log::error("Erreur création colis/QR: " . $e->getMessage(), ['data' => $colisItemData]);
                    $erreursCreation[] = "Erreur lors de la création du colis (Réf: {$referenceColisPrincipale}, ID interne: {$id_reference_next_available}).";
                }
            }
        }

        if (!empty($erreursCreation)) {
            return redirect()->back()->with('error', implode('<br>', $erreursCreation));
        }
        if (empty($colisEnregistres)) {
            return redirect()->back()->with('error', 'Aucun colis n\'a été créé. Vérifiez les quantités.');
        }

        // Nettoyage de la session après succès
        session()->forget(['step1', 'step2']);

        $colisEnregistresCollection = collect($colisEnregistres);
        $firstColis = $colisEnregistresCollection->first(); 
        
        $firstInfo = [
            'id' => $firstColis?->id,
            'reference_colis' => $firstColis?->reference_colis,
            'nom_destinataire' => optional($firstColis?->destinataire)->nom,
            'prenom_destinataire' => optional($firstColis?->destinataire)->prenom,
            'tel_destinataire' => optional($firstColis?->destinataire)->tel,
            'nom_expediteur' => optional($firstColis?->expediteur)->nom,
            'prenom_expediteur' => optional($firstColis?->expediteur)->prenom,
            'tel_expediteur' => optional($firstColis?->expediteur)->tel,
            'devise' => optional($firstColis)->devise,
        ];
        $totalQuantitePhysique = $colisEnregistresCollection->count();
        // Calcul du prix total de transit et des services pour tous les colis enregistrés
        $totalPrixTransit = $colisEnregistresCollection->sum('prix_transit_colis') + $colisEnregistresCollection->unique('service')->sum('montant_service');
        $restePaye = $totalPrixTransit - $montantPaiementTransaction;
        
        if ($modePaiement === 'delivery') {
            $restePaye = $totalPrixTransit;
        }
        
    
        $expediteurTelForSms = $expediteurTel;
        $destinataireTelForSms = $destinataireTel;

        // dd($expediteurTelForSms, $destinataireTelForSms);
        
        $colisReferences = $colisEnregistresCollection
            ->pluck('reference_colis')
            ->unique()
            ->implode(', ');
        $messageSms = "Cher(e) client(e), votre colis (Réf: {$colisReferences}) a été enregistrer et est en attente d'expédition. Merci de nous faire confiance. Suivi : https://aft-app.com";
            // Envoi du SMS à l'expéditeur 
        if ($expediteurTelForSms) { // Utilisation du numéro complet
            try {
                $smsService->sendSms($expediteurTelForSms, $messageSms);
                Log::info("SMS envoyé à l'expéditeur {$expediteurTelForSms} pour le colis {$colisReferences}.");
            } catch (\RuntimeException $e) {
                Log::error("⚠️ Erreur de configuration Infobip lors de l'envoi SMS à l'expéditeur: " . $e->getMessage(), [
                    'phone_number' => $expediteurTelForSms,
                    'message' => $messageSms
                ]);
            } catch (\Throwable $e) {
                Log::error("⚠️ Une erreur inattendue est survenue lors de l'envoi du SMS à l'expéditeur ! " . $e->getMessage(), [
                    'phone_number' => $expediteurTelForSms,
                    'message' => $messageSms,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

                // ENVOI DE L'EMAIL À L'EXPÉDITEUR
        try {
            Log::info("Tentative d'envoi d'email à: " . ($expediteur->email ?? 'NULL'));
            
            if (!empty($expediteur->email) && filter_var($expediteur->email, FILTER_VALIDATE_EMAIL)) {
                
                // Vérification supplémentaire
                Log::debug("Détails de l'email:", [
                    'email' => $expediteur->email,
                    'paiement_id' => $paiementPrincipal->id,
                    'colis_count' => $colisEnregistresCollection->count()
                ]);

                // CORRECTION : Utilisation correcte du Mailable
                Mail::to($expediteur->email)
                    ->send(new \App\Mail\ColisValidateMail($paiementPrincipal, $colisEnregistresCollection));
                
                Log::info("✅ Email de confirmation envoyé à: " . $expediteur->email);
                
            } else {
                Log::warning("Email invalide ou manquant pour l'expéditeur ID: " . $expediteur->id, [
                    'email' => $expediteur->email ?? 'non défini'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("❌ Erreur lors de l'envoi de l'email: " . $e->getMessage(), [
                'email' => $expediteur->email ?? 'non défini',
                'exception' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        
        // Retourner la vue de succès avec toutes les données nécessaires
        return view('AFT_LOUIS_BLERIOT.colis.add.complete', [
            'colis' => $colisEnregistresCollection,
            'first' => $firstInfo, 
            'totalQuantite' => $totalQuantitePhysique,
            'totalPrixTransit' => $totalPrixTransit,
            'restePaye' => $restePaye,
            'mode_payement' => $modePaiement,
            'totalMontantPaye' => $montantPaiementTransaction,
            'devise' => $firstColis['devise'] ?? 'EUR',
            'specialMessage' => $message,
        ])->with('success', 'Colis bien enregistré');
    }

    public function update_hold(Request $request, InfobipSmsService $infobipSmsService, $id)
    {
        // 1. Valider la requête
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0',
            'montant_items' => 'sometimes|json'
        ]);
    
        // 2. Trouver le devis avec ses items
        $devis = Devis::with('items')->findOrFail($id);
    
        DB::beginTransaction();
    
        try {
            // 3. Mettre à jour les montants des items si fournis
            if ($request->has('montant_items') && !empty($request->montant_items)) {
                $montantItems = json_decode($request->montant_items, true);
                
                foreach ($montantItems as $itemId => $montant) {
                    // CORRECTION : Utiliser DevisItems (avec 's') pour correspondre à votre modèle
                    $item = DevisItems::where('id', $itemId)
                                    ->where('devis_id', $devis->id)
                                    ->first();
                    
                    if ($item) {
                        $item->update(['montant' => $montant]);
                    }
                }
                
                // Recharger les relations pour avoir les données fraîches
                $devis->load('items');
            }
    
            // 4. Mettre à jour le montant et l'état du devis
            $devis->montant = $validatedData['montant'];
            $devis->etat = 'Validé';
            $devis->save();
    
            DB::commit();
    
            // Le reste du code pour les notifications...
            $paiementFactice = new Paiement();
            $paiementFactice->montant = $devis->montant;
            $paiementFactice->montant_paye = 0;
            $paiementFactice->statut_paiement = 'En attente';
            $paiementFactice->methode_paiement = 'Non défini';
            $paiementFactice->date_validation = now();
            
            $expediteurInfo = (object)[
                'email' => $devis->email_expediteur,
                'nom' => $devis->nom_expediteur,
                'tel' => $devis->tel_expediteur
            ];
            $paiementFactice->expediteur = $expediteurInfo;
    
            // Envoi de l'email à l'agent
            try {
                $agent = User::find($devis->user_id);
                if ($agent && !empty($agent->email) && filter_var($agent->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($agent->email)->send(new ColisValidatedMail($paiementFactice, $devis->items));
                    Log::info("Email de validation du devis envoyé avec succès à l'agent: " . $agent->email . " (Réf Devis: " . $devis->reference . ")");
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi de l'email de validation du devis à l'agent.", [
                    'reference_devis' => $devis->reference,
                    'exception' => $e
                ]);
            }
    
            // Envoi du SMS
            $message = "Bonjour " . $devis->nom_expediteur . ", votre devis (Réf: " . $devis->reference . ") a été validé. Montant Total: " . $devis->montant . " " . $devis->devise . ". Veuillez consulter vos emails pour les détails.";
            
            try {
                $infobipSmsService->sendSms($devis->tel_expediteur, $message);
                Log::info("SMS de validation du devis envoyé à " . $devis->tel_expediteur);
            } catch (\Exception $e) {
                Log::error('Erreur envoi SMS: ' . $e->getMessage());
            }
    
            return redirect()->route('aftlb_colis.hold')->with('success', 'Devis validé et notifié au client avec succès !');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la validation du devis: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la validation du devis: ' . $e->getMessage());
        }
    }
    public function editBon_livraison($id)
    {
        // dd($id);
        
        try {
            $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Gérer le cas où l'ID n'existe pas
            abort(404, 'Colis non trouvé.');
        }

       

            $colisCollection = Colis::where('reference_colis', $colis->reference_colis)->get();

            if ($colisCollection->isEmpty()) {
                return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
            }   

        if ($colisCollection->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun colis trouvé avec cette référence.');
        }

        // Récupération du premier colis
        $firstColis = $colisCollection->first();
        // dd($firstColis->reference_colis);
        $reference_colis = $firstColis->id;
        $date_facture = now();
        $expediteur = $firstColis->expediteur->nom . ' ' . $firstColis->expediteur->prenom;
        $tel_expediteur = $firstColis->expediteur->tel;
        $tel_destinataire = $firstColis->destinataire->tel;
        $destinataire = $firstColis->destinataire->nom . ' ' . $firstColis->destinataire->prenom;
        $numero_facture = '00' . str_pad($firstColis->id, 3, '0', STR_PAD_LEFT);
        $reference_colis = $firstColis->reference_colis;
        // Calcul du prix total
        $ids_colis = $colisCollection->pluck('id')->toArray();
        $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();
        $mode_payement = $paiements->pluck('methode_paiement')->unique()->first();
        $totalMontant = $paiements->sum('montant');
        $totalMontantPaye = $paiements->sum('montant_paye');
        $restePaye = $totalMontant - $totalMontantPaye;
       
        $prix_total = 0;
        foreach ($colisCollection as $colis) {
            if (!isset($colis->prix_transit_colis)) {
                throw new \Exception("Le champ prix_transit_colis est manquant pour un colis.");
            }
            $prix_total += $colis->prix_transit_colis;
        }

        // Utilisation de optional() pour éviter les erreurs si la relation paiement est nulle
        
        $montant_paye = optional($firstColis->paiement)->montant_reçu ?? 0;
        // dd($prix_total);
        $reste = $prix_total - $montant_paye;   

        $id_agent = Auth::user()->id;
        $nom_agent = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        // dd($nom_agent);

        // Création de la facture
        Invoice::create([
            'nom_agent' => $nom_agent,
            'nom_expediteur' => $expediteur,
            'nom_destinataire' => $destinataire,
            'expediteur_id' => $firstColis->expediteur->id,
            'destinataire_id' => $firstColis->destinataire->id,
            'agent_id' => $id_agent,
            'montant' => $prix_total ?? 0,
            'numero_facture' => $numero_facture,
        ]);
        // dd($u);
        // Préparation des données des colis
        $colisData = [];
        foreach ($colisCollection as $colis) {
            $colisData[] = [
                'description'         => $colis->description_colis,
                'quantite'            => $colis->quantite_colis,
                'poids'               => $colis->poids_colis,
                'type_colis'          => $colis->type_colis,
                'prix_transit_colis'  => $colis->prix_transit_colis,
            ];
        }

        // Passage des données à la vue
        return view('AFT_LOUIS_BLERIOT.colis.add.edit_bon_livraison', compact(
            'date_facture', 
            'reference_colis', 
            'expediteur', 
            'tel_expediteur', 
            'destinataire', 
            'prix_total', 
            'montant_paye', 
            'reste', 
            'mode_payement', 
            'colisData',
            'numero_facture',
            'tel_destinataire',
            'totalMontant',
            'totalMontantPaye',
            'restePaye',
            'colisCollection'

        ));
    }

    public function imprimerBon_livraison($id)
    {
        try {
            $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Gérer le cas où l'ID n'existe pas
            abort(404, 'Colis non trouvé.');
        }

            $colisCollection = Colis::where('reference_colis', $colis->reference_colis)->get();

            if ($colisCollection->isEmpty()) {
                return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
            }   

        if ($colisCollection->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun colis trouvé avec cette référence.');
        }

        // Récupération du premier colis
        // dd($colisCollection);
        $firstColis = $colisCollection->first();
        $reference_colis = $firstColis->id;
        $date_facture = now();
        $expediteur = $firstColis->expediteur->nom . ' ' . $firstColis->expediteur->prenom;
        $tel_expediteur = $firstColis->expediteur->tel;
        $tel_destinataire = $firstColis->destinataire->tel;
        $destinataire = $firstColis->destinataire->nom . ' ' . $firstColis->destinataire->prenom;
        $adresse_destinataire = $firstColis->destinataire->lieu_destination;
        $numero_facture = '00' . str_pad($firstColis->id, 3, '0', STR_PAD_LEFT);
        $reference_colis = $firstColis->reference_colis;
        
        // Calcul du prix total
        $ids_colis = $colisCollection->pluck('id')->toArray();
        $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();
        $mode_payement = $paiements->pluck('methode_paiement')->unique()->first();
        $totalMontant = $paiements->sum('montant');
        $totalMontantPaye = $paiements->sum('montant_paye');
        $restePaye = $totalMontant - $totalMontantPaye;
       
        $prix_total = 0;
        foreach ($colisCollection as $colis) {
            if (!isset($colis->prix_transit_colis)) {
                throw new \Exception("Le champ prix_transit_colis est manquant pour un colis.");
            }
            $prix_total += $colis->prix_transit_colis;
        }

        // Utilisation de optional() pour éviter les erreurs si la relation paiement est nulle
        
        $montant_paye = optional($firstColis->paiement)->montant_reçu ?? 0;
        // dd($prix_total);
        $reste = $prix_total - $montant_paye;   

        $id_agent = Auth::user()->id;
        $nom_agent = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        // dd($nom_agent);

        // Création de la facture
        Invoice::create([
            'nom_agent' => $nom_agent,
            'nom_expediteur' => $expediteur,
            'nom_destinataire' => $destinataire,
            'expediteur_id' => $firstColis->expediteur->id,
            'destinataire_id' => $firstColis->destinataire->id,
            'agent_id' => $id_agent,
            'montant' => $prix_total ?? 0,
            'numero_facture' => $numero_facture,
            

        ]);
        // dd($u);
        // Préparation des données des colis
        $colisData = [];
        foreach ($colisCollection as $colis) {
            $colisData[] = [
                'description'         => $colis->description_colis,
                'quantite'            => $colis->quantite_colis,
                'poids'               => $colis->poids_colis,
                'type_colis'          => $colis->type_colis,
                'prix_transit_colis'  => $colis->prix_transit_colis,
            ];
        }

        // Passage des données à la vue
        return view('AFT_LOUIS_BLERIOT.invoice.edit_bon_livraison', compact(
            'date_facture', 
            'reference_colis', 
            'expediteur', 
            'tel_expediteur', 
            'destinataire', 
            'prix_total', 
            'montant_paye', 
            'reste', 
            'mode_payement', 
            'colisData',
            'numero_facture',
            'tel_destinataire',
            'totalMontant',
            'totalMontantPaye',
            'restePaye',
            'colisCollection',
            'adresse_destinataire'

        ));
    }

    public function editFacture($id)
    {
        // 1. Récupérer le colis principal
        $colis_principal = Colis::find($id);
        if (!$colis_principal) {
            return redirect()->route('chine_colis.hold')->with('error', 'Colis non trouvé.');
        }

        // 2. Récupérer tous les colis partageant la même référence
        $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)
            ->with(['expediteur', 'destinataire'])
            ->get();

        if ($colisCollection->isEmpty()) {
            return redirect()->route('chine_colis.hold')->with('warning', 'Aucun colis trouvé avec cette référence.');
        }

        $firstColis = $colisCollection->first();

        // --- 3. Préparation des données de base de la facture ---
        $date_facture = now();
        $expediteur = trim(optional($firstColis->expediteur)->nom . ' ' . optional($firstColis->expediteur)->prenom);
        $tel_expediteur = optional($firstColis->expediteur)->tel;
        $adresse_expediteur = optional($firstColis->expediteur)->lieu_expedition;
        $destinataire = trim(optional($firstColis->destinataire)->nom . ' ' . optional($firstColis->destinataire)->prenom);
        $tel_destinataire = optional($firstColis->destinataire)->tel;
        $adresse_destinataire = optional($firstColis->destinataire)->lieu_destination;
        $numero_facture = 'FA-' . str_pad($firstColis->id, 5, '0', STR_PAD_LEFT);
        $reference_colis = $firstColis->reference_colis;
        $devise = $firstColis->devise;

        // --- 4. Regroupement par produit ---
        $colisParProduit = $colisCollection->groupBy('produit');

        $produitsGroupes = [];
        $sous_total_produits = 0;
        $quantite_totale_generale = 0;

        foreach ($colisParProduit as $nomProduit => $items) {
            $nombre_colis_par_produit = $items->count();

            // Ici : si tu veux additionner le prix_transit_colis de chaque colis (sans multiplier par quantité)
            $montant_total_ligne = $items->sum('prix_transit_colis');

            // Prix unitaire moyen = total / nombre de colis
            $prix_unitaire = ($nombre_colis_par_produit > 0)
                ? $montant_total_ligne / $nombre_colis_par_produit
                : 0;

            $produitsGroupes[] = [
                'produit' => $nomProduit,
                'nombre_colis' => $nombre_colis_par_produit,
                'prix_unitaire_moyen' => $prix_unitaire,
                'montant_total_ligne' => $montant_total_ligne,
            ];

            $sous_total_produits += $montant_total_ligne;
            $quantite_totale_generale += $nombre_colis_par_produit;
        }

        // --- 5. Service ---
        $service_info = null;
        if ($firstColis->service && (float)$firstColis->montant_service > 0) {
            $service_info = [
                'service' => $firstColis->service,
                'montant_service' => (float)$firstColis->montant_service,
            ];
        }
        // dd( $service_info, $produitsGroupes);

        $montant_service_total = $service_info ? $service_info['montant_service'] : 0;
        $prix_total_invoice = $sous_total_produits + $montant_service_total;

        // --- 6. Paiements ---
        $ids_colis = $colisCollection->pluck('id')->toArray();
        $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();

        $mode_payement = $paiements->pluck('methode_paiement')->unique()->implode(', ');
        $totalMontantPaye = $paiements->sum('montant_paye');
        $restePaye = $prix_total_invoice - $totalMontantPaye;

        // --- 7. Agent ---
        $agent = Auth::user();
        $nom_agent = trim($agent->first_name . ' ' . $agent->last_name);

        Invoice::updateOrCreate(
            ['numero_facture' => $numero_facture],
            [
                'nom_agent' => $nom_agent,
                'nom_expediteur' => $expediteur,
                'nom_destinataire' => $destinataire,
                'expediteur_id' => optional($firstColis->expediteur)->id,
                'destinataire_id' => optional($firstColis->destinataire)->id,
                'agent_id' => $agent->id,
                'montant' => $prix_total_invoice,
                'reference_colis' => $reference_colis,
            ]
        );

        // --- 8. Retourner la vue ---
        return view('AFT_LOUIS_BLERIOT.colis.add.edit_invoice', compact(
            'date_facture',
            'reference_colis',
            'expediteur',
            'tel_expediteur',
            'adresse_expediteur',
            'destinataire',
            'tel_destinataire',
            'adresse_destinataire',
            'prix_total_invoice',
            'sous_total_produits',
            'service_info',
            'mode_payement',
            'produitsGroupes',
            'numero_facture',
            'totalMontantPaye',
            'restePaye',
            'devise'
        ));
    }

    public function editEtiquette($id)
    {
        try {
            // 1. Récupérer le colis spécifique par ID pour obtenir la référence_colis
            $colisInitial = Colis::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Colis non trouvé.');
        }
    
        $reference_colis = $colisInitial->reference_colis;
    
        // 2. Récupérer tous les colis (objets Colis) qui partagent cette référence_colis
        // Chaque objet Colis aura son propre qr_code_path, type_colis, etc.
        $colisPourEtiquettes = Colis::where('reference_colis', $reference_colis)
                                   ->with(['expediteur', 'destinataire']) // Charger les relations
                                   ->get(); // Important: ->get() pour avoir une collection
    
        if ($colisPourEtiquettes->isEmpty()) {
            // Cela ne devrait pas arriver si $colisInitial a été trouvé, mais c'est une sécurité
            abort(404, 'Aucun colis trouvé pour cette référence.');
        }
    
        // 3. La vue PDF s'attend à une collection de colis et à un nombre total.
        //    Chaque élément de $colisPourEtiquettes EST une étiquette à générer.
        $pdf = PDF::loadView('AFT_LOUIS_BLERIOT.colis.add.edit_etiquette', [
                'colis_collection' => $colisPourEtiquettes, // Passer la collection de colis
                'totalEtiquettes' => $colisPourEtiquettes->count() // Nombre total d'étiquettes
            ])
            ->setPaper('a6', 'landscape') 
            ->setOption('isRemoteEnabled', true);
    
        // 4. Retourner le PDF pour téléchargement
        $safeRef = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $reference_colis);
        $fileName = 'etiquettes_' . $safeRef . '.pdf';
    
        return $pdf->download($fileName);
    }

    public function inprimerEtiquette($id) 
    {
        try {
            // 1. Récupérer le colis spécifique par ID pour obtenir la référence_colis
            $colisInitial = Colis::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Colis non trouvé.');
        }
    
        $reference_colis = $colisInitial->reference_colis;
    
        // 2. Récupérer tous les colis (objets Colis) qui partagent cette référence_colis
        // Chaque objet Colis aura son propre qr_code_path, type_colis, etc.
        $colisPourEtiquettes = Colis::where('reference_colis', $reference_colis)
                                   ->with(['expediteur', 'destinataire']) // Charger les relations
                                   ->get(); // Important: ->get() pour avoir une collection
    
        if ($colisPourEtiquettes->isEmpty()) {
            // Cela ne devrait pas arriver si $colisInitial a été trouvé, mais c'est une sécurité
            abort(404, 'Aucun colis trouvé pour cette référence.');
        }
    
        // 3. La vue PDF s'attend à une collection de colis et à un nombre total.
        //    Chaque élément de $colisPourEtiquettes EST une étiquette à générer.
        $pdf = PDF::loadView('AFT_LOUIS_BLERIOT.colis.add.edit_etiquette', [
                'colis_collection' => $colisPourEtiquettes, // Passer la collection de colis
                'totalEtiquettes' => $colisPourEtiquettes->count() // Nombre total d'étiquettes
            ])
            ->setPaper('a6', 'landscape') 
            ->setOption('isRemoteEnabled', true);
    
        // 4. Retourner le PDF pour téléchargement
        $safeRef = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $reference_colis);
        $fileName = 'etiquettes_' . $safeRef . '.pdf';
    
        return $pdf->download($fileName);
    }



    public function imprimerFacture($id)
    {
        // 1. Récupérer le colis principal
        $colis_principal = Colis::find($id);
        if (!$colis_principal) {
            return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
        }

        // 2. Récupérer tous les colis partageant la même référence
        $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)
            ->with(['expediteur', 'destinataire'])
            ->get();

        if ($colisCollection->isEmpty()) {
            return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun colis trouvé avec cette référence.');
        }

        $firstColis = $colisCollection->first();

        // --- 3. Préparation des données de base de la facture ---
        $date_facture = now();
        $expediteur = trim(optional($firstColis->expediteur)->nom . ' ' . optional($firstColis->expediteur)->prenom);
        $tel_expediteur = optional($firstColis->expediteur)->tel;
        $adresse_expediteur = optional($firstColis->expediteur)->lieu_expedition;
        $destinataire = trim(optional($firstColis->destinataire)->nom . ' ' . optional($firstColis->destinataire)->prenom);
        $tel_destinataire = optional($firstColis->destinataire)->tel;
        $adresse_destinataire = optional($firstColis->destinataire)->lieu_destination;
        $numero_facture = 'FA-' . str_pad($firstColis->id, 5, '0', STR_PAD_LEFT);
        $reference_colis = $firstColis->reference_colis;
        $devise = $firstColis->devise;

        // --- 4. Regroupement par produit ---
        $colisParProduit = $colisCollection->groupBy('produit');

        $produitsGroupes = [];
        $sous_total_produits = 0;
        $quantite_totale_generale = 0;

        foreach ($colisParProduit as $nomProduit => $items) {
            $nombre_colis_par_produit = $items->count();

            // Ici : si tu veux additionner le prix_transit_colis de chaque colis (sans multiplier par quantité)
            $montant_total_ligne = $items->sum('prix_transit_colis');

            // Prix unitaire moyen = total / nombre de colis
            $prix_unitaire = ($nombre_colis_par_produit > 0)
                ? $montant_total_ligne / $nombre_colis_par_produit
                : 0;

            $produitsGroupes[] = [
                'produit' => $nomProduit,
                'nombre_colis' => $nombre_colis_par_produit,
                'prix_unitaire_moyen' => $prix_unitaire,
                'montant_total_ligne' => $montant_total_ligne,
            ];

            $sous_total_produits += $montant_total_ligne;
            $quantite_totale_generale += $nombre_colis_par_produit;
        }

        // --- 5. Service ---
        $service_info = null;
        if ($firstColis->service && (float)$firstColis->montant_service > 0) {
            $service_info = [
                'service' => $firstColis->service,
                'montant_service' => (float)$firstColis->montant_service,
            ];
        }

        $montant_service_total = $service_info ? $service_info['montant_service'] : 0;
        $prix_total_invoice = $sous_total_produits + $montant_service_total;

        // --- 6. Paiements ---
        $ids_colis = $colisCollection->pluck('id')->toArray();
        $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();

        $mode_payement = $paiements->pluck('methode_paiement')->unique()->implode(', ');
        $totalMontantPaye = $paiements->sum('montant_paye');
        $restePaye = $prix_total_invoice - $totalMontantPaye;

        // --- 7. Agent ---
        $agent = Auth::user();
        $nom_agent = trim($agent->first_name . ' ' . $agent->last_name);

        Invoice::updateOrCreate(
            ['numero_facture' => $numero_facture],
            [
                'nom_agent' => $nom_agent,
                'nom_expediteur' => $expediteur,
                'adresse_expediteur' => $adresse_expediteur,
                'nom_destinataire' => $destinataire,
                'expediteur_id' => optional($firstColis->expediteur)->id,
                'destinataire_id' => optional($firstColis->destinataire)->id,
                'agent_id' => $agent->id,
                'montant' => $prix_total_invoice,
                'reference_colis' => $reference_colis,
            ]
        );

        // --- 8. Retourner la vue ---
        return view('AFT_LOUIS_BLERIOT.invoice.edit_invoice', compact(
            'date_facture',
            'reference_colis',
            'expediteur',
            'tel_expediteur',
            'adresse_expediteur',
            'destinataire',
            'tel_destinataire',
            'adresse_destinataire',
            'prix_total_invoice',
            'sous_total_produits',
            'service_info',
            'mode_payement',
            'produitsGroupes',
            'numero_facture',
            'totalMontantPaye',
            'restePaye',
            'devise'
        ));
    }


    public function editInvoice($id)
    {
        // Récupérer le colis principal
        $colis_principal = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
    
        // Récupérer les colis associés à ce colis principal
        $colisEnregistres = Colis::where('reference_colis', $colis_principal->reference_colis)->get();
        // dd($colisEnregistres);
        if ($colisEnregistres->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun colis n\'a été enregistré avec cette référence.');
        }
    
        // Collecter des informations pour afficher les détails
        $firstColis = $colisEnregistres->first();
        $firstInfo = [
            'id' => $firstColis->id,
            'reference_colis' => $firstColis->reference_colis,
            'nom_destinataire' => optional($firstColis->destinataire)->nom,
            'prenom_destinataire' => optional($firstColis->destinataire)->prenom,
            'tel_destinataire' => optional($firstColis->destinataire)->tel,
            'nom_expediteur' => optional($firstColis->expediteur)->nom,
            'prenom_expediteur' => optional($firstColis->expediteur)->prenom,
            'tel_expediteur' => optional($firstColis->expediteur)->tel,
            'devise' => optional($firstColis)->devise,
        ];
    
        $totalQuantite = $colisEnregistres->sum('quantite_colis');
        $totalPrixTransit = $colisEnregistres->sum('prix_transit_colis');
    
        // Préparer les paiements associés à ces colis
        $ids_colis = $colisEnregistres->pluck('id')->toArray();
        $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();
        $mode_payement = $paiements->pluck('methode_paiement')->unique()->first();
        $totalMontant = $paiements->sum('montant');
        $totalMontantPaye = $paiements->first()->montant_paye ?? 0;
    
        $restePaye = $totalMontant - $totalMontantPaye;
    
        // dd($firstInfo);
        return view('AFT_LOUIS_BLERIOT.invoice.edit', [
            'colis' => $colisEnregistres,
            'first' => $firstInfo,
            'totalQuantite' => $totalQuantite,
            'totalPrixTransit' => $totalPrixTransit,
            'restePaye' => $restePaye,
            'mode_payement' => $mode_payement,
            'totalMontantPaye' => $totalMontantPaye,
        ]);
    }



    public function storePayement(Request $request) // Renommée depuis storePayment pour correspondre à la route utilisée dans le JS
    {
        try {
            // Récupérer le montant total calculé précédemment et stocké en session
            $totalPrice = session('step1.total_prix', 0); // Mettre une valeur par défaut sûre
    
            // Définir les règles de base
            $rules = [
                'mode_payement' => 'required|in:bank,mobile_money,cheque,cash,delivery',
                'numero_compte' => 'required_if:mode_payement,bank|nullable|max:255',
                'nom_banque' => 'required_if:mode_payement,bank,cheque|nullable|max:255',
                'transaction_id' => 'required_if:mode_payement,bank|nullable|max:255', // Pas requis pour mobile_money car CinetPay le gère
                'numero_tel' => 'required_if:mode_payement,mobile_money|nullable|regex:/^\+?\d{10,15}$/', // Regex amélioré
                'operateur_mobile' => 'required_if:mode_payement,mobile_money|nullable|in:orange_money,wave,mtn_money', // Mettre à jour les valeurs possibles
                'numero_cheque' => 'required_if:mode_payement,cheque|nullable|max:255',
            ];
    
            // Ajouter la règle pour montant_reçu spécifiquement si le mode est 'cash'
            if ($request->input('mode_payement') === 'cash') {
                $rules['montant_reçu'] = [
                    'required',
                    'numeric',
                    'min:100', // Ou votre minimum requis
                    'max:' . $totalPrice // Validation par rapport au montant total
                ];
            } else {
                 // Rendre montant_reçu non requis et nullable pour les autres modes
                 $rules['montant_reçu'] = 'nullable|numeric';
            }
    
             // Définir les messages d'erreur personnalisés
             $messages = [
                 'required' => 'Le champ :attribute est obligatoire.',
                 'required_if' => 'Le champ :attribute est requis lorsque le mode de paiement est :value.',
                 'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
                 'numeric' => 'Le champ :attribute doit être un nombre.',
                 'min' => 'Le champ :attribute doit être au moins de :min.',
                 'montant_reçu.max' => 'Le montant reçu ne peut pas dépasser le montant total (' . number_format($totalPrice, 0, ',', ' ') . ' FCFA).',
                 'mode_payement.in' => 'Le mode de paiement sélectionné est invalide.',
                 'numero_tel.regex' => 'Le format du numéro de téléphone est invalide.',
                 'operateur_mobile.in' => 'L\'opérateur mobile sélectionné est invalide.',
             ];
    
            // Valider la requête
            $validatedData = $request->validate($rules, $messages);
    
            // --- Fin validation manuelle ---
    
            // Stocker uniquement les données validées pertinentes en session step2
            // Utiliser $validatedData pour s'assurer qu'on ne stocke que ce qui est validé
            $step2Data = collect($validatedData)->only([
                 'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id',
                 'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu'
            ])->all();
    
            // S'assurer que montant_reçu est null si le mode n'est pas cash
             if ($step2Data['mode_payement'] !== 'cash') {
                 $step2Data['montant_reçu'] = null;
             }
    
    
            session(['step2' => $step2Data]);
             // Ajouter le montant total attendu à step2 pour référence dans generer_qrcode
             session(['step2.montant_total_attendu' => $totalPrice]);
    
    
            return response()->json([
                'success' => true,
                'redirect' => route('aftlb_colis.generer.qrcode'),
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retourner les erreurs de validation au format JSON pour AJAX
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422); // Code 422 Unprocessable Entity
        } catch (\Exception $e) {
            // Log l'erreur serveur pour le débogage
             \Log::error('Erreur interne dans storePayement: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue. Veuillez réessayer plus tard.',
            ], 500); // Code 500 Internal Server Error
        }
    }
    


    public function complete()
    {

        return view('admin.colis.add.complete');
    }

    /**
     * Recherche automatique pour les clients.
     */
    public function search(Request $request)
    {
        $term = $request->get('term');
        $expediteurs = Client::where('type_client', 'expediteur')
            ->where(function ($query) use ($term) {
                $query->where('nom', 'LIKE', "%$term%")
                      ->orWhere('prenom', 'LIKE', "%$term%");
            })->get(['id', 'nom', 'prenom']);
        return response()->json([
            'results' => $expediteurs->map(fn($client) => [
                'id' => $client->id,
                'text' => $client->nom . ' ' . $client->prenom
            ])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = [
            'description' => $request->description,
            'quantite' => $request->quantite,
            'dimension' => $request->dimension,
            'prix' => $request->prix,
        ];
        return response()->json($product);
    }

    public function store_expediteur(Request $request)
    {
        // dd($request);
        $expediteur = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'agence' => $request->agence,
            'type_client' => $request->type_client ?? 'destinataire', // Par défaut 'destinataire'
        ]);
        return redirect()->back()->with('success', 'expediteur cree avec succès !');
    }

    public function store_destinataire(Request $request)
    {
        $destinataire = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'lieu_livraison' => $request->lieu_livraison,
            'agence' => $request->agence,
            'type_client' => $request->type_client ?? 'expediteur', // Par défaut 'expediteur'
        ]);

        return redirect()->back()->with('success', 'Destinataire créé avec succès !');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  

    public function hold()
    {
        return view('AFT_LOUIS_BLERIOT.colis.hold');
    }

    public function dump()
    {
        return view('AFT_LOUIS_BLERIOT.colis.dump');
    }

    public function history()
    {
        return view('AFT_LOUIS_BLERIOT.colis.history');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
 
    public function edit_hold($id)
    {
        $colis_principal = Colis::find($id);
        if (!$colis_principal) {
            return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
        }
    
        $colis_groupe_total = Colis::with(['expediteur', 'destinataire'])
                                    ->where('reference_colis', $colis_principal->reference_colis)
                                    ->get();
    
        if ($colis_groupe_total->isEmpty()) {
            return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun colis trouvé avec cette référence.');
        }
        
        // --- NOUVELLE LOGIQUE : REGROUPEMENT PAR SERVICE (NATURE DU COLIS) ---
        $groupes_par_service = $colis_groupe_total->groupBy('service');
    
        $colis_recap_list = [];
    
        foreach ($groupes_par_service as $service => $groupe) {
            $premier_colis_du_groupe = $groupe->first();
    
            $colis_recap_list[] = (object)[
                'expediteur' => $premier_colis_du_groupe->expediteur,
                'destinataire' => $premier_colis_du_groupe->destinataire,
                'mode_transit' => $premier_colis_du_groupe->mode_transit,
                'reference_colis' => $premier_colis_du_groupe->reference_colis,
                'service' => $service,
                
                'quantite_colis' => $groupe->count(), // La quantité est le nombre d'enregistrements
                'valeur_colis' => $groupe->sum('valeur_colis'),
                'poids_colis' => $groupe->sum('poids_colis'),
                'prix_transit_colis' => $groupe->sum('prix_transit_colis'),
                'dimension_result' => $groupe->pluck('dimension_result')->unique()->implode(' | '),
                'items' => $groupe
            ];
        }
        // --- FIN DE LA NOUVELLE LOGIQUE ---
    
        // On envoie la LISTE des fiches récapitulatives à la vue
        return view('AFT_LOUIS_BLERIOT.colis.edit_hold', ['colis_recap_list' => $colis_recap_list]);
    }



        
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function get_colis(Request $request)
{
    if ($request->ajax()) {
        $users = User::select(['id', 'first_name', 'email', 'role', 'created_at']);
        return DataTables::of($users)
            ->addColumn('action', function ($row) {
                $editUrl = '/users/' . $row->id . '/edit';

                return '
                    <div class="btn-group">
                        <a href="#" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#viewModal">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-credit-card"></i>
                        </a>
                    </div>
                   
                ';
            })
            ->rawColumns(['action']) // Permet de rendre le HTML
            ->make(true);
    }
}


public function get_colis_hold(Request $request)
{
    if ($request->ajax()) {
        try {
            \Log::info('Récupération des devis pour l\'agence de Chine');
            
            $devis = Devis::with('items')
                ->whereIn('etat', ['En attente', 'Validé'])
                ->where('devise', 'EUR')
                ->where('agence_expedition', 'AFT Agence Louis Bleriot')
                ->get();

            \Log::info('Nombre de devis trouvés: ' . $devis->count());

            $devisFormatted = $devis->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reference_colis' => $item->reference,
                    'nombre_de_colis' => $item->items->sum('quantite_colis'),
                    'expediteur_nom' => $item->nom_expediteur,
                    'expediteur_prenom' => $item->prenom_expediteur,
                    'expediteur_tel' => $item->tel_expediteur,
                    'destinataire_agence' => $item->agence_destination,
                    'etat' => $item->etat,
                    'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

            \Log::info('Formatage des données terminé');

            // Retourner les données dans le format attendu par DataTables
            return response()->json([
                'data' => $devisFormatted
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur dans get_colis_hold: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors du chargement des données',
                'data' => []
            ], 500);
        }
    }

    abort(404);
}
public function destroy_devis($id)
{
    try {
        // Trouver le devis
        $devis = Devis::findOrFail($id);
        
        // Vérifier que le devis est bien en attente
        if ($devis->etat !== 'En attente') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les devis en attente peuvent être supprimés.'
            ], 422);
        }
        
        // Supprimer les items associés au devis
        $devis->items()->delete();
        
        // Supprimer le devis
        $devis->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Devis supprimé avec succès.'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Erreur suppression devis: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression du devis: ' . $e->getMessage()
        ], 500);
    }
}
public function devisConfirme()
{
    // Retourne la vue située dans /views/AFT_LOUIS_BLERIOT/colis/devisconfirme.blade.php
    return view('AFT_LOUIS_BLERIOT.colis.devisconfirme');
}
public function get_devis_confirmes(Request $request)
{
    if ($request->ajax()) {
        $devis = Devis::with('items')
            // MODIFICATION CLÉ : On filtre par l'état 'confirmé'
            ->where('etat', 'confirmé') 
            ->where('agence_expedition', 'AFT Agence Louis Bleriot')
            ->get();

        $devisFormatted = $devis->map(function ($item) {
            return [
                'id' => $item->id,
                'reference_colis' => $item->reference,
                'nombre_de_colis' => $item->items->sum('quantite_colis'),
                'expediteur_nom' => $item->nom_expediteur,
                'expediteur_prenom' => $item->prenom_expediteur,
                'expediteur_tel' => $item->tel_expediteur,
                'expediteur_agence' => $item->agence_expedition,
                'destinataire_nom' => '', // Garder la structure cohérente
                'destinataire_prenom' => '',
                'destinataire_tel' => '',
                'destinataire_agence' => $item->agence_destination,
                'etat' => ucfirst($item->etat), // Met la première lettre en majuscule, ex: "Confirmé"
                'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                'details' => $item // Inutile pour la vue, mais ne pose pas de problème
            ];
        });

        return DataTables::of($devisFormatted)->make(true);
    }
}
public function get_devis_details($id)
{
    try {
        \Log::info("Tentative de récupération du devis ID: " . $id);
        
        $devis = Devis::with('items')
            ->where('id', $id)
            ->where('agence_expedition', 'AFT Agence Louis Bleriot')
            ->firstOrFail();

        \Log::info("Devis trouvé: " . $devis->reference);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $devis->id,
                'reference' => $devis->reference,
                'mode_transit' => $devis->mode_transit,
                'pays_expedition' => $devis->pays_expedition,
                'agence_expedition' => $devis->agence_expedition,
                'agence_destination' => $devis->agence_destination,
                'nom_expediteur' => $devis->nom_expediteur,
                'prenom_expediteur' => $devis->prenom_expediteur,
                'email_expediteur' => $devis->email_expediteur,
                'tel_expediteur' => $devis->tel_expediteur,
                'adresse_expediteur' => $devis->adresse_expediteur,
                'devise' => $devis->devise,
                'montant' => $devis->montant,
                'etat' => $devis->etat,
                'mode_de_retrait' => $devis->mode_de_retrait,
                'created_at' => $devis->created_at,
                'items' => $devis->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantite_colis' => $item->quantite_colis,
                        'service' => $item->service,
                        'valeur_colis' => $item->valeur_colis,
                        'type_colis' => $item->type_colis,
                        'description_colis' => $item->description_colis,
                        'poids' => $item->poids,
                        'longueur' => $item->longueur,
                        'largeur' => $item->largeur,
                        'hauteur' => $item->hauteur,
                    ];
                })
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error("Erreur lors de la récupération du devis: " . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Devis non trouvé: ' . $e->getMessage()
        ], 404);
    }
}
/**
     * NOUVELLE MÉTHODE : Affiche les détails d'un devis spécifique.
     */
    public function show($id)
    {
        // 1. Récupérer le devis avec ses items depuis la base de données
        // findOrFail() renverra une erreur 404 si le devis n'est pas trouvé
        $devis = Devis::with('items')->findOrFail($id);

        // dd( $devis);
        // 2. Retourner la nouvelle vue en lui passant les données du devis
        return view('AFT_LOUIS_BLERIOT.colis.show', compact('devis'));
    }

    public function get_colis_dump(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::query()
                ->select(
                    'colis.*',
                    'colis.reference_colis as reference_colis',
                    'expediteurs.nom as expediteur_nom',
                    'expediteurs.prenom as expediteur_prenom',
                    'expediteurs.tel as expediteur_tel',
                    'expediteurs.agence as expediteur_agence',
                    'destinataires.nom as destinataire_nom',
                    'destinataires.prenom as destinataire_prenom',
                    'destinataires.agence as destinataire_agence',
                    'destinataires.tel as destinataire_tel',
                    'colis.etat as etat',
                    'colis.created_at as created_at'
                )
                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                ->where('etat', 'Dechargé')
                ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                ->get();
            $colisGrouped = $colis->groupBy('reference_colis');


            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat, // conserve l'état d'origine ici
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();

            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Dechargé' ? 'Colis validé' : $row['etat'];
                })

                

                ->make(true);
        }
    }


    public function get_devis_colis(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                    'colis.id',
                    'colis.reference_colis',
                    'colis.etat',
                    'colis.created_at',
                    'expediteurs.nom as expediteur_nom', 
                    'expediteurs.prenom as expediteur_prenom', 
                    'expediteurs.tel as expediteur_tel', 
                    'expediteurs.agence as expediteur_agence', 
                    'destinataires.nom as destinataire_nom', 
                    'destinataires.prenom as destinataire_prenom', 
                    'destinataires.agence as destinataire_agence', 
                    'destinataires.tel as destinataire_tel'
                )
                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                ->whereIn('colis.etat', ['Devis', 'Validé'])
                ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                ->get()
                ->groupBy('reference_colis');
    
            $colisWithCount = $colis->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat === 'Devis' ? 'Dévis validé' : 'Colis validé',
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();
            return DataTables::of($colisWithCount)
                ->addColumn('action', function ($row) {
                    $printUrl = route('aftlb_colis.qrcode.edit', ['id' => $row['colis']->first()->id]);
                    return '
                        <div class="btn-group">
                            <a href="' . $printUrl . '" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function get_colis_valide(Request $request)
{
    if ($request->ajax()) {
        try {

            $colis = Colis::select(
                'colis.id',
                'colis.reference_colis',
                'colis.quantite_colis',
                'colis.prix_transit_colis',
                'colis.expediteur_id', // Garder les IDs si besoin pour les relations
                'colis.destinataire_id',
                'colis.etat',
                'colis.created_at',
                'expediteurs.nom as expediteur_nom',
                'expediteurs.prenom as expediteur_prenom',
                'expediteurs.tel as expediteur_tel',
                'expediteurs.agence as expediteur_agence',
                'destinataires.nom as destinataire_nom',
                'destinataires.prenom as destinataire_prenom',
                'destinataires.agence as destinataire_agence',
                'destinataires.tel as destinataire_tel'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.etat', 'Validé') // Filtrer par état 'Validé'
            ->where('colis.agence', 'AFT Agence Louis Bleriot')
            ->whereNull('colis.archived_at') // Exclure les colis archivés
            ->orderBy('colis.created_at', 'desc') // Optionnel: trier
            ->get();


            $colisIds = $colis->pluck('id')->unique()->toArray();

            $paiements = Paiement::whereIn('colis_id', $colisIds)
                                ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye')) // Sommer directement en SQL
                                ->groupBy('colis_id')
                                ->get()
                                ->keyBy('colis_id'); 

            $colisGrouped = $colis->groupBy('reference_colis');

            $processedData = $colisGrouped->map(function ($group, $reference) use ($paiements) {
                $firstColis = $group->first(); // Prendre le premier colis comme référence pour certaines infos
                $quantiteTotale = $group->sum('quantite_colis');
                $prixTotalColis = $group->sum('prix_transit_colis');
                $montantTotalPaye = 0;
                $colisIdsInGroup = $group->pluck('id')->toArray(); // IDs des colis dans ce groupe

                foreach ($colisIdsInGroup as $colisId) {
                    if (isset($paiements[$colisId])) {
                        $montantTotalPaye += $paiements[$colisId]->total_paye;
                    }
                }

                $paymentStatus = 'impaye';
                $tolerance = 0.01; // Tolérance pour les comparaisons flottantes

                if ($montantTotalPaye > 0) {
                    if (abs($prixTotalColis - $montantTotalPaye) < $tolerance) {
                        $paymentStatus = 'paye'; // Totalement payé
                    } elseif ($montantTotalPaye < $prixTotalColis) {
                        $paymentStatus = 'partiel'; // Partiellement payé
                    }
                }

                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $quantiteTotale, // Somme des quantités
                    'expediteur_nom' => $firstColis->expediteur_nom,
                    'expediteur_prenom' => $firstColis->expediteur_prenom,
                    'expediteur_tel' => $firstColis->expediteur_tel,
                    'expediteur_agence' => $firstColis->expediteur_agence,
                    'destinataire_nom' => $firstColis->destinataire_nom,
                    'destinataire_prenom' => $firstColis->destinataire_prenom,
                    'destinataire_tel' => $firstColis->destinataire_tel,
                    'destinataire_agence' => $firstColis->destinataire_agence,
                    'etat' => $firstColis->etat, // L'état devrait être le même pour tout le groupe
                    'created_at' => $firstColis->created_at ? $firstColis->created_at->format('d/m/Y H:i') : 'N/A', // Formatage de la date
                    'payment_status' => $paymentStatus, // Statut calculé
                    'prix_total' => $prixTotalColis, // Prix total du groupe
                    'montant_paye' => $montantTotalPaye, // Montant total payé pour le groupe
                    'colis_ids' => json_encode($colisIdsInGroup), // IDs du groupe en JSON pour le bouton Payer
                    'first_colis_id' => $firstColis->id // ID du premier colis pour Edit/Invoice
                ];
            })->values(); // Transformer la collection en tableau indexé numériquement

            return DataTables::of($processedData)
                ->addColumn('statut_paiement', function ($row) {
                    // Générer l'icône de statut de paiement avec tooltip
                    $status = $row['payment_status'];
                    $iconClass = ''; $iconColor = ''; $title = '';
                    $montantPayeFormatted = number_format($row['montant_paye'], 2, ',', ' ');
                    $prixTotalFormatted = number_format($row['prix_total'], 2, ',', ' ');
                    switch ($status) {
                        case 'paye':
                            $iconClass = 'fas fa-check-circle'; $iconColor = 'green';
                            $title = 'Payé (' . $montantPayeFormatted . ' / ' . $prixTotalFormatted . ')';
                            break;
                        case 'partiel':
                            $iconClass = 'fas fa-exclamation-circle'; $iconColor = 'orange';
                            $title = 'Paiement Partiel (' . $montantPayeFormatted . ' / ' . $prixTotalFormatted . ')';
                            break;
                        case 'impaye':
                        default:
                            $iconClass = 'fas fa-times-circle'; $iconColor = 'red';
                            $title = 'Impayé (0 / ' . $prixTotalFormatted . ')';
                            break;
                    }
                    return '<span title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"><i class="' . $iconClass . '" style="color: ' . $iconColor . '; font-size: 1.3em;"></i></span>';
                })
                ->addColumn('action', function ($row) {
                    // Générer les boutons d'action
                    $reference = $row['reference_colis'];
                    $firstColisId = $row['first_colis_id']; // ID pour Edit/Invoice

                    $editUrl = route('aftlb_colis.valide.edit', ['id' => $firstColisId]); // Route pour modifier (utilise l'ID)
                    $invoiceUrl = route('aftlb_colis.valide.edit.invoice', ['id' => $firstColisId]); // Route pour la facture (utilise l'ID)
                    $deleteUrl = route('aftlb_colis.destroy.colis.valide', ['reference' => $reference]); // Route pour archiver (utilise la référence)

                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modifier le colis groupé">
                                    <i class="fas fa-edit"></i>
                                </a>';

                    $payBtn = '<button type="button" class="btn btn-sm btn-success pay-btn"
                                        data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                        data-total="' . $row['prix_total'] . '"
                                        data-paid="' . $row['montant_paye'] . '"
                                        data-colis-ids="' . htmlspecialchars($row['colis_ids'], ENT_QUOTES, 'UTF-8') . '"
                                        title="Enregistrer un Paiement pour la référence ' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>';

                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                            data-url="' . $deleteUrl . '"
                                            title="Archiver la référence ' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '">
                                        <i class="fas fa-trash"></i>
                                    </button>';

                    $invoiceBtn = '<a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Voir la Facture">
                                    <i class="fas fa-file-invoice"></i>
                                   </a>';

                    return '<div class="action-buttons-container">'
                           . $editBtn
                           . $payBtn
                           . $deleteBtn
                           . $invoiceBtn
                           . '</div>';
                })
                ->rawColumns(['action', 'statut_paiement'])
                ->make(true); 

        } catch (\Exception $e) {
            Log::error('Erreur dans get_colis_valide: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur interne est survenue.'], 500);
        }
    }

    Log::warning("Requête non-AJAX reçue sur get_colis_valide");
    abort(404); 
}

public function enregistrer_paiement(Request $request)
{
    $validated = $request->validate([
        'reference_colis' => 'required|string|exists:colis,reference_colis',
        'montant_a_payer' => 'required|numeric|min:0.01',
        'colis_ids'       => 'required|json', 
    ]);

    // dd($validated);
    try {
        $colisIdsJson = $validated['colis_ids'];
        $nouveauMontantPaye = (float) $validated['montant_a_payer'];
        $referenceColis = $validated['reference_colis'];

        $colisIds = json_decode($colisIdsJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($colisIds) || empty($colisIds)) {
            Log::error('JSON colis_ids invalide ou vide reçu:', ['json_string' => $colisIdsJson]);
            return response()->json(['error' => 'Liste des IDs de colis invalide ou vide.'], 400);
        }

        $firstColisId = $colisIds[0];

        $colisExists = Colis::where('id', $firstColisId)
                            ->where('reference_colis', $referenceColis)
                            ->exists();

        if (!$colisExists) {
            Log::warning('Incohérence détectée : colis non trouvé malgré la validation.', ['id' => $firstColisId, 'ref' => $referenceColis]);
            return response()->json(['error' => 'Colis de référence non trouvé pour enregistrer le paiement.'], 404);
        }

        DB::beginTransaction();

        $ancienMontantPaye = Paiement::where('colis_id', $firstColisId)->sum('montant_paye');

        $montantTotal = $ancienMontantPaye + $nouveauMontantPaye;

        Paiement::create([
            'colis_id'         => $firstColisId, 
            'montant_paye'     => $nouveauMontantPaye,
            'date_paiement'    => now(), 
            'methode_paiement' => $request->input('methode_paiement', 'Espèce'),
        ]);

        DB::commit();

        return response()->json([
            'success' => 'Paiement enregistré avec succès pour la référence ' . $referenceColis,
            'ancien_montant_paye' => $ancienMontantPaye,
            'nouveau_montant'     => $nouveauMontantPaye,
            'montant_total'       => $montantTotal
        ]);

    } catch (ValidationException $e) {
        Log::error("Erreur de validation paiement: " . $e->getMessage(), $e->errors());
        return response()->json(['error' => 'Données invalides.', 'details' => $e->errors()], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur enregistrement paiement: " . $e->getMessage() . ' dans ' . $e->getFile() . ' ligne ' . $e->getLine());
        return response()->json(['error' => 'Une erreur technique est survenue lors de l\'enregistrement du paiement.'], 500);
    }
}


        // function de suppression des colis validés
    public function destroy_colis_valide($reference)
    {
        try {
            // Récupère tous les colis avec la même référence
            $colisList = Colis::where('reference_colis', $reference)
                ->whereNull('archived_at') // éviter de réarchiver
                ->get();
                // dd($colisList);
    
            if ($colisList->isEmpty()) {
                return response()->json(['error' => 'Aucun colis trouvé pour cette référence.'], 404);
            }
    
            foreach ($colisList as $colis) {
                $colis->delete();
                // $colis->archived_at = now();
                // $colis->save();
            }
    
            return response()->json(['success' => 'Colis archivés avec succès !']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'archivage : ' . $e->getMessage()], 500);
        }
    }


    public function edit_colis_valide($id)
    {
        // findOrFail gère automatiquement le cas où le colis n'existe pas (erreur 404).
        $colis_principal = Colis::findOrFail($id);

        // Récupérer tous les colis de la même référence avec leurs relations.
        $colis_groupe_total = Colis::with(['expediteur', 'destinataire'])
            ->where('reference_colis', $colis_principal->reference_colis)
            ->get();

        if ($colis_groupe_total->isEmpty()) {
            return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun colis trouvé avec cette référence.');
        }

        // 1. On récupère les informations globales (partagées par tous les colis de la transaction)
        $info_partagees = $colis_groupe_total->first();

        // 2. On groupe les colis par 'produit' pour un affichage clair et cohérent.
        $groupes_par_produit = $colis_groupe_total->groupBy('produit');

        // 3. Transformer les groupes en une liste d'objets récapitulatifs pour la vue.
        $colis_recap_list = $groupes_par_produit->map(function ($groupe, $produit) {
            $premier_colis_du_groupe = $groupe->first();

            return (object)[
                'produit'            => $produit,
                'quantite_colis'     => $groupe->sum('quantite_colis'),
                'poids_colis'        => $groupe->sum('poids_colis'),
                'prix_transit_colis' => $groupe->sum('prix_transit_colis'),
                'mode_transit'       => $premier_colis_du_groupe->mode_transit,
                'reference_colis'    => $premier_colis_du_groupe->reference_colis,
                'id_reference'       => $premier_colis_du_groupe->id_reference,
                'devise'             => $premier_colis_du_groupe->devise,
                'montant_service'    => $premier_colis_du_groupe->montant_service,
                'service'            => $premier_colis_du_groupe->service,
                'dimension_result'   => $groupe->pluck('dimension_result')->unique()->implode(' | '),
                'items'              => $groupe, // On garde les items pour récupérer leurs IDs dans la vue.
            ];
        })->values();

        return view('AFT_LOUIS_BLERIOT.colis.edit_colis_valide', [
            'colis_recap_list' => $colis_recap_list,
            'info_partagees'   => $info_partagees,
        ]);
    }

    public function updateMultipleColis(Request $request)
    {
        $validatedData = $request->all();
        $finalRedirectColisId = null; // Variable pour stocker l'ID du colis pour la redirection

        try {
            DB::transaction(function () use ($validatedData, $request, &$finalRedirectColisId) {
                $id_reference = $validatedData['id_reference'];

                // ================================================================
                // 1. CRÉATION / MISE À JOUR EXPÉDITEUR ET DESTINATAIRE
                // ================================================================
                $expediteur = Expediteur::updateOrCreate(
                    ['tel' => $validatedData['tel_expediteur']],
                    [
                        'nom'    => $validatedData['nom_expediteur'],
                        'prenom' => $validatedData['prenom_expediteur'],
                        'agence' => $validatedData['agence_expediteur']
                    ]
                );

                $destinataire = Destinataire::updateOrCreate(
                    ['tel' => $validatedData['tel_destinataire']],
                    [
                        'nom'    => $validatedData['nom_destinataire'],
                        'prenom' => $validatedData['prenom_destinataire'],
                        'agence' => $validatedData['agence_destinataire']
                    ]
                );

                // ================================================================
                // 2. MISE À JOUR DES COLIS EXISTANTS
                // ================================================================
                if (!empty($validatedData['groupes'])) {
                    foreach ($validatedData['groupes'] as $groupData) {
                        if (empty($groupData['colis_ids'])) continue;

                        $colisIds = $groupData['colis_ids'];
                        $quantite = count($colisIds);

                        $prixTotalGroupe = (float)($groupData['prix_transit_colis'] ?? 0);
                        $poidsTotalGroupe = (float)($groupData['poids_colis'] ?? 0);

                        $prixUnitaire = $quantite > 0 ? $prixTotalGroupe / $quantite : 0;
                        $poidsUnitaire = $quantite > 0 ? $poidsTotalGroupe / $quantite : 0;

                        Colis::whereIn('id', $colisIds)->update([
                            'expediteur_id'      => $expediteur->id,
                            'destinataire_id'    => $destinataire->id,
                            'agence'             => $validatedData['agence_expediteur'],
                            'produit'            => $groupData['produit'],
                            'mode_transit'       => $groupData['mode_transit'],
                            'dimension_result'   => $groupData['dimension_result'] ?? null,
                            'poids_colis'        => $poidsUnitaire,
                            'prix_transit_colis' => $prixUnitaire,
                            'service'            => $validatedData['service'] ?? null,
                            'montant_service'    => isset($validatedData['service']) ? ($validatedData['montant_service'] ?? 0) : null,
                        ]);

                        // Capture le premier colis existant si aucun nouveau n'est créé
                        if ($finalRedirectColisId === null && !empty($colisIds)) {
                            $finalRedirectColisId = $colisIds[0];
                        }
                    }
                }

                // ================================================================
                // 3. SUPPRESSION DES COLIS
                // ================================================================
                if ($request->has('deleted_ids')) {
                    $idsASupprimer = array_filter($request->input('deleted_ids'));
                    if (!empty($idsASupprimer)) {
                        $colisASupprimer = Colis::whereIn('id', $idsASupprimer)->get();

                        foreach ($colisASupprimer as $colis) {
                            $fullPath = public_path($colis->qr_code_path);
                            if ($colis->qr_code_path && File::exists($fullPath)) {
                                File::delete($fullPath);
                            }
                        }
                        Colis::destroy($idsASupprimer);
                    }
                }

                // ================================================================
                // 4. CRÉATION DE NOUVEAUX COLIS
                // ================================================================
                if (!empty($validatedData['new_group'])) {
                    foreach ($validatedData['new_group'] as $newGroupData) {
                        $quantite = max(1, (int)($newGroupData['quantite_colis'] ?? 1));
                        $prixTotalGroupe = (float)($newGroupData['prix_transit_colis'] ?? 0);
                        $poidsTotalGroupe = (float)($newGroupData['poids_colis'] ?? 0);

                        $prixUnitaire = $quantite > 0 ? $prixTotalGroupe / $quantite : 0;
                        $poidsUnitaire = $quantite > 0 ? $poidsTotalGroupe / $quantite : 0;

                        for ($i = 0; $i < $quantite; $i++) {
                            $colisModel = Colis::create([
                                'reference_colis'    => $validatedData['reference_colis'],
                                'id_reference'       => $id_reference,
                                'mode_transit'       => $newGroupData['mode_transit'] ?? 'maritime',
                                'expediteur_id'      => $expediteur->id,
                                'destinataire_id'    => $destinataire->id,
                                'agent_id'           => auth()->user()->agent->id ?? null,
                                'agence'             => $validatedData['agence_expediteur'],
                                'produit'            => $newGroupData['produit'],
                                'devise'             => $validatedData['devise'] ?? '€',
                                'quantite_colis'     => 1,
                                'poids_colis'        => $poidsUnitaire,
                                'prix_transit_colis' => $prixUnitaire,
                                'dimension_result'   => $newGroupData['dimension'] ?? null,
                                'description_colis'  => $newGroupData['description_colis'] ?? $newGroupData['produit'],
                                'status'             => 'en_attente',
                                'etat'               => 'Validé',
                                'qr_code_path'       => null,
                                'service'            => $validatedData['service'] ?? null,
                                'montant_service'    => isset($validatedData['service']) ? ($validatedData['montant_service'] ?? 0) : null,
                            ]);

                            // Génération du QR Code
                            $qrCodePath = $this->generateAndStoreQrCode($colisModel, $expediteur, $destinataire);
                            $colisModel->update(['qr_code_path' => $qrCodePath]);

                            if ($finalRedirectColisId === null) {
                                $finalRedirectColisId = $colisModel->id;
                            }
                        }
                    }
                }

                // ================================================================
                // 5. FILET DE SÉCURITÉ : si aucun colis capturé, on en prend un au hasard
                // ================================================================
                if ($finalRedirectColisId === null) {
                    $anyRemainingColis = Colis::where('id_reference', $id_reference)->first();
                    if ($anyRemainingColis) {
                        $finalRedirectColisId = $anyRemainingColis->id;
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error("Erreur lors de la mise à jour groupée des colis: " . $e->getMessage() . " Ligne: " . $e->getLine());
            return back()->with('error', 'Une erreur critique est survenue. Aucune modification n\'a été enregistrée.');
        }

        // ================================================================
        // 6. REDIRECTION FINALE
        // ================================================================
        if ($finalRedirectColisId) {
            return redirect()->route('aftlb_colis.valide.edit.invoice', ['id' => $finalRedirectColisId])
                ->with('success', 'Les modifications ont été enregistrées avec succès !');
        }

        return redirect()->route('aftlb_colis.colis.valide')
            ->with('success', 'Les modifications ont été enregistrées avec succès !');
    }

    private function generateAndStoreQrCode(Colis $colis, Expediteur $expediteur, Destinataire $destinataire): string
    {
        // 1. Contenu du QR Code
        $qrData = [
            'ID'     => $colis->id,
            'Ref'    => $colis->reference_colis,
            'Exp'    => $expediteur->nom . ' ' . $expediteur->prenom,
            'Dest'   => "{$destinataire->nom} / {$destinataire->tel}",
            'Agence' => $colis->agence
        ];
        // Transformer en texte ligne par ligne
        $qrCodeContent = implode("\n", array_map(
            fn($k, $v) => "$k: $v",
            array_keys($qrData),
            array_values($qrData)
        ));

        // Générer le QR code
        $qrCode = QrCode::create($qrCodeContent)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $pngResult = $writer->write($qrCode);

        // 3. Nom unique
        $safeRef = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $colis->reference_colis);
        $fileName = 'colis_' . $safeRef . '_id' . $colis->id . '_' . time() . '.png';
        $filePath = 'qrcodes/' . $fileName;
        $fullPath = public_path($filePath);

        // 4. Créer le dossier si inexistant
        $directory = dirname($fullPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // 5. Sauvegarder l'image
        file_put_contents($fullPath, $pngResult->getString());

        // 6. Retourner le chemin relatif (public/qrcodes/...)
        return $filePath;
    }
    

    public function update_colis_valide(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            // 'destinataire_agence' => 'required|string|max:255',
            // 'destinataire_tel' => 'required|string|max:255',
            // 'quantite_colis' => 'required|numeric',
            // 'valeur_colis' => 'required|numeric',
            // 'mode_transit' => 'required|string|max:255',
            // 'poids_colis' => 'required|numeric',
            // 'prix_transit_colis' => 'required|numeric',
        ]);
    
        // Récupération du colis
        $colis = Colis::findOrFail($id);
        $request->validate([
            'nom_expediteur' => 'required|string|max:255',
            'prenom_expediteur' => 'required|string|max:255',
            'destinataire_tel' => 'required|string|max:15', // Ajustez la validation selon vos besoins
            'agence_expediteur' => 'required|string|max:255',
            'nom_destinataire' => 'required|string|max:255',
            'prenom_destinataire' => 'required|string|max:255',
            'destinataire_tel' => 'required|string|max:15',
            'agence_destinataire' => 'required|string|max:255',
            'quantite_colis' => 'required|integer|min:1',
            'valeur_colis' => 'required|numeric|min:0',
            'mode_transit' => 'nullable|string|max:255',
            'poids_colis' => 'required|numeric|min:0',
            'prix_transit_colis' => 'required|numeric|min:0',
            'service' => 'required|numeric|min:0',
            
        ]);
    
        // Mise à jour des informations du colis
        $colis->update([
            'nom_expediteur' => $request->nom_expediteur,
            'prenom_expediteur' => $request->prenom_expediteur,
            'tel_expediteur' => $request->destinataire_tel,
            'agence_expediteur' => $request->agence_expediteur,
            'nom_destinataire' => $request->nom_destinataire,
            'prenom_destinataire' => $request->prenom_destinataire,
            'tel_destinataire' => $request->destinataire_tel,
            'agence_destinataire' => $request->agence_destinataire,
            'quantite_colis' => $request->quantite_colis,
            'valeur_colis' => $request->valeur_colis,
            'mode_transit' => $request->mode_transit,
            'poids_colis' => $request->poids_colis,
            'service' => $request->service,
            'prix_transit_colis' => $request->prix_transit_colis,
        ]);
        // dd($colis);
        // Redirection avec un message de succès
        return redirect()->route('aftlb_colis.colis.valide')->with('success', 'Colis mis à jour avec succès !');
    }


    public function print_facture($id)
    {
        $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        
        // Retournez une vue pour l'impression
        return view('AFT_LOUIS_BLERIOT.colis.colis_facture', compact('colis'));
    }
    
    public function colis_valide(Request $request)
    {
        return view('AFT_LOUIS_BLERIOT.colis.valide');
    }
    public function tout_colis(Request $request)
    {
        return view('AFT_LOUIS_BLERIOT.colis.tout_colis');
    }

        public function get_tout_colis(Request $request)
    {
        if ($request->ajax()) {
            try {

                $colis = Colis::select(
                    'colis.id',
                    'colis.reference_colis',
                    'colis.quantite_colis',
                    'colis.prix_transit_colis',
                    'colis.expediteur_id', // Garder les IDs si besoin pour les relations
                    'colis.destinataire_id',
                    'colis.etat',
                    'colis.created_at',
                    'expediteurs.nom as expediteur_nom',
                    'expediteurs.prenom as expediteur_prenom',
                    'expediteurs.tel as expediteur_tel',
                    'expediteurs.agence as expediteur_agence',
                    'destinataires.nom as destinataire_nom',
                    'destinataires.prenom as destinataire_prenom',
                    'destinataires.agence as destinataire_agence',
                    'destinataires.tel as destinataire_tel'
                )
                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                // ->where('colis.etat', 'Validé')
                ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                ->whereNull('colis.archived_at') // Exclure les colis archivés
                ->orderBy('colis.created_at', 'desc') // Optionnel: trier
                ->get();

                $colisIds = $colis->pluck('id')->unique()->toArray();

                $paiements = Paiement::whereIn('colis_id', $colisIds)
                                    ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye')) // Sommer directement en SQL
                                    ->groupBy('colis_id')
                                    ->get()
                                    ->keyBy('colis_id'); 

                $colisGrouped = $colis->groupBy('reference_colis');

                $processedData = $colisGrouped->map(function ($group, $reference) use ($paiements) {
                    $firstColis = $group->first(); // Prendre le premier colis comme référence pour certaines infos
                    $quantiteTotale = $group->sum('quantite_colis');
                    $prixTotalColis = $group->sum('prix_transit_colis');
                    $montantTotalPaye = 0;
                    $colisIdsInGroup = $group->pluck('id')->toArray(); // IDs des colis dans ce groupe

                    foreach ($colisIdsInGroup as $colisId) {
                        if (isset($paiements[$colisId])) {
                            $montantTotalPaye += $paiements[$colisId]->total_paye;
                        }
                    }

                    $paymentStatus = 'impaye';
                    $tolerance = 0.01; // Tolérance pour les comparaisons flottantes

                    if ($montantTotalPaye > 0) {
                        if (abs($prixTotalColis - $montantTotalPaye) < $tolerance) {
                            $paymentStatus = 'paye'; // Totalement payé
                        } elseif ($montantTotalPaye < $prixTotalColis) {
                            $paymentStatus = 'partiel'; // Partiellement payé
                        }
                    }

                    return [
                        'reference_colis' => $reference,
                        'nombre_de_colis' => $quantiteTotale, // Somme des quantités
                        'expediteur_nom' => $firstColis->expediteur_nom,
                        'expediteur_prenom' => $firstColis->expediteur_prenom,
                        'expediteur_tel' => $firstColis->expediteur_tel,
                        'expediteur_agence' => $firstColis->expediteur_agence,
                        'destinataire_nom' => $firstColis->destinataire_nom,
                        'destinataire_prenom' => $firstColis->destinataire_prenom,
                        'destinataire_tel' => $firstColis->destinataire_tel,
                        'destinataire_agence' => $firstColis->destinataire_agence,
                        'etat' => $firstColis->etat, // L'état devrait être le même pour tout le groupe
                        'created_at' => $firstColis->created_at ? $firstColis->created_at->format('d/m/Y H:i') : 'N/A', // Formatage de la date
                        'payment_status' => $paymentStatus, // Statut calculé
                        'prix_total' => $prixTotalColis, // Prix total du groupe
                        'montant_paye' => $montantTotalPaye, // Montant total payé pour le groupe
                        'colis_ids' => json_encode($colisIdsInGroup), // IDs du groupe en JSON pour le bouton Payer
                        'first_colis_id' => $firstColis->id // ID du premier colis pour Edit/Invoice
                    ];
                })->values(); // Transformer la collection en tableau indexé numériquement

                return DataTables::of($processedData)
                    ->addColumn('statut_paiement', function ($row) {
                        // Générer l'icône de statut de paiement avec tooltip
                        $status = $row['payment_status'];
                        $iconClass = ''; $iconColor = ''; $title = '';
                        $montantPayeFormatted = number_format($row['montant_paye'], 2, ',', ' ');
                        $prixTotalFormatted = number_format($row['prix_total'], 2, ',', ' ');
                        switch ($status) {
                            case 'paye':
                                $iconClass = 'fas fa-check-circle'; $iconColor = 'green';
                                $title = 'Payé (' . $montantPayeFormatted . ' / ' . $prixTotalFormatted . ')';
                                break;
                            case 'partiel':
                                $iconClass = 'fas fa-exclamation-circle'; $iconColor = 'orange';
                                $title = 'Paiement Partiel (' . $montantPayeFormatted . ' / ' . $prixTotalFormatted . ')';
                                break;
                            case 'impaye':
                            default:
                                $iconClass = 'fas fa-times-circle'; $iconColor = 'red';
                                $title = 'Impayé (0 / ' . $prixTotalFormatted . ')';
                                break;
                        }
                        return '<span title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"><i class="' . $iconClass . '" style="color: ' . $iconColor . '; font-size: 1.3em;"></i></span>';
                    })
                    ->addColumn('action', function ($row) {
                        // Générer les boutons d'action
                        $reference = $row['reference_colis'];
                        $firstColisId = $row['first_colis_id']; // ID pour Edit/Invoice

                        $invoiceUrl = route('aftlb_colis.valide.edit.invoice', ['id' => $firstColisId]); // Route pour la facture (utilise l'ID)
                        $deleteUrl = route('aftlb_colis.destroy.colis.valide', ['reference' => $reference]); // Route pour archiver (utilise la référence)

                     

                        $payBtn = '<button type="button" class="btn btn-sm btn-success pay-btn"
                                            data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                            data-total="' . $row['prix_total'] . '"
                                            data-paid="' . $row['montant_paye'] . '"
                                            data-colis-ids="' . htmlspecialchars($row['colis_ids'], ENT_QUOTES, 'UTF-8') . '"
                                            title="Enregistrer un Paiement pour la référence ' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '">
                                        <i class="fas fa-dollar-sign"></i>
                                    </button>';

                        $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn"
                                                data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                                data-url="' . $deleteUrl . '"
                                                title="Archiver la référence ' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '">
                                            <i class="fas fa-trash"></i>
                                        </button>';


                        return '<div class="action-buttons-container">'
                               . $payBtn
                               . $deleteBtn
                               . '</div>';
                    })
                    ->rawColumns(['action', 'statut_paiement'])
                    ->make(true); 

            } catch (\Exception $e) {
                Log::error('Erreur dans get_colis_valide: ' . $e->getMessage());
                return response()->json(['error' => 'Une erreur interne est survenue.'], 500);
            }
        }

        Log::warning("Requête non-AJAX reçue sur get_colis_valide");
        abort(404); 
    }

    



    public function edit_qrcode($id)
    {
        // Récupérer tous les colis qui appartiennent au lot (par exemple, colonne "hold_id")
        $colis = Colis::where('id', $id)->get();

        // dd($colis);
        if ($colis->isEmpty()) {
            abort(404, "Aucun colis trouvé pour cet identifiant.");
        }

        // Pour chaque colis, générer le QR code
        foreach ($colis as $colisItem) {
            $qrData = [
                'Identifiant'       => $colisItem->id,
                'Référence colis'       => $colisItem->reference_colis,
                'Statut'                => $colisItem->status,
                'Nom Expéditeur'        => $colisItem->expediteur->nom . ' ' . $colisItem->expediteur->prenom,
                'Nom Destinataire'      => $colisItem->destinataire->nom . ' ' . $colisItem->destinataire->prenom,
                'Téléphone Destinataire'=> $colisItem->destinataire->tel,
                'Agence Destination'    => $colisItem->destinataire->agence ?? '',
                'Lieu de Destination'   => $colisItem->destinataire->lieu_destination ?? '',
            ];

            // Construire le contenu du QR code
            $qrCodeContent = '';
            foreach ($qrData as $key => $value) {
                $qrCodeContent .= "{$key}: {$value}\n";
            }

            // Générer le QR code
            $qrCode = new QrCode($qrCodeContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $pngData = $result->getString();

            // Définir le chemin du fichier QR code pour ce colis
            $filePath = 'qrcodes/colis_' . $colisItem->id . '.png';
            $fullPath = public_path($filePath);

            // Créer le répertoire si nécessaire
            $directory = dirname($fullPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Sauvegarder le QR code
            file_put_contents($fullPath, $pngData);

            // Mettre à jour le chemin du QR code dans la base de données
            $colisItem->update(['qr_code_path' => $filePath]);
        }

        return view('AFT_LOUIS_BLERIOT.devis.edit_qrcode', compact('colis'));
    }

    public function get_colis_contenaire(Request $request)
    {

        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.id', // Ajout de l'id pour éviter les erreurs
                'colis.reference_colis',
                'expediteurs.nom as expediteur_nom', 
                'expediteurs.prenom as expediteur_prenom', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as expediteur_agence', 
                'destinataires.nom as destinataire_nom', 
                'destinataires.prenom as destinataire_prenom', 
                'destinataires.agence as destinataire_agence', 
                'destinataires.tel as destinataire_tel',
                'colis.etat',
                'colis.created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.mode_transit', 'maritime')  
            ->where('colis.etat', 'Chargé')
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get();
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat,
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->toIso8601String() : null,
                    'colis' => $group,
                    'id' => $group->first()->id // Ajout de l'ID pour action
                ];
            })->values();
    
            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Chargé' ? 'Dévis Chargé' : 'Colis Chargé';
                })
                ->addColumn('action', function ($row) {
                    $deleteUrl = route('colis.destroy.colis.valide', ['reference' => $row['reference_colis']]);
                    return '
                       <div class="d-flex align-items-center gap-2">
                            <div class="btn-group">
                                <a href="' . $deleteUrl . '" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div> 
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function get_colis_vol(Request $request)
    {

        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.id', // Ajout de l'id pour éviter les erreurs
                'colis.reference_colis',
                'expediteurs.nom as expediteur_nom', 
                'expediteurs.prenom as expediteur_prenom', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as expediteur_agence', 
                'destinataires.nom as destinataire_nom', 
                'destinataires.prenom as destinataire_prenom', 
                'destinataires.agence as destinataire_agence', 
                'destinataires.tel as destinataire_tel',
                'colis.etat',
                'colis.created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.mode_transit', 'Aerien')
            ->where('colis.etat', 'Chargé')
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get();
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat,
                   'created_at' => $group->first()->created_at ? $group->first()->created_at->toIso8601String() : null,
                    'colis' => $group,
                    'id' => $group->first()->id // Ajout de l'ID pour action
                ];
            })->values();
    
            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Chargé' ? 'Dévis Chargé' : 'Colis Chargé';
                })
                ->addColumn('action', function ($row) {
                    $deleteUrl = route('colis.destroy.colis.valide', ['reference' => $row['reference_colis']]);
                    return '
                       <div class="d-flex align-items-center gap-2">
                            <div class="btn-group">
                                <a href="' . $deleteUrl . '" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div> 
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function get_cargaison_ferme(Request $request)
    {
        if ($request->ajax()) {
            $bateaux = Bateaux::select(
                'id',
                'reference_bateau',
                'reference_conteneur',
                'created_at as date_depart',
                'date_arriver'
                )->where('agence_expedition', 'AFT Agence Louis Bleriot')
                ->get();
            return DataTables::of($bateaux)
                ->editColumn('date_depart', function ($row) {
                    return $row->date_depart ? \Carbon\Carbon::parse($row->date_depart)->format('d/m/Y H:i') : 'N/A';
                })
                ->editColumn('date_arriver', function ($row) {
                    return $row->date_arriver ? \Carbon\Carbon::parse($row->date_arriver)->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('actions', function ($row) {
                    $editUrl = route('aftlb_colis.bateaux.edit', $row->id);
                    $deleteUrl = route('aftlb_colis.bateaux.destroy', $row->id);
                    $listColisUrl = route('aftlb_colis.liste.bateau', $row->reference_conteneur);
                
                    return '
                        <div class="d-flex justify-content-center gap-1">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning rounded-circle" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="' . $listColisUrl . '" class="btn btn-sm btn-info rounded-circle" title="Voir les colis">
                                <i class="fas fa-box"></i>
                            </a>
                            <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Confirmer la suppression ?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn dt-button btn-sm btn-danger rounded-circle" title="Annuler">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                
                            
                        </div>
                    ';
                })            
                ->rawColumns(['actions'])
                ->make(true);
        }
    }
    
    public function edit_bateaux($id)
    {
        $bateau = Bateaux::findOrFail($id);
        return view('AFT_LOUIS_BLERIOT.cargaison.edit_bateau', compact('bateau'));
    }
    
    public function destroy_bateaux($id)
    {
        $bateau = Bateaux::findOrFail($id);
        $bateau->delete();
    
        return redirect()->back()->with('success', 'Bateau supprimé.');
    }
    
public function liste_colis_par_bateau($reference_conteneur)
{
     $colis = Colis::where(function($query) use ($reference_conteneur) {
                    $query->where('reference_contenaire', $reference_conteneur)
                        ->orWhere(function($q) use ($reference_conteneur) {
                            $q->whereNull('reference_contenaire')
                                ->where('reference_colis', 'like', "%-$reference_conteneur");
                        });
                })
                ->selectRaw('reference_colis, SUM(quantite_colis) as quantite_colis, SUM(poids_colis) as poids_colis')
                ->groupBy('reference_colis')
                ->get();

    return view('AFT_LOUIS_BLERIOT.cargaison.liste_bateau', compact('colis'));
}



public function get_colis_bateau(Request $request)
{
    if ($request->ajax()) {
        // 1. Récupération des colis avec reference_contenaire NON NULL
        $colis = Colis::select(
                'colis.*',
                'expediteurs.nom as expediteur_nom',
                'expediteurs.prenom as expediteur_prenom',
                'expediteurs.tel as expediteur_tel',
                'expediteurs.agence as expediteur_agence',
                'destinataires.nom as destinataire_nom',
                'destinataires.prenom as destinataire_prenom',
                'destinataires.agence as destinataire_agence',
                'destinataires.tel as destinataire_tel'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->whereIn('etat', ['Fermé'])
            ->where('colis.agence', 'AFT Agence Louis Bleriot')
            ->whereNotNull('colis.reference_contenaire') 
            ->get();

        // 2. Extraire les IDs uniques des colis
        $colisIds = $colis->pluck('id')->unique()->toArray();

        // 3. Récupérer les paiements liés
        $paiements = Paiement::whereIn('colis_id', $colisIds)
                            ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye'))
                            ->groupBy('colis_id')
                            ->get()
                            ->keyBy('colis_id');

        // 4. Grouper les colis par référence
        $colisGrouped = $colis->groupBy('reference_colis');

        // 5. Construire la data finale
        $processedData = $colisGrouped->map(function ($group, $reference) use ($paiements) {
            $firstColis = $group->first();
            $quantiteTotale = $group->sum('quantite_colis');
            $prixTotalColis = $group->sum('prix_transit_colis');

            $montantTotalPaye = 0;
            foreach ($group->pluck('id') as $colisId) {
                if (isset($paiements[$colisId])) {
                    $montantTotalPaye += $paiements[$colisId]->total_paye;
                }
            }

            // Calcul du statut paiement
            $paymentStatus = 'impaye';
            $tolerance = 0.01;
            if ($montantTotalPaye > 0) {
                if (abs($prixTotalColis - $montantTotalPaye) < $tolerance) {
                    $paymentStatus = 'paye';
                } elseif ($montantTotalPaye < $prixTotalColis) {
                    $paymentStatus = 'partiel';
                }
            }

            return [
                'reference_colis' => $reference,
                'nombre_de_colis' => $quantiteTotale,
                'expediteur_nom' => $firstColis->expediteur_nom,
                'expediteur_prenom' => $firstColis->expediteur_prenom,
                'expediteur_tel' => $firstColis->expediteur_tel,
                'expediteur_agence' => $firstColis->expediteur_agence,
                'destinataire_nom' => $firstColis->destinataire_nom,
                'destinataire_prenom' => $firstColis->destinataire_prenom,
                'destinataire_tel' => $firstColis->destinataire_tel,
                'destinataire_agence' => $firstColis->destinataire_agence,
                'etat' => $firstColis->etat,
                'created_at' => $firstColis->created_at ? $firstColis->created_at->format('d/m/Y H:i') : 'N/A',
                'payment_status' => $paymentStatus,
                'prix_total' => $prixTotalColis,
                'montant_paye' => $montantTotalPaye,
                'colis_ids' => json_encode($group->pluck('id')->toArray()),
                'first_colis_id' => $firstColis->id
            ];
        })->values();

        // 6. Retour DataTables
        return DataTables::of($processedData)
            ->addColumn('statut_paiement', function ($row) {
                $status = $row['payment_status'];
                $montantPayeFormatted = number_format($row['montant_paye'], 2, ',', ' ');
                $prixTotalFormatted = number_format($row['prix_total'], 2, ',', ' ');

                switch ($status) {
                    case 'paye':
                        return '<span title="Payé (' . $montantPayeFormatted . ' / ' . $prixTotalFormatted . ')">
                                    <i class="fas fa-check-circle" style="color:green;font-size:1.3em;"></i>
                                </span>';
                    case 'partiel':
                        return '<span title="Paiement Partiel (' . $montantPayeFormatted . ' / ' . $prixTotalFormatted . ')">
                                    <i class="fas fa-exclamation-circle" style="color:orange;font-size:1.3em;"></i>
                                </span>';
                    default:
                        return '<span title="Impayé (0 / ' . $prixTotalFormatted . ')">
                                    <i class="fas fa-times-circle" style="color:red;font-size:1.3em;"></i>
                                </span>';
                }
            })
            ->addColumn('action', function ($row) {
                $reference = $row['reference_colis'];
                $invoiceUrl = route('aftlb_colis.valide.edit.invoice', ['id' => $row['first_colis_id']]);

                return '<div class="action-buttons-container">
                            <button type="button" class="btn btn-sm btn-success pay-btn"
                                    data-reference="' . e($reference) . '"
                                    data-total="' . $row['prix_total'] . '"
                                    data-paid="' . $row['montant_paye'] . '"
                                    data-colis-ids="' . e($row['colis_ids']) . '"
                                    title="Enregistrer un Paiement">
                                <i class="fas fa-dollar-sign"></i>
                            </button>
                            <a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Facture">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </div>';
            })
            ->rawColumns(['action', 'statut_paiement'])
            ->make(true);
    }
}

    
    public function update_bateaux(Request $request, $id)
    {
        $request->validate([
            'date_depart' => 'required|date',
            'date_arriver' => 'required|date|after:date_depart',
            'compagnie' => 'nullable|string|max:255',
            'numero_bateau' => 'nullable|string|max:255',
            'nom_bateau' => 'nullable|string|max:255',
            'numero_ballon' => 'nullable|string|max:255',
            'nom_ballon' => 'nullable|string|max:255',
        ]);
    
        // 2. RÉCUPÉRATION DU MODÈLE
        $bateau = Bateaux::findOrFail($id);

        $bateau->update([
            'created_at' => $request->date_depart,
            'date_arriver' => $request->date_arriver,
            'compagnie' => $request->compagnie,
            'numero_bateau' => $request->numero_bateau,
            'nom_bateau' => $request->nom_bateau,
            'numero_ballon' => $request->numero_ballon,
            'nom_ballon' => $request->nom_ballon,
        ]);
    
        // 4. REDIRECTION
        return redirect()->route('aftlb_colis.cargaison.ferme')->with('success', 'Véhicule de navigation modifié avec succès.');
    }
    

    public function historique_contenaire()
    {
        // Cette vue contient la table '#contenairesTable'
        return view('AFT_LOUIS_BLERIOT.cargaison.historique_contenaire');
    }

    /**
     * Fournit les données pour la DataTable de l'historique des conteneurs.
     */
    public function get_contenaires(Request $request)
    {
        $contenaires = Colis::query()
            ->select('reference_contenaire')
            ->whereNotNull('reference_contenaire')
            ->where('agence', 'AFT Agence Louis Bleriot') 
            ->distinct();

        return datatables()->of($contenaires)
            ->addColumn('action', function ($row) {
                $listColisUrl = route('aftlb_colis.liste.colis.par.contenaire', [
                    'reference_contenaire' => $row->reference_contenaire
                ]);
                return '
                    <div class="d-flex justify-content-center">
                        <a href="' . $listColisUrl . '" class="btn btn-sm btn-info">
                            <i class="fas fa-list"></i> &nbsp; Liste des colis
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    /**
     * Affiche la page de détails avec la liste des colis pour un conteneur donné.
     */
    public function liste_colis_par_contenaire($reference_contenaire)
    {
        return view('AFT_LOUIS_BLERIOT.cargaison.liste_colis_contenaire', compact('reference_contenaire'));
    }


    /**
     * Fournit les données JSON des colis pour un conteneur spécifique à DataTables.
     */
    public function get_colis_pour_contenaire(Request $request, $reference_contenaire)
    {
        if (!$request->ajax()) {
            return abort(404); // On ne traite que les requêtes AJAX
        }

        // ------------------------------
        // 1. Récupération des colis filtrés par conteneur
        // ------------------------------
        $colis = Colis::select(
                'colis.*',
                'expediteurs.nom as expediteur_nom',
                'expediteurs.prenom as expediteur_prenom',
                'expediteurs.tel as expediteur_tel',
                'expediteurs.agence as expediteur_agence',
                'destinataires.nom as destinataire_nom',
                'destinataires.prenom as destinataire_prenom',
                'destinataires.agence as destinataire_agence',
                'destinataires.tel as destinataire_tel'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.reference_contenaire', $reference_contenaire)
            ->where('colis.agence', 'AFT Agence Louis Bleriot')
            ->get();

        // ------------------------------
        // 2. Récupération des paiements
        // ------------------------------
        $colisIds = $colis->pluck('id')->unique()->toArray();

        $paiements = Paiement::whereIn('colis_id', $colisIds)
                    ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye'))
                    ->groupBy('colis_id')
                    ->get()
                    ->keyBy('colis_id');

        // ------------------------------
        // 3. Groupement par référence_colis
        // ------------------------------
        $colisGrouped = $colis->groupBy('reference_colis');

        // ------------------------------
        // 4. Préparation des données pour DataTables
        // ------------------------------
        $processedData = $colisGrouped->map(function ($group, $reference) use ($paiements) {

            $firstColis = $group->first();
            $quantiteTotale = $group->sum('quantite_colis');
            $prixTotalColis = $group->sum('prix_transit_colis');
            $colisIdsInGroup = $group->pluck('id')->toArray();

            // Calcul du montant total payé pour le groupe
            $montantTotalPaye = 0;
            foreach ($colisIdsInGroup as $colisId) {
                $montantTotalPaye += $paiements[$colisId]->total_paye ?? 0;
            }

            // Détermination du statut de paiement
            $paymentStatus = 'impaye';
            $tolerance = 0.01; // Tolérance pour comparaison flottante
            if ($montantTotalPaye > 0) {
                if (abs($prixTotalColis - $montantTotalPaye) < $tolerance) {
                    $paymentStatus = 'paye';
                } elseif ($montantTotalPaye < $prixTotalColis) {
                    $paymentStatus = 'partiel';
                }
            }

            return [
                'reference_colis'     => $reference,
                'nombre_de_colis'     => $quantiteTotale,
                'expediteur_nom'      => $firstColis->expediteur_nom,
                'expediteur_prenom'   => $firstColis->expediteur_prenom,
                'expediteur_tel'      => $firstColis->expediteur_tel,
                'expediteur_agence'   => $firstColis->expediteur_agence,
                'destinataire_nom'    => $firstColis->destinataire_nom,
                'destinataire_prenom' => $firstColis->destinataire_prenom,
                'destinataire_tel'    => $firstColis->destinataire_tel,
                'destinataire_agence' => $firstColis->destinataire_agence,
                'etat'                => $firstColis->etat,
                'created_at'          => $firstColis->created_at?->format('d/m/Y H:i') ?? 'N/A',
                'payment_status'      => $paymentStatus,
                'prix_total'          => $prixTotalColis,
                'montant_paye'        => $montantTotalPaye,
                'colis_ids'           => json_encode($colisIdsInGroup),
                'first_colis_id'      => $firstColis->id,
            ];
        })->values();

        // ------------------------------
        // 5. DataTables : ajout des colonnes calculées
        // ------------------------------
        return DataTables::of($processedData)
            ->addColumn('statut_paiement', function ($row) {
                $status = $row['payment_status'];
                $montantPayeFormatted = number_format($row['montant_paye'], 2, ',', ' ');
                $prixTotalFormatted = number_format($row['prix_total'], 2, ',', ' ');

                switch ($status) {
                    case 'paye':
                        $iconClass = 'fas fa-check-circle';
                        $iconColor = 'green';
                        $title = "Payé ($montantPayeFormatted / $prixTotalFormatted)";
                        break;
                    case 'partiel':
                        $iconClass = 'fas fa-exclamation-circle';
                        $iconColor = 'orange';
                        $title = "Paiement Partiel ($montantPayeFormatted / $prixTotalFormatted)";
                        break;
                    case 'impaye':
                    default:
                        $iconClass = 'fas fa-times-circle';
                        $iconColor = 'red';
                        $title = "Impayé (0 / $prixTotalFormatted)";
                        break;
                }

                return '<span title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">
                            <i class="' . $iconClass . '" style="color:' . $iconColor . '; font-size:1.3em;"></i>
                        </span>';
            })
            ->addColumn('action', function ($row) {
                $reference = $row['reference_colis'];
                $firstColisId = $row['first_colis_id'];

                // $invoiceUrl = route('aftlb_colis.valide.edit.invoice', ['id' => $firstColisId]);
                $deleteUrl  = route('aftlb_colis.destroy.colis.valide', ['reference' => $reference]);

                $payBtn = '<button type="button" class="btn btn-sm btn-success pay-btn"
                                data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                data-total="' . $row['prix_total'] . '"
                                data-paid="' . $row['montant_paye'] . '"
                                data-colis-ids="' . htmlspecialchars($row['colis_ids'], ENT_QUOTES, 'UTF-8') . '"
                                title="Enregistrer un Paiement pour la référence ' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '">
                            <i class="fas fa-dollar-sign"></i>
                        </button>';

               

                return '<div class="action-buttons-container">' . $payBtn . '</div>';
            })
            ->rawColumns(['action', 'statut_paiement'])
            ->make(true);
    }




    //Historique des vol

        public function historique_vol()
    {
        // Cette vue contient la table '#contenairesTable'
        return view('AFT_LOUIS_BLERIOT.cargaison.historique_vol');
    }

    /**
     * Fournit les données pour la DataTable de l'historique des conteneurs.
     */
    public function get_vol(Request $request)
    {
        $vol = Colis::query()
            ->select('reference_vol')
            ->whereNotNull('reference_vol')
            ->where('agence', 'AFT Agence Louis Bleriot')
            ->distinct();

        return datatables()->of($vol)
            ->addColumn('action', function ($row) {
                $listColisUrl = route('aftlb_colis.liste.colis.par.vol', [
                    'reference_vol' => $row->reference_vol
                ]);
                return '
                    <div class="d-flex justify-content-center">
                        <a href="' . $listColisUrl . '" class="btn btn-sm btn-info">
                            <i class="fas fa-list"></i> &nbsp; Liste des colis
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    /**
     * Affiche la page de détails avec la liste des colis pour un conteneur donné.
     */
    public function liste_colis_par_vol($reference_vol)
    {
        return view('AFT_LOUIS_BLERIOT.cargaison.liste_colis_vol', compact('reference_vol'));
    }


    /**
     * Fournit les données JSON des colis pour un conteneur spécifique à DataTables.
     */
    public function get_colis_pour_vol(Request $request, $reference_vol)
    {
        if (!$request->ajax()) {
            return abort(404); // On ne traite que les requêtes AJAX
        }

        // ------------------------------
        // 1. Récupération des colis filtrés par conteneur
        // ------------------------------
        $colis = Colis::select(
                'colis.*',
                'expediteurs.nom as expediteur_nom',
                'expediteurs.prenom as expediteur_prenom',
                'expediteurs.tel as expediteur_tel',
                'expediteurs.agence as expediteur_agence',
                'destinataires.nom as destinataire_nom',
                'destinataires.prenom as destinataire_prenom',
                'destinataires.agence as destinataire_agence',
                'destinataires.tel as destinataire_tel'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.reference_vol', $reference_vol)
            ->where('colis.agence', 'AFT Agence Louis Bleriot')
            ->get();

        // ------------------------------
        // 2. Récupération des paiements
        // ------------------------------
        $colisIds = $colis->pluck('id')->unique()->toArray();

        $paiements = Paiement::whereIn('colis_id', $colisIds)
                    ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye'))
                    ->groupBy('colis_id')
                    ->get()
                    ->keyBy('colis_id');

        // ------------------------------
        // 3. Groupement par référence_colis
        // ------------------------------
        $colisGrouped = $colis->groupBy('reference_colis');

        // ------------------------------
        // 4. Préparation des données pour DataTables
        // ------------------------------
        $processedData = $colisGrouped->map(function ($group, $reference) use ($paiements) {

            $firstColis = $group->first();
            $quantiteTotale = $group->sum('quantite_colis');
            $prixTotalColis = $group->sum('prix_transit_colis');
            $colisIdsInGroup = $group->pluck('id')->toArray();

            // Calcul du montant total payé pour le groupe
            $montantTotalPaye = 0;
            foreach ($colisIdsInGroup as $colisId) {
                $montantTotalPaye += $paiements[$colisId]->total_paye ?? 0;
            }

            // Détermination du statut de paiement
            $paymentStatus = 'impaye';
            $tolerance = 0.01; // Tolérance pour comparaison flottante
            if ($montantTotalPaye > 0) {
                if (abs($prixTotalColis - $montantTotalPaye) < $tolerance) {
                    $paymentStatus = 'paye';
                } elseif ($montantTotalPaye < $prixTotalColis) {
                    $paymentStatus = 'partiel';
                }
            }

            return [
                'reference_colis'     => $reference,
                'nombre_de_colis'     => $quantiteTotale,
                'expediteur_nom'      => $firstColis->expediteur_nom,
                'expediteur_prenom'   => $firstColis->expediteur_prenom,
                'expediteur_tel'      => $firstColis->expediteur_tel,
                'expediteur_agence'   => $firstColis->expediteur_agence,
                'destinataire_nom'    => $firstColis->destinataire_nom,
                'destinataire_prenom' => $firstColis->destinataire_prenom,
                'destinataire_tel'    => $firstColis->destinataire_tel,
                'destinataire_agence' => $firstColis->destinataire_agence,
                'etat'                => $firstColis->etat,
                'created_at'          => $firstColis->created_at?->format('d/m/Y H:i') ?? 'N/A',
                'payment_status'      => $paymentStatus,
                'prix_total'          => $prixTotalColis,
                'montant_paye'        => $montantTotalPaye,
                'colis_ids'           => json_encode($colisIdsInGroup),
                'first_colis_id'      => $firstColis->id,
            ];
        })->values();

        // ------------------------------
        // 5. DataTables : ajout des colonnes calculées
        // ------------------------------
        return DataTables::of($processedData)
            ->addColumn('statut_paiement', function ($row) {
                $status = $row['payment_status'];
                $montantPayeFormatted = number_format($row['montant_paye'], 2, ',', ' ');
                $prixTotalFormatted = number_format($row['prix_total'], 2, ',', ' ');

                switch ($status) {
                    case 'paye':
                        $iconClass = 'fas fa-check-circle';
                        $iconColor = 'green';
                        $title = "Payé ($montantPayeFormatted / $prixTotalFormatted)";
                        break;
                    case 'partiel':
                        $iconClass = 'fas fa-exclamation-circle';
                        $iconColor = 'orange';
                        $title = "Paiement Partiel ($montantPayeFormatted / $prixTotalFormatted)";
                        break;
                    case 'impaye':
                    default:
                        $iconClass = 'fas fa-times-circle';
                        $iconColor = 'red';
                        $title = "Impayé (0 / $prixTotalFormatted)";
                        break;
                }

                return '<span title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">
                            <i class="' . $iconClass . '" style="color:' . $iconColor . '; font-size:1.3em;"></i>
                        </span>';
            })
            ->addColumn('action', function ($row) {
                $reference = $row['reference_colis'];
                $firstColisId = $row['first_colis_id'];

                $invoiceUrl = route('aftlb_colis.valide.edit.invoice', ['id' => $firstColisId]);
                $deleteUrl  = route('aftlb_colis.destroy.colis.valide', ['reference' => $reference]);

                $payBtn = '<button type="button" class="btn btn-sm btn-success pay-btn"
                                data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                data-total="' . $row['prix_total'] . '"
                                data-paid="' . $row['montant_paye'] . '"
                                data-colis-ids="' . htmlspecialchars($row['colis_ids'], ENT_QUOTES, 'UTF-8') . '"
                                title="Enregistrer un Paiement pour la référence ' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '">
                            <i class="fas fa-dollar-sign"></i>
                        </button>';

                $invoiceBtn = '<a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Voir la Facture">
                                <i class="fas fa-file-invoice"></i>
                            </a>';

                return '<div class="action-buttons-container">' . $payBtn . $invoiceBtn . '</div>';
            })
            ->rawColumns(['action', 'statut_paiement'])
            ->make(true);
    }

    public function cargaison_ferme(Request $request)
    {
        // 1. Récupérer les agences de destination
        $agencesDestination = Agence::where('pays_agence', 'Côte d\'Ivoire')->get();

        // 2. Récupérer les références MARITIMES fermées pour l'agence spécifiée
        // On utilise pluck() pour obtenir directement une collection de chaînes de caractères
        $referenceMaritimesBrutes = Colis::join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->where('colis.etat', 'Fermé')
            ->where('colis.mode_transit', 'maritime') // Assurez-vous de bien filtrer par mode
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->pluck('colis.reference_contenaire')
            ->filter() // Retire les valeurs null ou vides
            ->unique(); // Garde uniquement les valeurs uniques

        // 3. Récupérer les références AÉRIENNES fermées
        $referenceAeriensBrutes = Colis::where('etat', 'Fermé')
            ->where('mode_transit', 'aérien')
            ->whereHas('expediteur', function ($query) {
                 $query->where('agence', 'AFT Agence Louis Bleriot');
            })
            ->pluck('reference_vol')
            ->filter()
            ->unique();

        // 4. Compter combien de fois chaque référence a déjà été utilisée dans la table Bateaux
        $allReferences = $referenceMaritimesBrutes->merge($referenceAeriensBrutes);
        $referencesUsageCounts = Bateaux::whereIn('reference_conteneur', $allReferences)
            ->selectRaw('reference_conteneur, COUNT(*) as total')
            ->groupBy('reference_conteneur')
            ->pluck('total', 'reference_conteneur');

        // 5. Filtrer les références pour ne garder que celles utilisées moins de 2 fois
        $referenceMaritimes = $referenceMaritimesBrutes->filter(function ($ref) use ($referencesUsageCounts) {
            return !isset($referencesUsageCounts[$ref]) || $referencesUsageCounts[$ref] < 2;
        })->values();

        $referenceAeriens = $referenceAeriensBrutes->filter(function ($ref) use ($referencesUsageCounts) {
            return !isset($referencesUsageCounts[$ref]) || $referencesUsageCounts[$ref] < 2;
        })->values();

        // 6. Mois et année pour la génération de la référence finale
        $mois = Carbon::now()->translatedFormat('m'); // Format 'm' pour le mois en chiffre
        $annee = Carbon::now()->year;

        // 7. Retourner la vue avec les données compactées
        return view('AFT_LOUIS_BLERIOT.cargaison.cargaison_ferme', compact(
            'agencesDestination',
            'referenceMaritimes', // Sera une collection de chaînes
            'referenceAeriens',   // Sera une collection de chaînes
            'mois',
            'annee'
        ));
    }


public function devis_hold(Request $request)
{
    return view('AFT_LOUIS_BLERIOT.devis.hold');
}

public function liste_contenaire(Request $request)
{
    $referenceContenaire = $request->input('reference_contenaire', $this->generateReferenceContenaire());

    $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();

    return view('AFT_LOUIS_BLERIOT.cargaison.liste_contenaire',compact('referenceContenaire','agencesDestination'));
}

public function liste_vol(Request $request)
{
    $referenceVol = $request->input('reference_vol', $this->generateReferenceVol());
    return view('AFT_LOUIS_BLERIOT.cargaison.liste_vol',compact('referenceVol'));
}

}