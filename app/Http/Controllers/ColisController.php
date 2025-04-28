<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Produit;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Les_colis;
use App\Models\Colis;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Paiement;
use App\Models\Article;
use App\Models\Bateaux;
use App\Models\Invoice;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
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


class ColisController extends Controller
{
   
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
        $produits = Produit::where('description', 'like', '%' . $query . '%')
                        ->limit(15)
                        ->get(['id', 'description', 'prix']); // Sélectionner les champs à renvoyer
        return response()->json($produits);
    }

    public function storeProduit(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'categorie' => 'required|string|max:100|in:Colis,Service,Remise',
            'prix' => 'required|numeric|min:0',
        ]);
    
        Produit::create([
            'description' => $request->description,
            'categorie' => $request->categorie,
            'prix' => $request->prix,
        ]);
    
        return response()->json(['message' => 'Produit ajouté avec succès !'], 201);
    }


    public function create(Request $request)
    {
        // Récupération des agences et des clients
        $agences = Agence::select('nom_agence', 'id')->get();
        // $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        // $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        // Redirection vers la vue
        return view('admin.colis.add', compact('agences'));
    }

    /**
     * Étape de paiement.
     */
    // public function payement()
    // {
    //     return view('admin.colis.add.payement');
    // }

    /**
     * Étape 1 : Formulaire initial.
     */
    public function createStep1()
    {
        // Chargement des données
        // $step = 1;
        $agences = Agence::select('nom_agence', 'id')->get();
        // $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        // $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        return view('admin.colis.add.step1', compact('agences'));
    }

    /**
     * Enregistre les données de l'étape 1.
     */
    public function storeStep1(Request $request)
    {
        $request->session()->put('step1', $request->all());
        // Validation des données
        $request->validate([
            // Ajouter les règles de validation si nécessaires
        ]);

        // Stockage des données en session
        session(['step1' => $request->only([
            'nom_expediteur',
            'prenom_expediteur', 
            'email_expediteur', 
            'tel_expediteur',
            'agence_expedition',
            'adresse_expediteur', 
            'nom_destinataire', 
            'prenom_destinataire',
            'email_destinataire', 
            'tel_destinataire',
            'agence_destination',
            'adresse_destinataire'
        ])]);

        return redirect()->route('colis.create.step2');
    }

    /**
     * Étape 2 : Détails du colis.
     */
    public function createStep2()
    {
        
        return view('admin.colis.add.step2',['stepProgress' => 40]);
    }

    public function storeStep2(Request $request)
    {
       
        $request->session()->put('step3', $request->all());
        $request->validate([
            // 'quantite' => 'required',
            // 'type_emballage' => 'required',
            // 'dimension' => 'required',
            // 'description_colis' => 'required',
            // 'poids_colis' => 'required',
            // 'valeur_colis' => 'required',
        ]);

        session(['step3' => $request->only([
            'quantite_colis',
            'type_embalage', 
            'dimension_colis', 
            'description_colis', 
            'poids_colis', 
            'valeur_colis',
        ])]);

        return redirect()->route('colis.create.step3');
    }

    public function createStep3()
    {
        // $step = 3;
        return view('admin.colis.add.step3');
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

        return $baseReference; // Retourne la référence unique pour ce colis
    }
   
    private function generateReferenceContenaire()
    {
        $alphabet = range('A', 'Z');
        $letterIndex = 0;
        $increment = 1;
    
        do {
            $currentLetter = $alphabet[$letterIndex];
            $baseReference = "{$currentLetter}{$increment}";
    
            $exists = DB::table('colis')
                        ->where('reference_contenaire', $baseReference)
                        ->exists();
    
            if ($exists) {
                $increment++;
                if ($increment > 5) {
                    $increment = 1;
                    $letterIndex++;
                }
            }
        } while ($exists && $letterIndex < count($alphabet));
    
        if ($letterIndex >= count($alphabet)) {
            throw new \Exception("Plus de références de conteneur disponibles.");
        }
    
        return $baseReference;
    }
    
    
    private function generateReferenceVol()
   
    {
        $user = Auth::user();

        if (!$user) {
            
            throw new \Exception("Utilisateur non connecté.");
        }

        $initiales = strtoupper(substr($user->last_name ?? 'X', 0, 1) . substr($user->first_name ?? 'X', 0, 1));
    
        $contenaireRef = DB::table('colis')
            ->where('etat', '!=', 'Fermé') // Consider using constants or an enum for 'etat'
            ->orderByDesc('id')
            ->value('reference_contenaire');

        if (!$contenaireRef) {
            $contenaireRef = $this->generateReferenceContenaire();
            if (!$contenaireRef) {
                throw new \Exception("Impossible de générer une référence de conteneur.");
            }
        }

        $lastId = DB::table('colis')->max('id');

        $nextId = ($lastId === null) ? 1 : $lastId + 1;

        $numero = str_pad($nextId, 3, '0', STR_PAD_LEFT);


        $reference = "{$initiales}-{$numero}-{$contenaireRef}";

        return [
            'reference_colis' => $reference,
            'reference_contenaire' => $contenaireRef
        ];
    }

    private function generateReferenceColisComplet()
    {
        $user = Auth::user();

        if (!$user) {
            
            throw new \Exception("Utilisateur non connecté.");
        }

        $initiales = strtoupper(substr($user->last_name ?? 'X', 0, 1) . substr($user->first_name ?? 'X', 0, 1));
    
        $contenaireRef = DB::table('colis')
            ->where('etat', '!=', 'Fermé') // Consider using constants or an enum for 'etat'
            ->orderByDesc('id')
            ->value('reference_contenaire');

        if (!$contenaireRef) {
            $contenaireRef = $this->generateReferenceContenaire();
            if (!$contenaireRef) {
                throw new \Exception("Impossible de générer une référence de conteneur.");
            }
        }

        $lastId = DB::table('colis')->max('id');

        $nextId = ($lastId === null) ? 1 : $lastId + 1;

        $numero = str_pad($nextId, 3, '0', STR_PAD_LEFT);


        $reference = "{$initiales}-{$numero}-{$contenaireRef}";

        return [
            'reference_colis' => $reference,
            'reference_contenaire' => $contenaireRef
        ];
    }

    public function add_colis(Request $request)
    {
        $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->distinct()->pluck('pays_agence');
        $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
        $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();
    
        // Génère juste les références, sans enregistrer encore dans la base
        $referenceColis = $this->generateReferenceColisComplet();
        // dd($referenceColis);
        return view('admin.colis.add_colis', compact(
            'agencesExpedition', 'agencesDestination', 'paysUniques', 'referenceColis'
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
    
            // Compter les enregistrements avant la mise à jour
            $count = Colis::where('etat', 'Chargé')
                ->where('mode_transit', 'maritime')
                ->count();
            // dd($count);
            if ($count === 0) {
                return redirect()->back()->with('warning', 'Aucun colis avec l’état Chargé et un mode de transit Maritime.');
            }
    
            // Générer une référence unique pour le conteneur
            $referenceContenaire = $this->generateReferenceContenaire();
    
            // Mise à jour des enregistrements
            $updatedCount = Colis::where('etat', 'Chargé')
                ->where('mode_transit', 'maritime')
                ->update(['etat' => 'Fermé', 'reference_contenaire' => $referenceContenaire]);
    
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
        // dd($request);
        try {
            // Démarrez une transaction de base de données pour garantir l'atomicité
            DB::beginTransaction();
    
            // Compter les enregistrements avant la mise à jour
            $count = Colis::where('etat', 'Chargé')
                            ->where('mode_transit', 'aerien')
                            ->count();
    
            if ($count === 0) {
                return redirect()->back()->with('warning', 'Aucun colis avec l’état Chargé.');
            }
    
            // Générer une référence unique pour le conteneur
            $referenceContenaire = $this->generateReferenceContenaire();
    
            // Mise à jour des enregistrements
            $updatedCount = Colis::where('etat', 'Chargé')
                                ->where('mode_transit', 'aerien')
                                ->update(['etat' => 'Fermé', 'reference_contenaire' => $referenceContenaire]);
    
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




    public function store_colis(Request $request)
    {
        try {
            // Valider les données reçues
            $validated = $request->all();
    
            // Sauvegarder les données de la session
            $request->session()->put('step1', $validated);
            // Passer à l'étape suivante de paiement
            return redirect()->route('colis.create.payement');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement du colis : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du colis. Veuillez réessayer.');
        }
    }
    


    public function stepPayement()
    {
       
        return view('admin.colis.add.payement');
    }

    public function storePayement(Request $request)
    {
        try {
            $prixColis = session('step1.prix.0', 0); // Mettre 0 par défaut si non trouvé

            $validatedData = $request->validate([
            //     'mode_payement' => 'required|in:bank,mobile_money,cheque,cash,delivery',
            //     'numero_compte' => 'required_if:mode_payement,bank|max:255',
            //     'nom_banque' => 'required_if:mode_payement,bank,cheque|max:255',
            //     'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
            //     'numero_tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
            //     'operateur_mobile' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
            //     'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
            //     'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:100',
            // ], [
            //     'required' => 'Le champ :attribute est obligatoire.',
            //     'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            //     'numeric' => 'Le champ :attribute doit être un nombre.',
            //     'min' => 'Le champ :attribute doit être au moins :min.',
    
            //     'mode_payement.required' => 'Veuillez sélectionner un mode de paiement.',
            //     'mode_payement.in' => 'Le mode de paiement sélectionné est invalide.',
    
            //     'numero_compte.required_if' => 'Le numéro de compte est requis pour les paiements bancaires.',
            //     'nom_banque.required_if' => 'Le nom de la banque est requis pour ce mode de paiement.',
            //     'transaction_id.required_if' => 'L\'identifiant de transaction est obligatoire pour ce mode de paiement.',
            //     'numero_tel.required_if' => 'Le numéro de téléphone est requis pour les paiements mobile.',
            //     'numero_tel.regex' => 'Le numéro de téléphone doit contenir entre 10 et 15 chiffres.',
            //     'operateur_mobile.required_if' => 'Veuillez sélectionner un opérateur mobile.',
            //     'operateur_mobile.in' => 'L\'opérateur mobile sélectionné est invalide.',
            //     'numero_cheque.required_if' => 'Le numéro de chèque est requis pour les paiements par chèque.',
            //     'montant_reçu.required_if' => 'Le montant reçu est obligatoire pour les paiements en espèces.',
            //     'montant_reçu.min' => 'Le montant reçu doit être supérieur à 0',
            ]);
    
            // Stocker les données en session
     // Validation supplémentaire pour le montant en espèces
        if ($request->mode_payement === 'cash') {
            $prixColis = session('step1.prix.0');
            if ($request->montant_reçu > $prixColis) {
                return response()->json([
                    'success' => false,
                    'errors' => ['montant_reçu' => ['Le montant reçu ne peut pas dépasser le prix du colis ('.$prixColis.')']]
                ], 422);
            }
        }
    
            session(['step2' => $request->only([
                'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
                'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu',
                'dimension_result'
                
            ])]);
            return response()->json([
                'success' => true,
                'redirect' => route('colis.generer.qrcode'),
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(), // Retourne les erreurs de validation sous forme de tableau associatif
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue. Veuillez réessayer plus tard.',
            ], 500);
        }
    }
    

    public function generer_qrcode(Request $request, InfobipService $infobipService)
    {
        // dd($request->all());
        $data = array_merge(
            session('step1', []),
            session('step2', [])
        );
        // dd($data);
        if (empty($data) || !isset($data['quantite_colis']) || !is_array($data['quantite_colis'])) {

            \Log::error('Données de session invalides ou manquantes pour generer_qrcode.', ['session_data' => $data]);
            return redirect()->back()->with('error', 'Les données de la session sont invalides ou incomplètes. Veuillez recommencer.');
        }

        $data['status'] = $data['mode_payement'] ?? 'non payé'; // Status paiement global
        $data['etat'] = $data['etat'] ?? 'Validé';

        $expediteurData = [
            'nom' => $data['nom_expediteur'] ?? null,
            'prenom' => $data['prenom_expediteur'] ?? null,
            'email' => $data['email_expediteur'] ?? null,
            'tel' => $data['tel_expediteur'] ?? null,
            'agence' => $data['agence_expedition'] ?? null,
            'adresse' => $data['adresse_expediteur'] ?? null,
        ];

        $destinataireData = [
            'nom' => $data['nom_destinataire'] ?? null,
            'prenom' => $data['prenom_destinataire'] ?? null,
            'email' => $data['email_destinataire'] ?? null,
            'tel' => $data['tel_destinataire'] ?? null,
            'agence' => $data['agence_destination'] ?? null,
            'adresse' => $data['adresse_destinataire'] ?? null,
        ];

        // --- Création Expediteur & Destinataire (une seule fois) ---
        try {
            $expediteur = Expediteur::create($expediteurData);
            $destinataire = Destinataire::create($destinataireData);
        } catch (\Exception $e) {
            \Log::error('Erreur création Expediteur/Destinataire: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde des informations expéditeur/destinataire.');
        }
        // --- Données de Paiement ---
        $payementDataSession = session('step2', []);
        $montantTotalEstime = collect($data['prix'] ?? [])->sum(); // Calculer le total attendu des prix
    
        // *** NOUVELLE LOGIQUE POUR MONTANT PAYÉ ***
        $modePaiement = $payementDataSession['mode_payement'] ?? null;
        $montantPaiement = 0; // Initialiser à 0
    
        if ($modePaiement === 'cash') {
            // Prendre le montant reçu pour le paiement en espèces
            $montantPaiement = $payementDataSession['montant_reçu'] ?? 0;
        } elseif ($modePaiement === 'delivery') {
            // Pour paiement à la livraison, le montant payé initialement est 0
            $montantPaiement = 0;
        } elseif ($modePaiement) {
            
            $montantPaiement = $montantTotalEstime;
        }

        // Prendre l'ID de transaction de CinetPay en priorité si présent
        $transactionId = $request->input('cinetpay_transaction_id') ?? $payementDataSession['transaction_id'] ?? ('MANUAL-' . uniqid());
        $statutPaiement = 'non payé'; // Statut par défaut

        if ($modePaiement === 'delivery') {
            $statutPaiement = 'non payé'; // Ou 'en attente de paiement livraison'
        } elseif ($modePaiement === 'cash') {
            // Utiliser $montantPaiement déjà calculé
            if ($montantPaiement <= 0) {
                $statutPaiement = 'non payé';
            } elseif ($montantPaiement < $montantTotalEstime) {
                $statutPaiement = 'partiellement payé';
            } else { // >= $montantTotalEstime
                $statutPaiement = 'payé';
            }
        } elseif ($modePaiement) { // Pour bank, mobile_money, cheque
         
             $statutPaiement = 'payé';
        }

        // Agent ID
        $agentId = Auth::check() ? Auth::user()->agent?->id : null;

        // Préparer les données de base pour le paiement (sera lié à chaque colis)
        $basePaiementData = [
            'methode_paiement' => $payementDataSession['mode_payement'] ?? null,
            'montant' => $montantPaiement, // Le montant total payé pour cette transaction
            'operateur' => $payementDataSession['operateur_mobile'] ?? null,
            'banque' => $payementDataSession['nom_banque'] ?? null,
            'NumeroPaiement' => $payementDataSession['numero_tel'] ?? $payementDataSession['numero_cheque'] ?? $payementDataSession['numero_compte'] ?? null,
            'id_transaction' => $transactionId,
            'statut_paiement' => $statutPaiement,
            'date_validation' => now(),
            'expediteur_id' => $expediteur->id,
            'agent_id' => $agentId,
            // 'colis_id' sera ajouté dans la boucle
        ];
        // dd($basePaiementData);

        $colisEnregistres = [];
        $erreursCreation = [];

        foreach ($data['quantite_colis'] as $index => $quantite) {
            if ($quantite <= 0) continue; // Ignorer si quantité invalide

            $hauteur = $data['hauteur'][$index] ?? null;
            $largeur = $data['largeur'][$index] ?? null;
            $longueur = $data['longueur'][$index] ?? null;
            $dimension_result = (isset($hauteur, $largeur, $longueur)) ? "{$hauteur}x{$largeur}x{$longueur}" : null;

        
             $referenceColis = $data['reference_colis'] ?? ('REF-' . uniqid());
            // dd($referenceColis);
            $colisItemData = [
                'reference_colis' => $referenceColis,
                'reference_contenaire' => $data['reference_contenaire'] ?? null,
                'quantite_colis' => $quantite,
                'service' => $data['service'][$index] ?? null,
                'prix_transit_colis' => $data['prix'][$index] ?? 0, // Mettre 0 par défaut
                'poids_colis' => $data['poids_colis'][$index] ?? null,
                'mode_transit' => $data['mode_transit'] ?? null,
                'status' => $data['status'], // Statut paiement global (sera mis à jour par paiement?)
                'etat' => $data['etat'], // Etat colis global
                'type_colis' => $data['type_colis'][$index] ?? null,
                'dimension_result' => $dimension_result, // Calculé ci-dessus
                'description_colis' => $data['description_colis'][$index] ?? null,
                'expediteur_id' => $expediteur->id,
                'destinataire_id' => $destinataire->id,
                'agent_id' => $agentId,
                'qr_code_path' => null, // Initialisé à null
                // 'montant_payé' => $data['etat'], // Initialisé à null
            ];
            // dd($colisItemData);
            try {
                // Création du colis unique en BDD
                // dd($colisItemData);
                $colisModel = Colis::create($colisItemData);
                // dd($colisModel);
                $paiementDataPourCeColis = array_merge($basePaiementData, ['montant_paye' => $basePaiementData['montant'],'colis_id' => $colisModel->id, 'montant' => $colisItemData['prix_transit_colis']]); // Montant spécifique?
                // dd($paiementDataPourCeColis);
                $paiement = Paiement::create($paiementDataPourCeColis);
                $qrData = [
                    'ID' => $colisModel->id, // Utiliser l'ID réel
                    'Ref' => $colisModel->reference_colis,
                    'Etat' => $colisModel->etat,
                    'Exp' => optional($expediteur)->nom,
                    'Dest' => optional($destinataire)->nom . '/' . optional($destinataire)->tel,
                    'Agence' => optional($destinataire)->agence,
                ];
                // dd($qrData);
                $qrCodeContent = implode("\n", array_map(
                    function ($k, $v) { return "$k: $v"; },
                    array_keys($qrData),
                    array_values($qrData)
                ));

                $qrCode = new QrCode($qrCodeContent);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $pngData = $result->getString();

                // Chemin fichier (utiliser ID et référence pour unicité)
                $safeRef = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $colisModel->reference_colis);
                $filePath = 'qrcodes/colis_' . $safeRef . '_' . $colisModel->id . '.png';
                $fullPath = public_path($filePath);
                $directory = dirname($fullPath);

                // dd($colisModel);

                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true, true); // Ajout du dernier true
                }
                file_put_contents($fullPath, $pngData);

                // Mettre à jour le chemin dans la BDD pour ce colis
                $colisModel->update(['qr_code_path' => $filePath]);

                $colisEnregistres[] = $colisModel->fresh(); // Recharger le modèle avec le qr_path
            } catch (\Exception $e) {
                \Log::error("Erreur création colis/paiement/QR pour index {$index}: " . $e->getMessage(), ['data' => $colisItemData]);
                dd($e->getMessage());
                $erreursCreation[] = "Erreur lors de la création du colis avec référence {$referenceColis}.";
            }
        }

        if (!empty($erreursCreation)) {
            return redirect()->back()->with('error', implode('<br>', $erreursCreation));
        }

        if (empty($colisEnregistres)) {
             return redirect()->back()->with('error', 'Aucun colis n\'a été créé. Vérifiez les quantités.');
        }
        // dd($colisEnregistres);
        // Si $colisEnregistres est un array, le convertir en collection pour utiliser les méthodes
        $colisEnregistres = collect($colisEnregistres);
        dd($colisEnregistres);
        // Récupérer le premier colis
        $firstColis = $colisEnregistres->first();

        // Vérifier si $firstColis existe avant d'accéder à ses propriétés
        $firstInfo = [
            'id' => $firstColis?->id,
            'reference_colis' => $firstColis?->reference_colis,
            'nom_destinataire' => optional($firstColis?->destinataire)->nom,
            'prenom_destinataire' => optional($firstColis?->destinataire)->prenom,
            'tel_destinataire' => optional($firstColis?->destinataire)->tel,
            'nom_expediteur' => optional($firstColis?->expediteur)->nom,
            'prenom_expediteur' => optional($firstColis?->expediteur)->prenom,
            'tel_expediteur' => optional($firstColis?->expediteur)->tel,
        ];

        $totalQuantite = $colisEnregistres->sum('quantite_colis');
        $totalPrixTransit = $colisEnregistres->sum('prix_transit_colis');

        $ids_colis = $colisEnregistres->pluck('id')->toArray();
        $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();

        $mode_payement = $paiements->pluck('methode_paiement')->unique()->first();

        $totalMontant = $paiements->sum('montant');
        $totalMontantPaye = $paiements->first()?->montant_paye ?? 0;

        $restePaye = $totalMontant - $totalMontantPaye;

        session()->forget(['step1', 'step2']);

        return view('admin.colis.add.complete',[
            'colis' => $colisEnregistres,
            'first' => $firstInfo,
            'totalQuantite' => $totalQuantite,
            'totalPrixTransit' => $totalPrixTransit,
            'restePaye' => $restePaye,
            'mode_payement' => $mode_payement,
            'totalMontantPaye' => $totalMontantPaye,
        ]);

    }


    public function editFacture($id)
    {
        // dd($id);
        
        $colis_principal = Colis::find($id);

            if (!$colis_principal) {
                return redirect()->route('colis.hold')->with('error', 'Colis non trouvé.');
            }

            $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

            if ($colisCollection->isEmpty()) {
                return redirect()->route('colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
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
        // $totalMontantPaye = $paiements->sum('montant_paye');
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
        // $restePaye = $prix_total - $montant_paye;   

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
        return view('admin.colis.add.edit_invoice', compact(
            'date_facture', 
            'reference_colis', 
            'expediteur', 
            'tel_expediteur', 
            'destinataire', 
            'prix_total', 
            'montant_paye', 
            'mode_payement', 
            'colisData',
            'numero_facture',
            'tel_destinataire',
            'totalMontant',
            'totalMontantPaye',
            'restePaye'

        ));
    }

    public function editEtiquette($id)
    {
        // 1. Récupérer le colis spécifique par ID avec ses relations
        try {
            $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Gérer le cas où l'ID n'existe pas
            abort(404, 'Colis non trouvé.');
        }
        $reference_colis = $colis->reference_colis;

        $colisEnregistres = Colis::where('reference_colis', $reference_colis)->get();
        dd($colisEnregistres);
        $totalQuantite = $colisEnregistres->sum('quantite_colis');

        // dd($totalQuantite); // Affichage de la quantité totale des colis avec la même référence

        $quantite = max(1, (int)$totalQuantite);

        // 2. Préparer la collection d'étiquettes (clones)
        $etiquettes = new Collection(); // Utiliser new Collection()
        $quantite = $colis->quantite_colis ?? 1; // Utiliser 1 si null ou 0
        $quantite = max(1, (int)$quantite); // S'assurer que c'est au moins 1

        for ($i = 0; $i < $quantite; $i++) {
            $etiquettes->push(clone $colis); // Cloner pour chaque étiquette
        }

        $pdf = PDF::loadView('admin.colis.add.edit_etiquette', [
                'colis' => $etiquettes, 
                'colisType' => $colisEnregistres, 
                'totalEtiquettes' => $quantite // Le nombre total d'étiquettes à générer
            ])
            ->setPaper('a6', 'landscape')
            ->setOption('isRemoteEnabled', true); // Important pour les images externes/locales via public_path

        // 5. Retourner le PDF pour téléchargement
        $safeRef = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $colis->reference_colis ?? $colis->id);
        $fileName = 'etiquettes_' . $safeRef . '.pdf';

        return $pdf->download($fileName);
    }

    public function inprimerEtiquette($id) 
    {
        try {
            $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Gérer le cas où l'ID n'existe pas
            abort(404, 'Colis non trouvé.');
        }
        $reference_colis = $colis->reference_colis;

        $colisEnregistres = Colis::where('reference_colis', $reference_colis)->get();

        // Récupérer tous les type_colis
        $typeColisList = $colisEnregistres->pluck('type_colis');
        
        $totalQuantite = $colisEnregistres->sum('quantite_colis');


        $quantite = max(1, (int)$totalQuantite);


        $etiquettes = new \Illuminate\Support\Collection();

        // Construire une collection avec pour chaque étiquette un type_colis spécifique
        foreach ($typeColisList as $typeColis) {
            $cloneColis = clone $colis;
            $cloneColis->type_colis = $typeColis; // On assigne le bon type_colis
            $etiquettes->push($cloneColis);
        }

        while ($etiquettes->count() < $quantite) {
            $cloneColis = clone $colis;
            $cloneColis->type_colis = $typeColisList->last(); // répéter le dernier type_colis
            $etiquettes->push($cloneColis);
        }
    
        $pdf = PDF::loadView('admin.invoice.edit_etiquette', [
                'colis' => $etiquettes,
                'totalEtiquettes' => $quantite,
            ])
            ->setPaper('a6', 'landscape')
            ->setOption('isRemoteEnabled', true);
    
        $safeRef = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $colis->reference_colis ?? $colis->id);
        $fileName = 'etiquettes_' . $safeRef . '.pdf';
    
        return $pdf->download($fileName);
    }
    

    public function imprimerFacture($id)
    {
        // dd($id);
        
        $colis_principal = Colis::find($id);

            if (!$colis_principal) {
                return redirect()->route('colis.hold')->with('error', 'Colis non trouvé.');
            }

            $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

            if ($colisCollection->isEmpty()) {
                return redirect()->route('colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
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
        $totalMontantPaye = $paiements->first()->montant_paye ?? 0;
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
        // $reste = $prix_total - $montant_paye;   

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
        return view('admin.invoice.edit_invoice', compact(
            'date_facture', 
            'reference_colis', 
            'expediteur', 
            'tel_expediteur', 
            'destinataire', 
            'prix_total', 
            'montant_paye', 
            'mode_payement', 
            'colisData',
            'numero_facture',
            'tel_destinataire',
            'totalMontant',
            'totalMontantPaye',
            'restePaye'

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
    
        return view('admin.invoice.edit', [
            'colis' => $colisEnregistres,
            'first' => $firstInfo,
            'totalQuantite' => $totalQuantite,
            'totalPrixTransit' => $totalPrixTransit,
            'restePaye' => $restePaye,
            'mode_payement' => $mode_payement,
            'totalMontantPaye' => $totalMontantPaye,
        ]);
    }
    
    

    
    /**
     * Étape finale : Confirmation.
     */
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
    public function show($id)
    {
        //
    }

    public function hold()
    {
        $colis = Colis::select(
            'colis.*',  // Sélectionne toutes les colonnes de colis
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
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
        ->where('etat', 'En attente')  // Filtre l'état des colis
        ->get(); 
        // dd($colis->);
        return view('admin.colis.hold');
    }
//    public function colis arrivés()
    public function dump()
    {
        return view('admin.colis.dump');
    }

    public function history()
    {
        return view('admin.colis.history');
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
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->where('etat', 'En attente')  // Filtre l'état des colis
            ->get(); // Exécute la requête une seule fois

            return DataTables::of($colis)
                ->addColumn('action', function ($row) {
                    $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis

                    return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fas fa-credit-card"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
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

                        $editUrl = route('colis.valide.edit', ['id' => $firstColisId]); // Route pour modifier (utilise l'ID)
                        $invoiceUrl = route('colis.valide.edit.invoice', ['id' => $firstColisId]); // Route pour la facture (utilise l'ID)
                        $deleteUrl = route('colis.destroy.colis.valide', ['reference' => $reference]); // Route pour archiver (utilise la référence)

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

    public function enregistrerPaiement(Request $request)
    {
        $validated = $request->validate([
            'reference_colis' => 'required|string|exists:colis,reference_colis',
            'montant_a_payer' => 'required|numeric|min:0.01',
            'colis_ids'       => 'required|json', 
        ]);
    
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
    



 public function destroyColisValid(Request $request, $reference)
 {
     // Find all colis with the given reference that are not archived
     $colisToArchive = Colis::where('reference_colis', $reference)
                            ->whereNull('archived_at')
                            ->get();

     if ($colisToArchive->isEmpty()) {
         return response()->json(['error' => 'Aucun colis valide trouvé pour cette référence.'], 404);
     }

     try {
         // Archive each colis found
         foreach ($colisToArchive as $colis) {
             $colis->archived_at = now();
             $colis->save();
         }

         return response()->json(['success' => 'Colis avec la référence "' . $reference . '" archivés avec succès.']);

     } catch (\Exception $e) {
         // Log the error for debugging
         \Log::error("Error archiving colis reference {$reference}: " . $e->getMessage());
         return response()->json(['error' => 'Une erreur est survenue lors de l\'archivage.'], 500);
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
            $colis->archived_at = now();
            $colis->save();
        }

        return response()->json(['success' => 'Colis archivés avec succès !']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur lors de l\'archivage : ' . $e->getMessage()], 500);
    }
}




    public function print_facture($id)
    {
        $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        
        // Retournez une vue pour l'impression
        return view('admin.colis.colis_facture', compact('colis'));
    }
    

    // AJAX pour les colis arrivés
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

    // AJAX pour les devis colis
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
                    $printUrl = route('colis.qrcode.edit', ['id' => $row['colis']->first()->id]);
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
    
    
    

    public function edit_qrcode($id)
    {
        // Récupérer tous les colis qui appartiennent au lot (par exemple, colonne "hold_id")
        $colis = Colis::where('id', $id)->get();

        if ($colis->isEmpty()) {
            abort(404, "Aucun colis trouvé pour cet identifiant.");
        }

        // Pour chaque colis, générer le QR code
        foreach ($colis as $colisItem) {
            $qrData = [
                'Identifiant' => $colisItem->id,
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

        return view('admin.devis.edit_qrcode', compact('colis'));
    }


public function get_colis_hold(Request $request)
{
    if ($request->ajax()) {
        $colis = Colis::select(
            'colis.*',  // Sélectionne toutes les colonnes de colis
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
        ->where('etat', 'En attente','Validé')
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
            'created_at' => $group->first()->created_at ? $group->first()->created_at->format('d/m/Y') : null,
            'colis' => $group
        ];
    })->values();
    return DataTables::of($colisWithCount)
    ->addColumn('action', function ($row) {
        // $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis
        $editUrl = route('colis.hold.edit', ['id' => $row['colis']->first()->id]);
    // dd($editUrl);
        return '
                <div class="btn-group">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modify" data-bs-target="#modifModal">
                        <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                    </a>
                </div>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
}

    // Fonction edit pour les colis en attente
    public function edit_hold($id)
    {
        // Récupérer le colis principal avec l'ID donné
        $colis_principal = Colis::find($id);

        // Vérifier si le colis existe
        if (!$colis_principal) {
            return redirect()->route('colis.hold')->with('error', 'Colis non trouvé.');
        }

        // Récupérer tous les colis qui ont la même référence que le colis principal
        $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

        // Vérifier si des colis correspondants ont été trouvés
        if ($colis->isEmpty()) {
            return redirect()->route('colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
        }

        // Retourner la vue d'édition avec la collection de colis
        return view('admin.colis.edit_hold', compact('colis'));
    }
    

    



    public function edit_colis_valide($id)
    {
       // Vérifier si le colis existe
        $colis_principal = Colis::find($id);
        if (!$colis_principal) {
            return redirect()->route('colis.hold')->with('error', 'Colis non trouvé.');
        }

        // Récupérer tous les colis qui ont la même référence que le colis principal
        $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

        // Vérifier si des colis correspondants ont été trouvés
        if ($colis->isEmpty()) {
            return redirect()->route('colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
        }
        // dd($colis);
        return view('admin.colis.edit_colis_valide', compact('colis'));
    }

    // public function update_hold(Request $request) // Suppression de $id ici
    // {
    //     // Validation des données (important pour la sécurité)
    //     $validatedData = $request->validate([
    //         'colis.*.prix_transit_colis' => 'required|numeric|min:0',
    //         // Ajoutez d'autres règles de validation pour chaque champ modifiable.
    //     ]);

    //     $colisData = $request->input('colis');

    //     foreach ($colisData as $colisId => $data) {
    //         try {
    //             $colis = Colis::findOrFail($colisId);
    //             $numero_expediteur = $colis->expediteur->tel;
    //             dd($numero_expediteur);
    //             // Mise à jour des champs autorisés (sécurité !)
    //             $colis->prix_transit_colis = $data['prix_transit_colis'];
    //             $colis->status = 'payé';
    //             $colis->etat = 'Devis';
    //             $colis->save();

    //             // Reconstitution des données du QR Code (Déplacer hors de la boucle si les données ne changent pas)
    //             $qrData = [
    //                 'Référence colis' => $colis->reference_colis,
    //                 'Statut' => $colis->status,
    //                 'Nom Expéditeur' => $colis->expediteur->nom . ' ' . $colis->expediteur->prenom,
    //                 'Nom Destinataire' => $colis->destinataire->nom . ' ' . $colis->destinataire->prenom,
    //                 'Téléphone Destinataire' => $colis->destinataire->tel,
    //                 'Agence Destination' => $colis->destinataire->agence ?? '',
    //                 'Lieu de Destination' => $colis->destinataire->lieu_destination ?? '',
    //             ];
    //             // Logique du QR code ici si nécessaire (vous pouvez logguer, enregistrer, etc.)
    //             Log::info('QR Code Data pour le colis ' . $colisId . ': ' . json_encode($qrData));

    //         } catch (\Exception $e) {
    //             Log::error('Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
    //             return back()->with('error', 'Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
    //         }
    //     }

    //     // Redirection avec un message de succès
    //     return redirect()->route('colis.hold')->with('success', 'Devis faits avec succès !');
    // }
    
    public function update_hold(Request $request, InfobipService $infobipService)
    {
        $validatedData = $request->validate([
            'colis.*.prix_transit_colis' => 'required|numeric|min:0',
        ]);
    
        $colisData = $request->input('colis');
    
        foreach ($colisData as $colisId => $data) {
            try {
                $colis = Colis::findOrFail($colisId);
                $numero_expediteur = +2250546158376;
                // dd($numero_expediteur);
                // dd( $colis);
                // Mise à jour du colis
                $colis->prix_transit_colis = $data['prix_transit_colis'];
                $colis->status = 'payé';
                $colis->etat = 'Devis';
                $colis->save();
    
                // Message SMS
                $message = "Bonjour " . $colis->expediteur->nom . ", 
                            le devis de votre colis (Réf: " . $colis->reference_colis . ") a été établi avec succès. 
                            Le prix est de " . number_format($colis->prix_transit_colis, 2, ',', ' ') . " CFA. 
                            Connectez-vous pour effectuer votre paiement.";

                // Envoi du SMS
                $response = $infobipService->sendSms($numero_expediteur, $message);
                Log::info('SMS envoyé à ' . $numero_expediteur . ': ' . json_encode($response));
    
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
                return back()->with('error', 'Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
            }
        }
    
        return redirect()->route('colis.hold')->with('success', 'Devis faits avec succès !');
    }
    

    public function updateMultipleColis(Request $request)
    {
        // Validation globale (optionnelle, mais recommandée)
        $request->validate([
           
        ]);

        $colisData = $request->input('colis'); // Récupère toutes les données des colis

        foreach ($colisData as $colisId => $data) {
            // Récupérer le colis correspondant
            $colis = Colis::findOrFail($colisId);

            // Mise à jour des données de l'expéditeur
            $colis->expediteur->nom = $data['nom_expediteur'];
            $colis->expediteur->prenom = $data['prenom_expediteur'];
            $colis->expediteur->tel = $data['tel_expediteur'];
            $colis->expediteur->agence = $data['agence_expediteur'];
            $colis->expediteur->save();

            // Mise à jour des données du destinataire
            $colis->destinataire->nom = $data['nom_destinataire'];
            $colis->destinataire->prenom = $data['prenom_destinataire'];
            $colis->destinataire->tel = $data['tel_destinataire'];
            $colis->destinataire->agence = $data['agence_destinataire'];
            $colis->destinataire->save();

            // Mise à jour des données du colis
            $colis->quantite_colis = $data['quantite_colis'];
            $colis->valeur_colis = $data['valeur_colis'];
            $colis->mode_transit = $data['mode_transit'];
            $colis->poids_colis = $data['poids_colis'];
            $colis->prix_transit_colis = $data['prix_transit_colis'];
            $colis->save();

            // Reconstitution des données du QR Code (comme avant)
            $qrData = [
                'Identifiant' => $colisItem->id,
                'Référence colis' => $colis->reference_colis,
                'Statut' => $colis->status,
                'Nom Expéditeur' => $colis->expediteur->nom . ' ' . $colis->expediteur->prenom,
                'Nom Destinataire' => $colis->destinataire->nom . ' ' . $colis->destinataire->prenom,
                'Téléphone Destinataire' => $colis->destinataire->tel,
                'Agence Destination' => $colis->destinataire->agence ?? '',
                'Lieu de Destination' => $colis->destinataire->lieu_destination ?? '',
            ];

            Log::info('QR Code Data pour le colis ' . $colisId . ': ' . json_encode($qrData));

        }


        return redirect()->route('colis.colis.valide')->with('success', 'Colis mis à jour avec succès !');
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
                'created_at' => $group->first()->created_at ? $group->first()->created_at->format('d/m/Y H:i'): null,
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
                'created_at' => $group->first()->created_at ? $group->first()->created_at->format('d/m/Y H:i'): null,
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



public function devis_hold(Request $request)
{
    return view('admin.devis.hold');
}
public function colis_valide(Request $request)
{
    return view('admin.colis.valide');
}
public function getColisInfo($reference)
{
    $colis = Colis::with('expediteur', 'destinataire')
        ->where('reference_colis', $reference)
        ->first();

    if ($colis) {
        return response()->json($colis);
    } else {
        return response()->json(null); // Or return an appropriate error code
    }
}
public function cargaison_ferme(Request $request)
{
    // Récupérer les agences de destination
    $agencesDestination = Agence::where('pays_agence', 'Côte d\'Ivoire')->get();

    // Récupérer les références de conteneurs fermés, sans doublons
    $referenceFermes = Colis::where('etat', 'Fermé')->pluck('reference_contenaire')->unique()->toArray();

    // Obtenir le mois et l'année actuels
    $mois = Carbon::now()->translatedFormat('F'); // Ex: Janvier, Février...
    $annee = Carbon::now()->year;

    return view('admin.cargaison.cargaison_ferme', compact('agencesDestination', 'referenceFermes', 'mois', 'annee'));
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
        )->get();
            // dd($bateaux);
        return DataTables::of($bateaux)
            ->editColumn('date_depart', function ($row) {
                return $row->date_depart ? \Carbon\Carbon::parse($row->date_depart)->format('d/m/Y H:i') : 'N/A';
            })
            ->editColumn('date_arriver', function ($row) {
                return $row->date_arriver ? \Carbon\Carbon::parse($row->date_arriver)->format('d/m/Y H:i') : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('colis.bateaux.edit', $row->id);
                $deleteUrl = route('colis.bateaux.destroy', $row->id);
                $listColisUrl = route('colis.liste.bateau', $row->reference_conteneur);
            
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
    return view('admin.cargaison.edit_bateau', compact('bateau'));
}

public function destroy_bateaux($id)
{
    $bateau = Bateaux::findOrFail($id);
    $bateau->delete();

    return redirect()->back()->with('success', 'Bateau supprimé.');
}

public function liste_colis_par_bateau($reference_conteneur)
{
    $colis = Colis::where('reference_contenaire', $reference_conteneur)->get();
    // dd($colis);

    return view('admin.cargaison.liste_bateau', compact('colis'));
}

public function update_bateaux(Request $request, $id)
{
    // dd($request->all());
    $request->validate([
        'reference_bateau' => 'required|string|max:255',
        'reference_contenaire' => 'required|string|max:255',
        'date_depart' => 'required|date',
        'date_arriver' => 'required|date|after_or_equal:date_depart',
    ]);

    $bateau = Bateaux::findOrFail($id);
    $bateau->reference_bateau = $request->reference_bateau;
    $bateau->reference_conteneur = $request->reference_contenaire;
    $bateau->created_at = $request->date_depart;
    $bateau->date_arriver = $request->date_arriver;
    // dd($bateau);
    $bateau->save();

    return redirect()->route('colis.cargaison.ferme')->with('success', 'Bateau modifié avec succès.');
}




public function liste_contenaire(Request $request)
{

    $referenceContenaire = $request->input('reference_contenaire', $this->generateReferenceContenaire());
    $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();
    
    return view('admin.cargaison.liste_contenaire',compact('referenceContenaire','agencesDestination'));
}

public function liste_vol(Request $request)
{
    $referenceVol = $request->input('reference_vol', $this->generateReferenceVol());
    
    return view('admin.cargaison.liste_vol',compact('referenceVol'));
}



}
