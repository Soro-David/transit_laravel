<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
// use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\User;
use App\Models\Versement;
use App\Models\Produit;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Les_colis;
use App\Models\Colis;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Paiement;
use App\Models\Article;
use App\Models\Invoice;
use App\Models\Bateaux;
// use App\Models\Bateaux;
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
use Exception;
use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade;
use App\Services\CurrencyConverterService;
use PDF;
use Illuminate\Support\Carbon;


class ApmsAngreColisController extends Controller
{
    /**
     * Affiche la liste des ressources.
     */
    public function index()
    {
        // À implémenter si nécessaire
    }

    /**
     * Affiche le formulaire de création d'un colis.
     */
    public function create(Request $request)
    {
        // Récupération des agences et des clients
        $agences = Agence::select('nom_agence', 'id')->get();
        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        // Redirection vers la vue
        return view('IPMS_SIMEXCI_ANGRE.colis.add', compact('agences', 'client_expediteurs', 'client_destinataires'));
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
        // Récupérer la dernière référence enregistrée
        $lastReference = DB::table('colis')
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
        $lastReference = DB::table('colis')
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

    // Initiales de l'utilisateur (ex: SE)
    $initiales = strtoupper(
        substr($user->last_name ?? 'X', 0, 1) .
        substr($user->first_name ?? 'X', 0, 1)
    );

    // Vérifier si le dernier colis de ce mode est "Fermé"
    $dernierColis = DB::table('colis')
        ->where('mode_transit', $mode_transit)
        ->orderByDesc('id')
        ->first();

    if ($dernierColis && $dernierColis->etat === 'Fermé') {
        // Si fermé => reset à 1
        $nextIdRef = 1;
    } else {
        // Sinon on continue l'incrémentation
        $lastIdRef = DB::table('colis')
            ->where('mode_transit', $mode_transit)
            ->max('id_reference');
        $nextIdRef = ($lastIdRef ?? 0) + 1;
    }

    // Déterminer la bonne référence de conteneur ou vol selon le mode
    if ($mode_transit === 'maritime') {
        $contenaireRef = DB::table('colis')
            ->where('mode_transit', $mode_transit)
            ->where('etat', '!=', 'Fermé')
            ->orderByDesc('id')
            ->value('reference_contenaire') ?? $this->generateReferenceContenaire();
    } elseif ($mode_transit === 'aerien') {
        $contenaireRef = DB::table('colis')
            ->where('mode_transit', $mode_transit)
            ->where('etat', '!=', 'Fermé')
            ->orderByDesc('id')
            ->value('reference_vol') ?? $this->generateReferenceVol();
    } else {
        throw new \Exception("Mode de transit invalide : $mode_transit");
    }

    // Format final de la référence du colis
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
        $agencesExpedition = Agence::where('nom_agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();
        // dd($agences);
        // Génère juste les références, sans enregistrer encore dans la base
        // $referenceColis = $this->generateReferenceParMode();
        $referenceColis_maritime = $this->generateReferenceParMode('maritime');
        $referenceColis_aerien = $this->generateReferenceParMode('aerien');
        // dd($referenceColis);
       return view('IPMS_SIMEXCI_ANGRE.colis.add_colis', compact(
            'agencesExpedition', 'agencesDestination', 'paysUniques', 'referenceColis_maritime','referenceColis_aerien'
        ));
    }


    
 
    public function autocompleteProduit(Request $request)
    {
        $query = $request->get('query');
        $produits = Produit::where('description', 'like', '%' . $query . '%')
                            ->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                            ->limit(15)
                            ->get(['id', 'description', 'prix']);
                            
        return response()->json($produits);
    }

    public function store_colis(Request $request)
    {
        try {
            // Valider les données reçues
            $validated = $request->all();
    
            // Sauvegarder les données de la session
            $request->session()->put('step1', $validated);
            // Passer à l'étape suivante de paiement
            return redirect()->route('ipms_angre_colis.create.payement');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement du colis : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du colis. Veuillez réessayer.');
        }
    }


    public function stepPayment()
    {
       
       // Récupérer les données de l'étape 1 depuis la session
        $step1Data = session('step1');

        // Vérifier si les données existent et contiennent les prix
        if (!$step1Data || !isset($step1Data['prix']) || !is_array($step1Data['prix'])) {
            // Rediriger vers la première étape avec une erreur si les données sont manquantes
            // Remplacez 'route.vers.etape1' par le nom réel de votre route pour l'étape 1
            return redirect()->route('ipms_angre_colis.create.colis')->with('error', 'Données de colis manquantes ou invalides. Veuillez recommencer.');
        }

        // Calculer le montant total en additionnant tous les prix du tableau 'prix'
        $totalPrice = collect($step1Data['prix'])->sum();

        // Optionnel mais recommandé : stocker aussi le total en session pour usage ultérieur
        session(['step1.total_prix' => $totalPrice]);

        // Retourner la vue de paiement en lui passant le montant total calculé
        return view('IPMS_SIMEXCI_ANGRE.colis.add.payement', [
            'totalPrice' => $totalPrice
        ]);
    }


    public function storePayment(Request $request)
    {

        try {
            $validatedData = $request->validate([
            //     'mode_payement' => 'required|in:bank,mobile_money,cheque,cash',
            //     'numero_compte' => 'required_if:mode_payement,bank|max:255',
            //     'nom_banque' => 'required_if:mode_payement,bank,cheque|max:255',
            //     'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
            //     'numero_tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
            //     'operateur_mobile' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
            //     'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
            //     'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:1',
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
            //     'montant_reçu.min' => 'Le montant reçu doit être supérieur à zéro.',
            ]);
    
            // Stocker les données en session
            
            session(['step2' => $request->only([
                'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
                'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu',
            ])]);
            return response()->json([
                'success' => true,
                'redirect' => route('ipms_angre_colis.generer.qrcode'),
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
        $data = array_merge(
            session('step1', []),
            session('step2', [])
        );
        // dd($data);
        // Vérifiez que les données de base existent
        if (empty($data) || !isset($data['quantite_colis']) || !is_array($data['quantite_colis'])) {
             // Log l'erreur pour diagnostic
             \Log::error('Données de session invalides ou manquantes pour generer_qrcode.', ['session_data' => $data]);
            return redirect()->back()->with('error', 'Les données de la session sont invalides ou incomplètes. Veuillez recommencer.');
        }

        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'Validé';
       
        // Construction des numéros de téléphone complets avec indicatif
        $expediteurCountryCode = $data['country_code_expediteur'] ?? $data['tel_expediteur_societe'] ?? '';
        $expediteurPhoneNumber = $data['tel_expediteur'] ?? '';
        $expediteurTel = trim($expediteurCountryCode . $expediteurPhoneNumber);

        $destinataireCountryCode = $data['country_code_destinataire'] ?? '';
        $destinatairePhoneNumber = $data['tel_destinataire'] ?? $data['tel_destinataire_societe'] ?? '';
        $destinataireTel = trim($destinataireCountryCode . $destinatairePhoneNumber);

        // dd($expediteurTel, $destinataireTel);

          $expediteur = Expediteur::create([
                'nom' => $data['nom_expediteur'] ?? $data['nom_expediteur_societe'] ?? '',
                'prenom' => $data['prenom_expediteur'] ?? $data['prenom_expediteur_societe'] ?? '',
                'email' => $data['email_expediteur'] ?? $data['email_expediteur_societe'] ?? null,
                'tel' => $expediteurTel, // Utilise le numéro complet
                'agence' => $data['agence_expedition'] ?? $data['agence_expediteur_societe'] ?? null, // Gère le cas où l'agence n'est pas définie
                'lieu_expedition' => $data['adresse_expediteur'] ?? $data['adresse_expediteur_societe'] ?? 'null',
            ]);

            $destinataire = Destinataire::create([
                'nom' => $data['nom_destinataire'] ?? $data['nom_destinataire_societe'] ?? '',
                'prenom' => $data['prenom_destinataire'] ?? $data['prenom_destinataire_societe'] ?? '',
                'email' => $data['email_destinataire'] ?? $data['email_destinataire_societe'] ?? null,
                'tel' => $destinataireTel, // Utilise le numéro complet
                'agence' => $data['agence_destination'] ?? $data['agence_destinataire_societe'] ?? null, // Gère le cas où l'agence n'est pas définie
                'lieu_destination' => $data['adresse_destinataire'] ?? $data['adresse_destinataire_societe'] ?? 'null',
            ]);


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

        // --- Création des Colis, Paiements et QR Codes ---
        $colisEnregistres = []; // Pour stocker les modèles Colis sauvegardés
        $erreursCreation = [];

        foreach ($data['quantite_colis'] as $index => $quantite) {
            if ($quantite <= 0) continue; // Ignorer si quantité invalide

            $hauteur = $data['hauteur'][$index] ?? null;
            $largeur = $data['largeur'][$index] ?? null;
            $longueur = $data['longueur'][$index] ?? null;
            $dimension_result = (isset($hauteur, $largeur, $longueur)) ? "{$hauteur}x{$largeur}x{$longueur}" : null;

            $referenceColis = $data['reference_colis'] ?? ('REF-' . uniqid());
            $agence = $data['agence_expedition'] ?? $data['agence_expedition_societe'] ?? null;

            //    dd($agence);
                $lastIdRef = DB::table('colis')
                    ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                    ->where('colis.mode_transit', $data['mode_transit'])
                    ->where('expediteurs.agence', $agence)
                    ->max('colis.id_reference');

                $id_reference = ($lastIdRef ?? 0) + 1;

            $colisItemData = [
                'devise' => 'FCFA',
                'reference_colis' => $referenceColis,
                'id_reference' => $id_reference,
                'reference_contenaire' => $data['reference_contenaire'] ?? null,
                'quantite_colis' => $quantite,
                'service' => $data['service'][$index] ?? null,
                'prix_transit_colis' => $data['prix'][$index] ?? 0,
                'poids_colis' => $data['poids_colis'][$index] ?? null,
                'mode_transit' => $data['mode_transit'] ?? null,
                'status' => $data['status'],
                'etat' => $data['etat'],
                'type_colis' => $data['type_colis'][$index] ?? null,
                'dimension_result' => $dimension_result,
                'description_colis' => $data['description_colis'][$index] ?? null,
                'expediteur_id' => $expediteur->id,
                'destinataire_id' => $destinataire->id,
                'agent_id' => $agentId,
                'qr_code_path' => null,
                // 'montant_payé' => $data['etat'], // Initialisé à null
            ];
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

                // Ajouter le modèle sauvegardé et mis à jour à notre collection
                $colisEnregistres[] = $colisModel->fresh(); // Recharger le modèle avec le qr_path
            } catch (\Exception $e) {
                \Log::error("Erreur création colis/paiement/QR pour index {$index}: " . $e->getMessage(), ['data' => $colisItemData]);
                // dd($e->getMessage());
                $erreursCreation[] = "Erreur lors de la création du colis avec référence {$referenceColis}.";
                // Peut-être ajouter une logique de transaction/rollback ici
            }
        }

        // S'il y a eu des erreurs, rediriger avec les messages
        if (!empty($erreursCreation)) {
            return redirect()->back()->with('error', implode('<br>', $erreursCreation));
        }

        // Si tout s'est bien passé mais aucun colis créé (ex: quantité 0 partout)
        if (empty($colisEnregistres)) {
             return redirect()->back()->with('error', 'Aucun colis n\'a été créé. Vérifiez les quantités.');
        }


        $colisEnregistres = collect($colisEnregistres);

        // Récupérer le premier colis
        $firstColis = $colisEnregistres->first();

        // Vérifier si $firstColis existe avant d'accéder à ses propriétés
        $firstInfo = [
            'id' => $firstColis?->id,
            'reference_colis' => $firstColis?->reference_colis,
            'nom_destinataire' => optional($firstColis?->destinataire)->nom,
            'prenom_destinataire' => optional($firstColis?->destinataire)->prenom,
            'tel_destinataire' => optional($firstColis?->destinataire)->tel,
            'adresse_destinataire' => optional($firstColis?->destinataire)->lieu_destination,
            'nom_expediteur' => optional($firstColis?->expediteur)->nom,
            'prenom_expediteur' => optional($firstColis?->expediteur)->prenom,
            'tel_expediteur' => optional($firstColis?->expediteur)->tel,
            'devise' => optional($firstColis?->expediteur)->devise,
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

            $expediteurTelForSms = $expediteurTel;
            $destinataireTelForSms = $destinataireTel;

            $colisReferences = $colisEnregistres
                                ->pluck('reference_colis')
                                ->unique()
                                ->implode(', ');
                                
            $messageSmsDestinataire = "Bonjour, un colis (Réf: {$colisReferences}) vous est destiné. Il a été créé par {$expediteur->nom} et est en attente d'expédition. Vous serez notifié(e) de son avancement.";

            $messageSmsExpediteur = "Cher(e) client(e), votre colis (Réf: {$colisReferences}) a été enregistrer et est en attente d'expédition. Merci de votre confiance. Suivi : https://aft-app.com";
            // dd($expediteurTelForSms, $destinataireTelForSms, $messageSmsExpediteur, $messageSmsDestinataire);
            // Envoi du SMS à l'expéditeur
            if ($expediteurTel) { 
                try {
                    $infobipService->sendSms($expediteurTel, $messageSmsExpediteur);
                    Log::info("SMS envoyé à l'expéditeur {$expediteurTel} pour le colis {$colisReferences}.");
                } catch (\RuntimeException $e) {
                    Log::error("⚠️ Erreur de configuration Infobip lors de l'envoi SMS à l'expéditeur: " . $e->getMessage(), [
                        'phone_number' => $expediteurTel,
                        'message' => $messageSmsExpediteur
                    ]);
                } catch (\Throwable $e) {
                    Log::error("⚠️ Une erreur inattendue est survenue lors de l'envoi du SMS à l'expéditeur ! " . $e->getMessage(), [
                        'phone_number' => $expediteurTel,
                        'message' => $messageSmsExpediteur,
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



        return view('IPMS_SIMEXCI_ANGRE.colis.add.complete',[
            'colis' => $colisEnregistres,
            'first' => $firstInfo,
            'totalQuantite' => $totalQuantite,
            'totalPrixTransit' => $totalPrixTransit,
            'restePaye' => $restePaye,
            'mode_payement' => $mode_payement,
            'totalMontantPaye' => $totalMontantPaye,
        ]);

    }


    public function storeProduit(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'categorie' => 'required|string|max:100|in:Colis,Service,Remise',
            'prix' => 'required|numeric|min:0',
             'agence' => 'required|string|max:255',
        ]);
    
        Produit::create([
            'description' => $request->description,
            'categorie' => $request->categorie,
            'prix' => $request->prix,
            'agence' => $request->agence,
        ]);
    
        return response()->json(['message' => 'Produit ajouté avec succès !'], 201);
    }
    
    
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
            'lieu_rraison' => $request->lieu_livraison,
            'agence' => $request->agence,
            'type_client' => $request->type_client ?? 'expediteur',
        ]);

        return redirect()->back()->with('success', 'Destinataire créé avec succès !');
    }


    public function show($id)
    {
        //
    }

    public function hold()
    {
        return view('IPMS_SIMEXCI_ANGRE.colis.hold');
    }

    public function dump()
    {
        return view('IPMS_SIMEXCI_ANGRE.colis.dump');
    }

    public function suivi()
    {
        return view('IPMS_SIMEXCI_ANGRE.colis.suivi');
    }

    public function history()
    {
        return view('admin.colis.history');
    }

    public function update(Request $request, $id)
    {
        //
    }
    public function edit_hold($id)
    {
        $colis = Colis::findOrFail($id);

        return view('IPMS_SIMEXCI_ANGRE.colis.edit_hold', compact('colis'));
    }

    // Fonction update pour les colis en attente
    public function update_hold(Request $request, $id)
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
        // Mise à jour des champs
        $colis->update([
            'destinataire_agence' => $request->input('destinataire_agence'),
            'destinataire_tel' => $request->input('destinataire_tel'),
            'quantite_colis' => $request->input('quantite_colis'),
            'valeur_colis' => $request->input('valeur_colis'),
            'mode_transit' => $request->input('mode_transit'),
            'poids_colis' => $request->input('poids_colis'),
            'prix_transit_colis' => $request->input('prix_transit_colis'),
            'status' => 'payé',
            'etat' => 'Devis', // Ajout du statut
        ]);
        // Redirection avec un message de succès
        return redirect()->route('ipms_angre_colis.hold')->with('success', 'Colis mis à jour avec succès !');
    }

  
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
        ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
        ->get(); // Exécute la requête une seule fois

        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                $editUrl = route('ipms_angre_colis.hold.edit', ['id' => $row->id]); // Si vous avez une route d'édition pour chaque colis

                return '
                    <div class="btn-group">
                        <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modify" data-bs-target="#modifModal">
                            <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
            ->make(true);
    }
}


    public function get_colis_suivi(Request $request)
    {
        if ($request->ajax()) {
            try {

                $colis = Colis::select(
                    'colis.id',
                    'colis.reference_colis',
                    'colis.quantite_colis',
                    'colis.prix_transit_colis',
                    'colis.expediteur_id',
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
                ->where('colis.etat', '!=', 'Dechargé')
                ->whereNull('colis.archived_at') 
                ->orderBy('colis.created_at', 'desc')
                ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                ->where('colis.mode_transit', 'aerien')
                ->get();

                $colisIds = $colis->pluck('id')->unique()->toArray();

                $paiements = Paiement::whereIn('colis_id', $colisIds)
                                    ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye'))
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

    public function get_colis_dump(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Requête pour récupérer les colis pour l'agence d'Angré, avec l'agent créateur
                $query = Colis::with(['expediteur', 'destinataire', 'paiement', 'agent']) // Charger la relation 'agent'
                    ->where('etat', 'Dechargé')
                    ->whereHas('destinataire', function ($query) {
                        $query->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                    })
                    ->where('recup', 'oui');
                
                $colisGrouped = $query->get()->groupBy('reference_colis');
    
                $processedData = $colisGrouped->map(function ($group) {
                    $firstColis = $group->first();
                    $prixTotalColis = $group->sum('prix_transit_colis');
                    $montantTotalPaye = $firstColis->paiement ? (float)$firstColis->paiement->montant_paye : 0;
                    $paymentStatus = $firstColis->paiement ? $firstColis->paiement->statut_paiement : 'non payé';
    
                    // AJOUT : Récupérer l'ID de l'agence du créateur
                    $creatorAgenceId = optional($firstColis->agent)->agence_id;
    
                    return [
                        'reference_colis' => $firstColis->reference_colis,
                        'nombre_de_colis' => $group->sum('quantite_colis'),
                        'expediteur_nom' => optional($firstColis->expediteur)->nom,
                        'expediteur_prenom' => optional($firstColis->expediteur)->prenom,
                        'expediteur_tel' => optional($firstColis->expediteur)->tel,
                        'destinataire_nom' => optional($firstColis->destinataire)->nom,
                        'destinataire_prenom' => optional($firstColis->destinataire)->prenom,
                        'destinataire_tel' => optional($firstColis->destinataire)->tel,
                        'destinataire_agence' => optional($firstColis->destinataire)->agence,
                        'etat' => $firstColis->etat,
                        'created_at' => $firstColis->created_at ? $firstColis->created_at->format('d/m/Y H:i') : 'N/A',
                        'payment_status' => $paymentStatus,
                        'prix_total' => $prixTotalColis,
                        'montant_paye' => $montantTotalPaye,
                        'colis_ids' => json_encode($group->pluck('id')->toArray()),
                        'first_colis_id' => $firstColis->id,
                        'creator_agence_id' => $creatorAgenceId, // On passe l'ID au front-end
                    ];
                })->values();
    
                return DataTables::of($processedData)
                    ->addColumn('statut_paiement', function ($row) {
                        $status = $row['payment_status'];
                        $iconClass = 'fas fa-times-circle'; $iconColor = 'red'; $title = 'Impayé';
    
                        if ($status === 'payé') {
                            $iconClass = 'fas fa-check-circle'; $iconColor = 'green'; $title = 'Payé';
                        } elseif ($status === 'partiellement payé') {
                            $iconClass = 'fas fa-exclamation-circle'; $iconColor = 'orange'; $title = 'Paiement Partiel';
                        }
                        
                        return '<span title="' . $title . '"><i class="' . $iconClass . '" style="color: ' . $iconColor . '; font-size: 1.3em;"></i></span>';
                    })
                    ->addColumn('action', function ($row) {
                        $firstColisId = $row['first_colis_id'];
                        
                        // ADAPTATION : Utiliser les noms de route pour 'ipms_angre_colis'
                        $editUrl = route('ipms_angre_colis.valide.edit', ['id' => $firstColisId]);
                        $invoiceUrl = route('ipms_angre_colis.valide.edit.invoice', ['id' => $firstColisId]);
                        $deleteUrl = route('ipms_angre_colis.destroy.colis.valide', ['reference' => $row['reference_colis']]);
    
                        $editBtn = '<a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modifier"><i class="fas fa-edit"></i></a>';
                        
                        $payBtn = '';
                        if ($row['payment_status'] !== 'payé') {
                            // MODIFICATION : On ajoute data-creator-agence-id au bouton
                            $payBtn = '<button type="button" class="btn btn-sm btn-success pay-btn"
                                        data-reference="' . htmlspecialchars($row['reference_colis'], ENT_QUOTES, 'UTF-8') . '"
                                        data-total="' . $row['prix_total'] . '"
                                        data-paid="' . $row['montant_paye'] . '"
                                        data-colis-ids="' . htmlspecialchars($row['colis_ids'], ENT_QUOTES, 'UTF-8') . '"
                                        data-colis-id="' . $row['first_colis_id'] . '"
                                        data-creator-agence-id="' . $row['creator_agence_id'] . '"
                                        title="Enregistrer un Paiement">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>';
                        } else {
                            $payBtn = '<button type="button" class="btn btn-sm btn-secondary" disabled title="Paiement complet">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>';
                        }
    
                        $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn" data-url="' . $deleteUrl . '"><i class="fas fa-trash"></i></button>';
                        $invoiceBtn = '<a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Facture"><i class="fas fa-file-invoice"></i></a>';
    
                        return '<div class="action-buttons-container">' . $editBtn . $payBtn . $deleteBtn . $invoiceBtn . '</div>';
                    })
                    ->rawColumns(['action', 'statut_paiement'])
                    ->make(true);
    
            } catch (\Exception $e) {
                Log::error('Erreur dans get_colis_dump pour Angré: ' . $e->getMessage());
                return response()->json(['error' => 'Une erreur interne est survenue.'], 500);
            }
        }
    }
    



    public function get_devis_colis(Request $request)
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
            ->whereIn('etat', ['Devis','Validé'])  // Filtre l'état des colis
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
            ->get(); // Exécute la requête une seule fois

            return DataTables::of($colis)
                ->addColumn('etat', function ($row) {
                    if ($row->etat === 'Devis') {
                        return 'Dévis validé'; // Si l'état est "Devis", afficher "Dévis validé"
                    } elseif ($row->etat === 'Validé') {
                        return 'Colis validé'; // Si l'état est "Validé", afficher "Colis validé"
                    }
                    return $row->etat; // Sinon, retourner l'état original
                })
                ->addColumn('action', function ($row) {
                   $printUrl = route('ipms_angre_colis.qrcode.edit', ['id' => $row->id]); // Si vous avez une route d'édition pour chaque colis
                   

                    return '
                        <div class="btn-group">
                            <a href="' . $printUrl . '" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-print"></i>
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
                ->where('expediteurs.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
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

                        $editUrl = route('ipms_angre_colis.valide.edit', ['id' => $firstColisId]); // Route pour modifier (utilise l'ID)
                        $invoiceUrl = route('ipms_angre_colis.valide.edit.invoice', ['id' => $firstColisId]); // Route pour la facture (utilise l'ID)
                        $deleteUrl = route('ipms_angre_colis.destroy.colis.valide', ['reference' => $reference]); // Route pour archiver (utilise la référence)

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
            ->where('expediteurs.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
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
                    'id' => $group->first()->id 
                ];
            })->values();
    
            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Chargé' ? 'Dévis Chargé' : 'Colis Chargé';
                })
                ->addColumn('action', function ($row) {
                    $deleteUrl = route('ipms_angre_colis.destroy.colis.valide', ['reference' => $row['reference_colis']]);
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
                )
                ->where('agence_expedition', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                ->get();
            return DataTables::of($bateaux)
                ->editColumn('date_depart', function ($row) {
                    return $row->date_depart ? \Carbon\Carbon::parse($row->date_depart)->format('d/m/Y H:i') : 'N/A';
                })
                ->editColumn('date_arriver', function ($row) {
                    return $row->date_arriver ? \Carbon\Carbon::parse($row->date_arriver)->format('d/m/Y H:i') : 'N/A';
                })->addColumn('actions', function ($row) {
                    $editUrl = route('ipms_angre_colis.bateaux.edit', $row->id);
                    $deleteUrl = route('ipms_angre_colis.bateaux.destroy', $row->id);
                    $listColisUrl = route('ipms_angre_colis.liste.bateau', $row->reference_conteneur);
                
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

    // public function cargaison_ferme(Request $request)
    // {

    //     $agencesDestination = Agence::where('pays_agence', 'Côte d\'Ivoire')->get();

    //     // Récupérer les références de conteneurs fermés, sans doublons
    //     $referenceFermes = Colis::where('etat', 'Fermé')->pluck('reference_contenaire')->unique()->toArray();

    //     // Obtenir le mois et l'année actuels
    //     $mois = Carbon::now()->translatedFormat('F'); // Ex: Janvier, Février...
    //     $annee = Carbon::now()->year;

    //     return view('IPMS_SIMEXCI_ANGRE.cargaison.cargaison_ferme', compact('agencesDestination', 'referenceFermes', 'mois', 'annee'));
    // }


    public function cargaison_ferme(Request $request)
{
    // Récupérer les agences de destination
    $agencesDestination = Agence::where('pays_agence', 'Côte d\'Ivoire')->get();

    // Étape 1 : Récupérer tous les colis fermés
    $colisFermes = Colis::select('colis.reference_contenaire', 'colis.reference_vol')
                       ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                        ->where('colis.etat', 'Fermé')
                        ->where('expediteurs.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche') // <-- adapte selon besoin
                        ->get();
    // Étape 2 : Fusionner les références conteneur et vol dans un tableau unique
    $referencesColis = collect($colisFermes)
        ->flatMap(function ($colis) {
            return [$colis->reference_contenaire, $colis->reference_vol];
        })
        ->filter()  // Supprimer les valeurs nulles
        ->unique()
        ->values()
        ->toArray();

    // Étape 3 : Récupérer les références avec le nombre d’occurrences dans Bateaux
    $referencesBateauxCounts = Bateaux::whereIn('reference_conteneur', $referencesColis)
        ->selectRaw('reference_conteneur, COUNT(*) as total')
        ->groupBy('reference_conteneur')
        ->pluck('total', 'reference_conteneur') // ['REF123' => 2, 'REF456' => 1, ...]
        ->toArray();

    // Étape 4 : Ne garder que les références qui n'existent pas OU qui existent 1 fois
    $referenceFermes = collect($referencesColis)
        ->filter(function ($ref) use ($referencesBateauxCounts) {
            return !isset($referencesBateauxCounts[$ref]) || $referencesBateauxCounts[$ref] < 2;
        })
        ->values()
        ->toArray();

    // Mois et année
    $mois = Carbon::now()->translatedFormat('F');
    $annee = Carbon::now()->year;

    return view('IPMS_SIMEXCI_ANGRE.cargaison.cargaison_ferme', compact('agencesDestination', 'referenceFermes', 'mois', 'annee'));
}


    public function edit_bateaux($id)
    {
        $bateau = Bateaux::findOrFail($id);
        return view('IPMS_SIMEXCI_ANGRE.cargaison.edit_bateau', compact('bateau'));
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
    
        return view('IPMS_SIMEXCI_ANGRE.cargaison.liste_bateau', compact('colis'));
    }

    public function update_bateaux(Request $request, $id)
    {
        // dd($id);
        $request->validate([
            'reference_bateau' => 'required|string|max:255',
            'reference_contenaire' => 'required|string|max:255',
            'date_depart' => 'required|date',
            'date_arriver' => 'required|date',
        ]);
    
        $bateau = Bateaux::findOrFail($id);
        $bateau->reference_bateau = $request->reference_bateau;
        $bateau->reference_conteneur = $request->reference_contenaire;
        $bateau->created_at = $request->date_depart;
        $bateau->date_arriver = $request->date_arriver;
        $bateau->save();
    
        return redirect()->route('ipms_angre_colis.cargaison.ferme')->with('success', 'Bateau modifié avec succès.');
    }
    // Fonction edit pour les colis en attente

    public function edit_colis_valide($id)
    {
        $colis = Colis::findOrFail($id);
        // dd($colis);
        return view('IPMS_SIMEXCI_ANGRE.colis.edit_colis_valide', compact('colis'));
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
            'prix_transit_colis' => $request->prix_transit_colis,
        ]);
     // dd($colis);
        // Redirection avec un message de succès
        return redirect()->route('ipms_angre_colis.colis.valide')->with('success', 'Colis mis à jour avec succès !');
    }


    public function print_facture($id)
    {
        $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        
        // Retournez une vue pour l'impression
        return view('IPMS_SIMEXCI_ANGRE.colis.colis_facture', compact('colis'));
    }
    
    public function colis_valide(Request $request)
    {
        return view('IPMS_SIMEXCI_ANGRE.colis.valide');
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
                // $colis->archived_at = now();
                // $colis->save();
                  $colis->delete();
            }
    
            return response()->json(['success' => 'Colis supprimé avec succès !']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }

    public function store_bateaux(Request $request)
    {
        try {
            // dd($request->all());
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
    
            // Vérification manuelle si le numero_bateau existe déjà (s’il est fourni)
            if ($request->filled('numero_bateau')) {
                $exists = Bateaux::where('numero_bateau', $request->numero_bateau)->exists();
                if ($exists) {
                    return redirect()->back()
                        ->with('error', 'Le numéro de bateau "' . $request->numero_bateau . '" existe déjà.')
                        ->withInput();
                }
            }
            // dd($request->all());
            // Création du bateau
            Bateaux::create([
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

        return view('IPMS_SIMEXCI_ANGRE.devis.edit_qrcode', compact('colis'));
    }

    public function get_colis_contenaire(Request $request)
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
            ->where('etat', 'Chargé')  // Filtre l'état des colis
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
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

    // public function get_colis_vol(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $colis = Colis::select(
    //             'colis.*',  // Sélectionne toutes les colonnes de colis
    //             'colis.reference_colis as reference_colis',
    //             'expediteurs.nom as expediteur_nom', 
    //             'expediteurs.prenom as expediteur_prenom', 
    //             'expediteurs.tel as expediteur_tel', 
    //             'expediteurs.agence as expediteur_agence', 
    //             'destinataires.nom as destinataire_nom', 
    //             'destinataires.prenom as destinataire_prenom', 
    //             'destinataires.agence as destinataire_agence', 
    //             'destinataires.tel as destinataire_tel',
    //             'colis.etat as etat',
    //             'colis.created_at as created_at'
    //         )
    //         ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table expediteurs
    //         ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table destinataires
    //         ->where('colis.mode_transit', 'Aerien')  // Filtre pour le mode de transit
    //         ->where('colis.etat', 'Chargé')  // Filtre l'état des colis
    //         ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
    //         ->get(); // Exécute la requête 

    //         return DataTables::of($colis)
    //             ->addColumn('action', function ($row) {
    //                 $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis

    //                 return '
    //                     <div class="btn-group">
    //                         <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
    //                             <i class="fas fa-eye"></i>
    //                         </a>
    //                         <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
    //                             <i class="fas fa-credit-card"></i>
    //                         </a>
    //                     </div>
    //                 ';
    //             })
    //             ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
    //             ->make(true);
    //     }
    // }
   
    public function contenaire_fermer(Request $request)
    {
    
        // dd($request);
        try {
            // Démarrez une transaction de base de données pour garantir l'atomicité
            DB::beginTransaction();
    
            $agence = 'IPMS-SIMEX-CI Angre 8ème Tranche'; // Définir l'agence une seule fois

            $colis = Colis::where('etat', 'Chargé')
                ->where('mode_transit', 'aerien')
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

    public function vol_fermer(Request $request)
    {
        // dd($request);
        try {
            // Démarrez une transaction de base de données pour garantir l'atomicité
            DB::beginTransaction();
            $agence = 'IPMS-SIMEX-CI Angre 8ème Tranche'; // Définir l'agence une seule fois

        $colis = Colis::where('etat', 'Chargé')
            ->where('mode_transit', 'aerien')
            ->whereHas('expediteur', function ($query) use ($agence) {
                $query->where('agence', $agence);
            })
            ->get();

        $count = $colis->count();
    
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





public function devis_hold(Request $request)
{
    return view('IPMS_SIMEXCI_ANGRE.devis.hold');
}

public function liste_contenaire(Request $request)
{
    $referenceContenaire = $request->input('reference_contenaire', $this->generateReferenceContenaire());
    return view('IPMS_SIMEXCI_ANGRE.devis.liste_contenaire',compact('referenceContenaire'));
}

public function liste_vol(Request $request)
{
    $referenceVol = $request->input('reference_vol', $this->generateReferenceVol());
    return view('IPMS_SIMEXCI_ANGRE.cargaison.liste_vol',compact('referenceVol'));
}


public function liste_ballon()
{
    // Récupérer uniquement les bateaux non récupérés
    $ballons = Bateaux::select('id', 'reference_bateau', 'date_arriver', 'reference_conteneur')
                      ->where('recuperer', '!=', 'oui')
                      ->where('agence_destination', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                      ->where('type', '=', 'ballon')
                      ->get();

    return view('IPMS_SIMEXCI_ANGRE.ballon.liste_ballon', compact('ballons'));
}

public function validerBallon(Request $request)
{
    // Validation des données entrantes
    $request->validate([
        'reference_conteneur' => 'required|string',
    ]);

    try {
        // Utilisation d'une transaction pour garantir la cohérence des mises à jour
        return DB::transaction(function () use ($request) {
            $ballon = Bateaux::where('reference_conteneur', $request->reference_conteneur)->first();

            // dd($ballon);
            if (!$ballon) {
                throw new Exception('🚢 Bateau non trouvé.');
            }

            // Vérifier si le bateau est déjà récupéré
            if ($ballon->recuperer === 'oui' && $ballon->type === 'ballon') {
                throw new Exception('⚠️ Ce ballon a déjà été récupéré.');
            }

            // dd($ballon);
            $referenceConteneur = $ballon->reference_conteneur;

            // Récupérer tous les colis liés à ce conteneur
            $colis = Colis::where('reference_contenaire', $referenceConteneur)->get();

            if ($colis->isEmpty()) {
                throw new Exception('📦 Aucun colis trouvé pour ce conteneur.');
            }

            // Mise à jour en masse des colis récupérés
            Colis::where('reference_contenaire', $referenceConteneur)->update(['recup' => 'oui']);

            // Mettre à jour le champ "recuperer" du bateau
            $ballon->update(['recuperer' => 'oui']);

            return response()->json([
                'success' => true,
                'message' => '✅ Tous les colis et le ballon ont été récupérés avec succès.',
            ]);
        });

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400);
    }
}


public function get_ballon(Request $request)
{
    if ($request->ajax()) {
        $ballon = Bateaux::select(
            'reference_bateau',
            'created_at as date_depart',
            'date_arriver'
        )->where('agence_destination', 'IPMS-SIMEX-CI Angre 8ème Tranche')
        ->where('recuperer', '=', 'oui')
        ->get();

        // dd($ballon);
        return DataTables::of($ballon)
            ->editColumn('date_depart', function ($row) {
                return $row->date_depart ? \Carbon\Carbon::parse($row->date_depart)->format('d/m/Y H:i') : 'N/A';
            })
            ->editColumn('date_arriver', function ($row) {
                return $row->date_arriver ? \Carbon\Carbon::parse($row->date_arriver)->format('d/m/Y H:i') : 'N/A';
            })
            ->make(true);
    }
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
            return redirect()->route('ipms_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
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
    return view('IPMS_SIMEXCI.invoice.edit_bon_livraison', compact(
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

    ));
}

public function editInvoice($id)
{
    $colis_principal = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);

    $colisEnregistres = Colis::where('reference_colis', $colis_principal->reference_colis)->get();
    if ($colisEnregistres->isEmpty()) {
        return redirect()->back()->with('error', 'Aucun colis n\'a été enregistré avec cette référence.');
    }

    $firstColis = $colisEnregistres->first();
    $firstInfo = [
        'id' => $firstColis->id,
        'reference_colis' => $firstColis->reference_colis,
        'nom_destinataire' => optional($firstColis->destinataire)->nom,
        'prenom_destinataire' => optional($firstColis->destinataire)->prenom,
        'tel_destinataire' => optional($firstColis->destinataire)->tel,
        'adresse_destinataire' => optional($firstColis->destinataire)->lieu_destination,
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

    return view('IPMS_SIMEXCI_ANGRE.invoice.edit', [
        'colis' => $colisEnregistres,
        'first' => $firstInfo,
        'totalQuantite' => $totalQuantite,
        'totalPrixTransit' => $totalPrixTransit,
        'restePaye' => $restePaye,
        'mode_payement' => $mode_payement,
        'totalMontantPaye' => $totalMontantPaye,
    ]);
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
    // 3. Pas besoin de regénérer le QR code ici, on utilise celui déjà généré.
    $pdf = PDF::loadView('IPMS_SIMEXCI_ANGRE.colis.add.edit_etiquette', [
            'colis' => $etiquettes, // La collection de clones
            'totalEtiquettes' => $quantite // Le nombre total d'étiquettes à générer
        ])
        ->setPaper('a6', 'landscape')
        ->setOption('isRemoteEnabled', true); // Important pour les images externes/locales via public_path

    // 5. Retourner le PDF pour téléchargement
    $safeRef = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $colis->reference_colis ?? $colis->id);
    $fileName = 'etiquettes_' . $safeRef . '.pdf';

    return $pdf->download($fileName);
}

public function editFacture($id)
{
    $colis_principal = Colis::find($id);

    if (!$colis_principal) {
        return redirect()->route('ipms_angre_colis.hold')->with('error', 'Colis non trouvé.');
    }

    $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)
                            ->with(['expediteur', 'destinataire', 'paiement'])
                            ->get();

    if ($colisCollection->isEmpty()) {
        return redirect()->route('ipms_angre_colis.hold')->with('warning', 'Aucun colis trouvé avec cette référence.');
    }

    $firstColis = $colisCollection->first();

    $date_facture = now();
    $expediteur = optional($firstColis->expediteur)->nom . ' ' . optional($firstColis->expediteur)->prenom;
    $tel_expediteur = optional($firstColis->expediteur)->tel;
    $destinataire = optional($firstColis->destinataire)->nom . ' ' . optional($firstColis->destinataire)->prenom;
    $adresse_destinataire = optional($firstColis->destinataire)->lieu_destination; ;
    $tel_destinataire = optional($firstColis->destinataire)->tel;
    $numero_facture = 'FA-' . str_pad($firstColis->id, 5, '0', STR_PAD_LEFT);
    $reference_colis = $firstColis->reference_colis;
    $devise = $firstColis->devise;

    $groupedItems = [];
    $prix_total_invoice = 0;

    foreach ($colisCollection as $colis) {
        $prixLigne = (float)($colis->prix_transit_colis ?? 0);
        $quantiteLigne = (int)($colis->quantite_colis ?: 1);
        $serviceDescription = trim($colis->service ?? 'Service Non Défini');

      
        $prixUnitaire = ($quantiteLigne != 0) ? $prixLigne / $quantiteLigne : 0;

        $groupKey = $serviceDescription;
        // dd($groupKey);
        if (!isset($groupedItems[$groupKey])) {
            $groupedItems[$groupKey] = [
                'service'           => $serviceDescription,
                'quantite_totale'   => 0, 
                'montant_total_ligne' => 0, 
                'prix_unitaire'     => $prixUnitaire, 
                'type_colis'        => $colis->type_colis ?? 'N/A',
            ];
        } else {
             
        }


        $groupedItems[$groupKey]['quantite_totale'] += $quantiteLigne;
        $groupedItems[$groupKey]['montant_total_ligne'] += $prixLigne;

        $prix_total_invoice += $prixLigne;
    }
    // dd($groupedItems);
    $invoiceItems = array_values($groupedItems);

    $ids_colis = $colisCollection->pluck('id')->toArray();
    $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();

    $mode_payement = $paiements->pluck('methode_paiement')->unique()->first();
    $totalMontantDue = $prix_total_invoice;
    $totalMontantPaye = $paiements->sum('montant_paye');
    $restePaye = $totalMontantDue - $totalMontantPaye;

    $agent = Auth::user();
    $id_agent = $agent->id;
    $nom_agent = $agent->first_name . ' ' . $agent->last_name;

    $existingInvoice = Invoice::where('numero_facture', $numero_facture)->first();
    if (!$existingInvoice) {
         Invoice::create([
             'nom_agent' => $nom_agent,
             'nom_expediteur' => $expediteur,
             'nom_destinataire' => $destinataire,
             'expediteur_id' => optional($firstColis->expediteur)->id,
             'destinataire_id' => optional($firstColis->destinataire)->id,
             'agent_id' => $id_agent,
             'montant' => $prix_total_invoice ?? 0,
             'numero_facture' => $numero_facture,
         ]);
    }

    $prix_total = $prix_total_invoice;

    return view('IPMS_SIMEXCI_ANGRE.colis.add.edit_invoice', compact(
        'date_facture',
        'reference_colis',
        'expediteur',
        'tel_expediteur',
        'destinataire',
        'tel_destinataire',
        'prix_total',
        'mode_payement',
        'invoiceItems', 
        'numero_facture',
        'totalMontantPaye',
        'restePaye',
        'devise',
        'adresse_destinataire',
    ));
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

    $pdf = PDF::loadView('IPMS_SIMEXCI_ANGRE.invoice.edit_etiquette', [
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
    $colis_principal = Colis::find($id);

    if (!$colis_principal) {
        return redirect()->route('ipms_angre_colis.hold')->with('error', 'Colis non trouvé.');
    }

    $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)
                            ->with(['expediteur', 'destinataire', 'paiement'])
                            ->get();

    if ($colisCollection->isEmpty()) {
        return redirect()->route('ipms_angre_colis.hold')->with('warning', 'Aucun colis trouvé avec cette référence.');
    }

    $firstColis = $colisCollection->first();

    $date_facture = now();
    $expediteur = optional($firstColis->expediteur)->nom . ' ' . optional($firstColis->expediteur)->prenom;
    $tel_expediteur = optional($firstColis->expediteur)->tel;
    $destinataire = optional($firstColis->destinataire)->nom . ' ' . optional($firstColis->destinataire)->prenom;
    $tel_destinataire = optional($firstColis->destinataire)->tel;
    $adresse_destinataire = optional($firstColis->destinataire)->lieu_destination;
    $numero_facture = 'FA-' . str_pad($firstColis->id, 5, '0', STR_PAD_LEFT);
    $reference_colis = $firstColis->reference_colis;
    $devise = $firstColis->devise;

    $groupedItems = [];
    $prix_total_invoice = 0; 

    foreach ($colisCollection as $colis) {
        $prixLigne = (float)($colis->prix_transit_colis ?? 0);
        $quantiteLigne = (int)($colis->quantite_colis ?: 1);
        $serviceDescription = trim($colis->service ?? 'Service Non Défini');

        $prixUnitaire = ($quantiteLigne != 0) ? $prixLigne / $quantiteLigne : 0;

        $groupKey = $serviceDescription;
        if (!isset($groupedItems[$groupKey])) {
            $groupedItems[$groupKey] = [
                'service'           => $serviceDescription,
                'quantite_totale'   => 0, 
                'montant_total_ligne' => 0,
                'prix_unitaire'     => $prixUnitaire, 
                'type_colis'        => $colis->type_colis ?? 'N/A',
            ];
        } else {
            
        }


        $groupedItems[$groupKey]['quantite_totale'] += $quantiteLigne;
        $groupedItems[$groupKey]['montant_total_ligne'] += $prixLigne;

        $prix_total_invoice += $prixLigne;
    }
    
    $invoiceItems = array_values($groupedItems);

    $ids_colis = $colisCollection->pluck('id')->toArray();
    $paiements = Paiement::whereIn('colis_id', $ids_colis)->get();

    $mode_payement = $paiements->pluck('methode_paiement')->unique()->first();
    $totalMontantDue = $prix_total_invoice;
    $totalMontantPaye = $paiements->sum('montant_paye');
    $restePaye = $totalMontantDue - $totalMontantPaye;

    // --- Agent and Invoice Record ---
    $agent = Auth::user();
    $id_agent = $agent->id;
    $nom_agent = $agent->first_name . ' ' . $agent->last_name;

    $existingInvoice = Invoice::where('numero_facture', $numero_facture)->first();
    if (!$existingInvoice) {
         Invoice::create([
             'nom_agent' => $nom_agent,
             'nom_expediteur' => $expediteur,
             'nom_destinataire' => $destinataire,
             'expediteur_id' => optional($firstColis->expediteur)->id,
             'destinataire_id' => optional($firstColis->destinataire)->id,
             'agent_id' => $id_agent,
             'montant' => $prix_total_invoice ?? 0,
             'numero_facture' => $numero_facture,
         ]);
    }

    $prix_total = $prix_total_invoice;

    return view('IPMS_SIMEXCI_ANGRE.invoice.edit_invoice', compact(
        'date_facture',
        'reference_colis',
        'expediteur',
        'tel_expediteur',
        'destinataire',
        'tel_destinataire',
        'prix_total', 
        'mode_payement',
        'invoiceItems',
        'numero_facture',
        'totalMontantPaye',
        'restePaye',
        'devise',
        'adresse_destinataire',
    ));
}
public function enregistrerPaiement(Request $request)
{
    try {
        $validated = $request->validate([
            'colis_id'        => 'required|integer|exists:colis,id',
            'montant_a_payer' => 'required|numeric|min:1',
            'colis_ids'       => 'required|json'
        ]);
    } catch (ValidationException $e) {
        return response()->json(['error' => 'Les données fournies sont invalides.', 'details' => $e->errors()], 422);
    }

    $colisIdReference = $validated['colis_id'];
    $nouveauVersementFCFA = (float) $validated['montant_a_payer'];
    $colisIdsDuGroupe = json_decode($validated['colis_ids'], true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($colisIdsDuGroupe)) {
        return response()->json(['error' => 'Format des IDs de colis invalide.'], 400);
    }

    DB::beginTransaction();
    try {
        // Récupérer le colis de référence et son créateur
        $colisDeReference = Colis::with(['paiement', 'agent'])->findOrFail($colisIdReference);
        $paiement = $colisDeReference->paiement;
        $agentCreateur = $colisDeReference->agent;

        if (!$paiement) { throw new \Exception("Dossier de paiement introuvable pour le colis ID {$colisIdReference}."); }
        if (!$agentCreateur) { throw new \Exception("Impossible de trouver l'agent créateur du colis ID {$colisIdReference}."); }

        // Agent qui effectue l'action
        $agentActuel = Auth::user()->agent;
        if (!$agentActuel) { throw new \Exception("Utilisateur connecté n'est pas un agent valide."); }
        
        // --- LOGIQUE CONDITIONNELLE BASÉE SUR L'AGENCE DU CRÉATEUR DU COLIS ---
        
        // CAS 1: Le colis a été créé par un agent de l'agence 7
        if ($agentCreateur->agence_id == 7) {
            $montantTotalDu = (float) Colis::whereIn('id', $colisIdsDuGroupe)->sum('prix_transit_colis');
            $montantDejaPaye = (float) $paiement->montant_paye;
            $montantRestant = $montantTotalDu - $montantDejaPaye;

            if ($nouveauVersementFCFA > ($montantRestant + 1)) {
                throw new \Exception('Le montant du versement (' . $nouveauVersementFCFA . ') ne peut pas dépasser le montant restant à payer (' . $montantRestant . ').');
            }

            Versement::create([
                'paiement_id'       => $paiement->id,
                'montant_versement' => $nouveauVersementFCFA, // Enregistrement en FCFA
                'agent_id'          => $agentActuel->id,
                'colis_id'          => $colisIdReference,
            ]);
            
            $paiement->montant_paye += $nouveauVersementFCFA;
        } 
        // CAS 2: Le colis a été créé par une autre agence
        else {
            $nouveauVersementEUR = round($nouveauVersementFCFA / CurrencyConverterService::FCFA_TO_EUR_RATE, 2);
            $montantTotalDu = (float) Colis::whereIn('id', $colisIdsDuGroupe)->sum('prix_transit_colis');
            $montantDejaPaye = (float) $paiement->montant_paye;
            $montantRestant = $montantTotalDu - $montantDejaPaye;

            if ($nouveauVersementEUR > ($montantRestant + 0.01)) {
                throw new \Exception('Le montant du versement (' . $nouveauVersementEUR . ' EUR) ne peut pas dépasser le montant restant à payer (' . round($montantRestant, 2) . ' EUR).');
            }

            Versement::create([
                'paiement_id'       => $paiement->id,
                'montant_versement' => $nouveauVersementEUR, // Enregistrement en EURO
                'agent_id'          => $agentActuel->id,
                'colis_id'          => $colisIdReference,
            ]);
            
            $paiement->montant_paye += $nouveauVersementEUR;
        }

        // Mise à jour commune du statut
        $totalPayeFinal = (float) $paiement->montant_paye;
        $totalDuFinal = (float) Colis::whereIn('id', $colisIdsDuGroupe)->sum('prix_transit_colis');
        $tolerance = ($agentCreateur->agence_id == 7) ? 1.0 : 0.01;

        if ($totalPayeFinal >= ($totalDuFinal - $tolerance)) {
            $paiement->statut_paiement = 'payé';
        } elseif ($totalPayeFinal > 0) {
            $paiement->statut_paiement = 'partiellement payé';
        } else {
            $paiement->statut_paiement = 'non payé';
        }
        
        $paiement->save();
        DB::commit();

        return response()->json(['success' => 'Paiement enregistré avec succès !']);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur enregistrement versement: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['error' => 'Une erreur interne est survenue: ' . $e->getMessage()], 500);
    }
}
}
