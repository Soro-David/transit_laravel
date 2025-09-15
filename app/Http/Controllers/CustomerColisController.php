<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
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
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail; // Importez la façade Mail
use App\Mail\DevisCreatedMail; // Importez votre Mailable
use App\Mail\ColisValidatedMail;


class CustomerColisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('customer.colis.add');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json($product);
    }

    public function createStep1()
    {
        $id = auth()->user()->getIdUSer();
        // dd($id);
        $user = User::findOrfail($id);
        // Chargement des données
        $agences = Agence::select('nom_agence', 'id')->get();
        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();
        return view('customer.colis.add.step1', compact('agences', 'client_expediteurs', 'client_destinataires','user'));
    }

    /**
     * Enregistre les données de l'étape 1.
     */

         /**
     * Génère une référence de colis unique
     *
     * @return string
     */
    private function generateReferenceContenaire()
    {
        // Récupérer la dernière référence enregistrée
        $lastReference = DB::table('colis')
            ->whereNotNull('reference_contenaire')
            ->orderByDesc('id')
            ->value('reference_contenaire');

            // dd($lastReference);
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

            // dd($lastReference);
        if ($lastReference) {
            // Extraire le numéro (tout ce qui vient après "A")
            $lastNumber = (int) str_replace('A', '', $lastReference);
            $newNumber = $lastNumber + 1;
        } else {
            // Premier vol
            $newNumber = 1;
        }

        // dd($newNumber);
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

        // Agence cible
      

        // Dernier identifiant de référence par mode + agence
        $lastIdRef = DB::table('colis')
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->where('colis.mode_transit', $mode_transit)
            ->max('colis.id_reference');

        // Incrémentation de l'ID de référence
        $nextIdRef = ($lastIdRef ?? 0) + 1;

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
        $id = auth()->user()->getIdUSer();

        $user = User::findOrfail($id);

        $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->distinct()->pluck('pays_agence');

        $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
        $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();


        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();
        // dd($agences);
        // Génère juste les références, sans enregistrer encore dans la base
        // $referenceColis = $this->generateReferenceParMode();
        $referenceColis_maritime = $this->generateReferenceParMode('maritime');
        $referenceColis_aerien = $this->generateReferenceParMode('aerien');
        // dd($referenceColis);
        return view('customer.colis.add_colis', compact(
            'agencesExpedition', 'agencesDestination', 'paysUniques', 'referenceColis_maritime','referenceColis_aerien',
            'client_expediteurs', 'client_destinataires','user'
        ));
    }


public function store_colis(Request $request)
{
    $data = $request->all();

    $data['status'] = $data['mode_payement'] ?? 'non payé';
    $data['etat'] = $data['etat'] ?? 'En attente';

    $expediteurCountryCode = $data['country_code_expediteur'] ?? $data['country_code_expediteur'] ?? '';
    $expediteurPhoneNumber = $data['tel_expediteur'] ?? $data['tel_expediteur_societe'] ?? '';
    $expediteurTel = trim($expediteurCountryCode . $expediteurPhoneNumber);

    $destinataireCountryCode = $data['country_code_destinataire'] ?? $data['country_code_destinataire'] ?? '';
    $destinatairePhoneNumber = $data['tel_destinataire'] ?? $data['tel_destinataire_societe'] ?? '';
    $destinataireTel = trim($destinataireCountryCode . $destinatairePhoneNumber);

    $userId = Auth::id();

    $expediteurData = [
        'nom' => $data['nom_expediteur'] ?? $data['nom_expediteur_societe'] ?? '',
        'prenom' => $data['prenom_expediteur'] ?? $data['prenom_expediteur_societe'] ?? '',
        'email' => $data['email_expediteur'] ?? $data['email_expediteur_societe'] ?? '',
        'tel' => $expediteurTel,
        'user_id' => $userId,
        'agence' => $data['agence_expedition_societe'] ?? $data['agence_particulier_expediteur'] ?? $data['agence_expedition'] ?? '',
        'adresse' => $data['adresse_expediteur_societe'] ?? $data['adresse_expediteur'] ?? 'null',
    ];

    $destinataireData = [
        'nom' => $data['nom_destinataire'] ?? $data['nom_destinataire_societe'] ?? '',
        'prenom' => $data['prenom_destinataire'] ?? $data['prenom_destinataire_societe'] ?? '',
        'email' => $data['email_destinataire'] ?? $data['email_destinataire_societe'] ?? '',
        'tel' => $destinataireTel,
        'agence' => $data['agence_destination_societe'] ?? $data['agence_particulier_destinataire'] ?? $data['agence_destination'] ?? '',
        'adresse' => $data['adresse_destinataire_societe'] ?? $data['adresse_destinataire'] ?? 'null',
    ];

    try {
        $expediteur = Expediteur::updateOrCreate(
            ['user_id' => $userId],
            $expediteurData
        );
        
        $destinataire = Destinataire::create($destinataireData);
    } catch (\Exception $e) {
        Log::error('Erreur création/màj Expediteur/Destinataire: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Erreur lors de la sauvegarde des informations.');
    }

    $colisEnregistres = [];
    $erreursCreation = [];
    
    if ($data['mode_transit'] === 'maritime') {
        $referenceColisPrincipale = $data['reference_colis_maritime'] ?? ('REF-MAR-' . strtoupper(uniqid()));
    } elseif ($data['mode_transit'] === 'aerien') {
        $referenceColisPrincipale = $data['reference_colis_aerien'] ?? ('REF-AER-' . strtoupper(uniqid()));
    } else {
        $referenceColisPrincipale = 'REF-' . strtoupper(uniqid());
    }
    
    $nombreTotalColisCrees = 0;
    preg_match('/\d+/', $referenceColisPrincipale, $matches);
    $id_reference = isset($matches[0]) ? (int)$matches[0] : null;

    foreach ($data['quantite_colis'] as $index => $quantite_pour_ligne_article) {
        $quantite_pour_ligne_article = (int)$quantite_pour_ligne_article;
        if ($quantite_pour_ligne_article <= 0) continue;

        $hauteur = $data['hauteur'][$index] ?? null;
        $largeur = $data['largeur'][$index] ?? null;
        $longueur = $data['longueur'][$index] ?? null;
        $dimension_result = (isset($hauteur, $largeur, $longueur)) ? "{$hauteur}x{$largeur}x{$longueur}" : null;

        $prixUnitairePourCetteLigne = $data['prix'][$index] ?? 0;

        for ($i = 1; $i <= $quantite_pour_ligne_article; $i++) {
            $colisItemData = [
                'devise' => $data['devise'] ?? null,
                'reference_colis' => $referenceColisPrincipale,
                'id_reference' => $id_reference,
                'reference_contenaire' => $data['reference_contenaire'] ?? null,
                'quantite_colis' => 1,
                'service' => $data['service'][$index] ?? null,
                'prix_transit_colis' => $prixUnitairePourCetteLigne,
                'poids_colis' => $data['poids_colis'][$index] ?? null,
                'mode_transit' => $data['mode_transit'] ?? null,
                'status' => $data['status'],
                'etat' => $data['etat'],
                'valeur_colis' => $data['valeur_colis'][$index] ?? null,
                'type_colis' => $data['type_colis'][$index] ?? null,
                'dimension_result' => $dimension_result,
                'description_colis' => $data['description_colis'][$index] ?? null,
                'expediteur_id' => $expediteur->id,
                'destinataire_id' => $destinataire->id,
                'qr_code_path' => null,
            ];

            try {
                // Création du colis d'abord
                $colisModel = Colis::create($colisItemData);
                
                // Génération du QR Code APRÈS la création du colis pour obtenir l'ID
                $qrData = [
                    'ID' => $colisModel->id,
                    'Ref' => $colisModel->reference_colis,
                    'Etat' => $colisModel->etat,
                    'Exp' => optional($expediteur)->nom,
                    'Dest' => optional($destinataire)->nom . '/' . optional($destinataire)->tel,
                    'Agence' => optional($destinataire)->agence,
                ];
                
                $qrCodeContent = implode("\n", array_map(fn ($k, $v) => "$k: $v", array_keys($qrData), array_values($qrData)));
                $qrCode = new QrCode($qrCodeContent);
                $writer = new PngWriter();
                $pngData = $writer->write($qrCode)->getString();
                
                $filePath = 'qrcodes/colis_id_' . $colisModel->id . '.png';
                $fullPath = public_path($filePath);
                $directory = dirname($fullPath);
                
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true, true);
                }
                
                File::put($fullPath, $pngData);
                
                // Mise à jour du colis avec le chemin du QR code
                $colisModel->update(['qr_code_path' => $filePath]);
                
                $colisEnregistres[] = $colisModel->fresh();
                $nombreTotalColisCrees++;
                
                // dd($colisModel);
            } catch (\Exception $e) {
                Log::error("Erreur création colis/paiement/QR pour index {$index}, item {$i}: " . $e->getMessage(), ['data' => $colisItemData, 'exception' => $e]);
                $erreursCreation[] = "Erreur lors de la création du colis (Réf: {$referenceColisPrincipale}, item {$i}).";
            }
        }
    }

    // Envoi de l'email de notification avec les détails du devis
    if (!empty($colisEnregistres)) {
        $recipientEmail = null;
        $agenceExpedition = $expediteurData['agence'] ?? null;

        if ($agenceExpedition === 'Agence de Chine') {
            $recipientEmail = 'douane@aft-app.com';
        } elseif ($agenceExpedition === 'AFT Agence Louis Bleriot') {
            $recipientEmail = 'entrepot.paris@aft-app.com';
        } elseif ($agenceExpedition === 'IPMS-SIMEX-CI Angre 8ème Tranche') {
            $recipientEmail = 'entrepot.abidjan@aft-app.com'; 
        }

        if (is_null($recipientEmail)) {
            $recipientEmail = config('mail.from.address');
            Log::warning('Aucune adresse e-mail spécifique trouvée pour l\'agence d\'expédition: ' . $agenceExpedition . '. Envoi à l\'adresse par défaut: ' . $recipientEmail);
        }

        $emailData = [
            'reference_colis_principale' => $referenceColisPrincipale,
            'expediteur' => $expediteurData,
            'destinataire' => $destinataireData,
            'nombre_colis' => $nombreTotalColisCrees,
            'premier_colis' => $colisEnregistres[0]->toArray() ?? null,
        ];

        try {
            Mail::to($recipientEmail)->send(new DevisCreatedMail($emailData));
            Log::info("✅ Email envoyé à {$recipientEmail} pour le devis {$referenceColisPrincipale}");
        } catch (\Throwable $e) {
            Log::error("❌ Erreur envoi mail Hostinger : " . $e->getMessage(), [
                'recipient' => $recipientEmail,
                'reference' => $referenceColisPrincipale,
            ]);
        }
    }

    return redirect()->route('customer_colis.hold')
            ->with('success', ' colis individuels ont été enregistrés avec succès.');
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

    public function hold(Request $request)
    {
        $email = auth()->user()->email;
        // Récupérer tous les colis liés à cet email
        $colis = Colis::where('email', $email);
        // dd( $colis);
        return view('customer.colis.hold');
    }

    public function history()
    {
        return view('customer.colis.history');
    }

    public function suivi()
    {
        return view('customer.colis.suivi');
    }

    public function facture(Request $request)
    {
        $userEmail = auth()->user()->email;

        $expediteurs = Expediteur::where('email', $userEmail)->get();

        if ($expediteurs->isNotEmpty()) {
            $expediteurIds = $expediteurs->pluck('id')->toArray();

            $invoices = Invoice::whereIn('expediteur_id', $expediteurIds)
                ->with(['expediteur', 'destinataire', 'agent'])
                ->get();

            $groupedInvoices = $invoices->groupBy('agent_id');

            $latestInvoices = [];

            foreach ($groupedInvoices as $invoiceNumber => $invoiceGroup) {
                $latestInvoice = $invoiceGroup->sortByDesc('created_at')->first();

                $latestInvoices[] = $latestInvoice;
            }

            return view('customer.invoice.index', ['invoices' => $latestInvoices]);
        } else {
            abort(404, "Aucun expéditeur trouvé avec l'email : " . $userEmail);
        }
    }

    public function invoice(Request $request)
    {
        $id = $request->id;
        $invoice = Invoice::findOrFail($id);
        $userId = $invoice->expediteur_id;
    
        $expediteur = Expediteur::findOrFail($userId);
        $expId = $expediteur->id;

        $colis = Colis::where('expediteur_id', $expId)
                     ->orderBy('created_at', 'desc')
                     ->first();

        $reference_colis = $colis->reference_colis;

        $colisCollection = Colis::where('reference_colis', $reference_colis)
                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table expediteurs
                ->whereIn('colis.etat', ['Validé', 'Dechargé'])  // Filtre sur l'état des colis
                ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')  // Filtre sur l'agence de l'expéditeur
                ->get();  // Récupérer les résultats de la requête

            // Vérifier si la collection est vide et renvoyer un message d'erreur
            if ($colisCollection->isEmpty()) {
                return redirect()->back()->with('error', 'Aucun colis trouvé avec cette référence.');
            }

        // Récupération du premier colis
        $firstColis = $colisCollection->first();
        
        $date_facture = now();
        $expediteur = $firstColis->expediteur->nom . ' ' . $firstColis->expediteur->prenom;
        $tel_expediteur = $firstColis->expediteur->tel;
        $destinataire = $firstColis->destinataire->nom . ' ' . $firstColis->destinataire->prenom;
        $numero_facture = '00' . str_pad($firstColis->id, 3, '0', STR_PAD_LEFT);


        // Calcul du prix total
        $prix_total = 0;
        foreach ($colisCollection as $colis) {
            if (!isset($colis->prix_transit_colis)) {
                throw new \Exception("Le champ prix_transit_colis est manquant pour un colis.");
            }
            $prix_total += $colis->prix_transit_colis;
        }

        // Utilisation de optional() pour éviter les erreurs si la relation paiement est nulle
        $mode_payement = optional($firstColis->paiement)->mode_de_paiement ?? 'N/A';
        $montant_paye = optional($firstColis->paiement)->montant_reçu ?? 0;
        $reste = $prix_total - $montant_paye;

        $id_agent = Auth::user()->id;
        $nom_agent = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        
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

        // dd($colisData);
        // Passage des données à la vue
        return view('customer.invoice.invoice', compact(
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
        ));
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

    // function pour le payement
    public function step_payement(Request $request, $id)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
    
        $validatedData = $request->validate([
            'mode_payement'            => 'required|string|in:bank,mobile_money,cheque,cash,paiement_enlevement,paiement_livraison',
            'numero_compte'           => 'nullable|string',
            'nom_banque'              => 'nullable|string',
            'transaction_id'          => 'nullable|string',
            'numero_tel'              => 'nullable|string',
            'operateur_mobile'        => 'nullable|string',
            'numero_cheque'           => 'nullable|string',
            'montant_reçu'            => 'nullable|numeric',
            'cinetpay_transaction_id' => 'nullable|string',
        ]);
    
        $colis = Colis::findOrFail($id);
    
        // Récupérer tous les colis ayant la même référence et le même expéditeur
        $colisWithSameReference = Colis::where('reference_colis', $colis->reference_colis)
                                       ->where('expediteur_id', $colis->expediteur_id)
                                       ->get();
    
        // Montant total pour tous les colis de la même référence
        $totalAmount = $colisWithSameReference->sum('prix_transit_colis');
    
        $isPartiallyPaid = in_array($validatedData['mode_payement'], ['paiement_enlevement', 'paiement_livraison']);
        $statusPaiement  = $isPartiallyPaid ? 'non payé' : 'Payé';
        $statusColis     = $isPartiallyPaid ? 'non payé' : 'payé';
    
        // ✅ Ajout de 'colis_id' pour lier le paiement au colis concerné
        $paiementData = [
            'colis_id'        => $colis->id,
            'methode_paiement'=> $validatedData['mode_payement'],
            'expediteur_id'   => $colis->expediteur_id,
            'date_validation' => now(),
            'statut_paiement' => $statusPaiement,
            'agent_id'        => null,
            'montant'         => $totalAmount,                 // Montant total
            'montant_paye'    => $isPartiallyPaid ? 0 : $totalAmount,
        ];
    
        // Gestion des différents modes de paiement
        if ($validatedData['mode_payement'] === 'bank') {
            $paiementData['banque']         = $validatedData['nom_banque'] ?? null;
            $paiementData['NumeroPaiement'] = $validatedData['numero_compte'] ?? null;
            $paiementData['id_transaction'] = $validatedData['transaction_id'] ?? null;
        } elseif ($validatedData['mode_payement'] === 'mobile_money') {
            $paiementData['operateur']      = $validatedData['operateur_mobile'] ?? null;
            $paiementData['NumeroPaiement'] = $validatedData['numero_tel'] ?? null;
            $paiementData['id_transaction'] = $validatedData['cinetpay_transaction_id']
                                              ?? $validatedData['transaction_id']
                                              ?? null;
        } elseif ($validatedData['mode_payement'] === 'cheque') {
            $paiementData['banque']         = $validatedData['nom_banque'] ?? null;
            $paiementData['NumeroPaiement'] = $validatedData['numero_cheque'] ?? null;
        }
    
        // Vérifier s'il existe déjà un paiement pour cette référence
        $existingPaiement = null;
        foreach ($colisWithSameReference as $colisItem) {
            if ($colisItem->paiement_id) {
                $existingPaiement = Paiement::find($colisItem->paiement_id);
                break;
            }
        }
    
        if ($existingPaiement) {
            // Mise à jour du paiement existant
            $existingPaiement->update($paiementData);
            $paiement = $existingPaiement;
        } else {
            // Création d’un nouveau paiement
            $paiement = Paiement::create($paiementData);
        }
    
        // Mise à jour de tous les colis liés à la même référence
        foreach ($colisWithSameReference as $colisItem) {
            $colisItem->status      = $statusColis;
            $colisItem->etat        = 'Validé';
            $colisItem->paiement_id = $paiement->id;
            $colisItem->save();
    
            // Envoi de l'email de confirmation de paiement
            try {
                \Mail::to($colisItem->expediteur->email)
                    ->send(new \App\Mail\PaymentConfirmedMail($colisItem, $paiementData));
            } catch (\Exception $e) {
                Log::error(
                    'Erreur lors de l\'envoi de l\'email de confirmation de paiement pour le colis '
                    . $colisItem->id . ': ' . $e->getMessage()
                );
            }
        }
    
        $modeDePaiementTexte = ucfirst(str_replace('_', ' ', $validatedData['mode_payement']));
        $message = $isPartiallyPaid
            ? "La demande de \"{$modeDePaiementTexte}\" a bien été enregistrée !"
            : 'Paiement enregistré avec succès.';
    
        return response()->json([
            'redirect' => route('customer_colis.history'),
            'message'  => $message
        ]);
    }
    
    public function edit_payement($id)
    {
        // Récupérer le colis par son ID
        $colis = Colis::findOrFail($id);

        return view('customer.colis.edit_payement', compact('colis'));
    }
    public function get_colis(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
    
        $email = auth()->user()->email;
    
        // Construction de la requête de base
        $query = Colis::select(
                'colis.reference_colis',
                DB::raw('COUNT(colis.id) as nombre_colis'),
                'expediteurs.agence as expediteur_agence',
                DB::raw('CONCAT(destinataires.nom, " ", destinataires.prenom) as destinataire_nom_complet'),
                'destinataires.tel as destinataire_tel',
                'destinataires.agence as destinataire_agence',
                DB::raw('MAX(colis.updated_at) as last_updated_at')
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('expediteurs.email', $email)
            ->where('colis.etat', 'En attente')
            ->groupBy(
                'colis.reference_colis',
                'expediteurs.agence',
                'destinataires.nom',
                'destinataires.prenom',
                'destinataires.tel',
                'destinataires.agence'
            );
    
        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $deleteUrl = route('customer_colis.delete', ['reference_colis' => $row->reference_colis]);
                return '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-danger btn-delete-group" data-reference="' . $row->reference_colis . '" title="Supprimer le groupe">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';
            })
            ->editColumn('last_updated_at', function ($row) {
                if ($row->last_updated_at) {
                    return \Carbon\Carbon::parse($row->last_updated_at)->format('d/m/Y H:i');
                }
                return '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function delete_colis_group(Request $request, $reference_colis)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }

        try {
            $email = auth()->user()->email;

            $count = Colis::where('reference_colis', $reference_colis)
                ->whereHas('expediteur', function ($query) use ($email) {
                    $query->where('email', $email);
                })
                ->where('etat', 'En attente')
                ->count();

            if ($count > 0) {
                Colis::where('reference_colis', $reference_colis)
                    ->whereHas('expediteur', function ($query) use ($email) {
                        $query->where('email', $email);
                    })
                    ->where('etat', 'En attente')
                    ->delete();
                return response()->json(['success' => 'Groupe de colis supprimé avec succès.']);
            } else {
                return response()->json(['error' => 'Aucun colis trouvé pour cette référence ou vous n\'avez pas la permission.'], 403);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression du groupe de colis.'], 500);
        }
    }


    // colis valider
    public function get_colis_valide(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
    
        $email = Auth::user()->email;
    
        // Désactiver temporairement le mode ONLY_FULL_GROUP_BY
        DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""))');
    
        $query = Colis::query()
            ->select(
                'colis.reference_colis',
                DB::raw('COUNT(colis.id) as nombre_colis_par_reference'),
                DB::raw('SUM(colis.prix_transit_colis) as total_prix_devis'),
                DB::raw('MAX(colis.updated_at) as last_updated_at'),
                DB::raw('MIN(colis.id) as representative_colis_id'),
                'expediteurs.agence as expediteur_agence',
                'destinataires.nom as destinataire_nom',
                'destinataires.prenom as destinataire_prenom',
                'destinataires.agence as destinataire_agence',
                'destinataires.tel as destinataire_contact',
                'colis.etat',
                'colis.status'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->leftJoin('paiements', 'colis.id', '=', 'paiements.colis_id')
            ->where('expediteurs.email', $email)
            ->where('colis.etat', 'validé')
            // Exclure les colis complètement payés
            ->where(function($query) {
                $query->where('colis.status', '!=', 'payé')
                      ->orWhereNull('colis.status');
            })
            ->where(function($query) {
                $query->where('paiements.statut_paiement', '!=', 'payé')
                      ->orWhereNull('paiements.statut_paiement');
            })
            ->groupBy('colis.reference_colis');
    
        return DataTables::of($query)
            ->addColumn('etat_display', function ($row) {
                return 'validé';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('customer_colis.payement.edit', ['id' => $row->representative_colis_id]);
                
                // Bouton toujours vert comme demandé
                return '
                    <div class="btn-group">
                        <a href="' . $editUrl . '" class="btn btn-sm btn-success" title="Payer le devis">
                            <i class="fas fa-hand-holding-usd"></i>
                        </a>
                    </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function get_colis_suivi(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }

        if (!Auth::check()) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
        $email = Auth::user()->email;

        $query = Colis::query()
            ->select(
                'colis.reference_colis',
                DB::raw('COUNT(colis.id) as nombre_colis_par_reference'),
                'expediteurs.agence as expediteur_agence',   
                'destinataires.nom as destinataire_nom',   
                'destinataires.prenom as destinataire_prenom', 
                'destinataires.tel as destinataire_tel',              
                'destinataires.agence as destinataire_agence',      
                'colis.etat',                                       
                DB::raw('MAX(colis.updated_at) as last_updated_at') 
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('expediteurs.email', $email)
            ->whereIn('colis.etat', ['En transit', 'Validé', 'En entrepot', 'Dechargé', 'Chargé', 'Fermé', 'Livré'])
            ->groupBy(
                'colis.reference_colis',
                'expediteurs.agence',
                'destinataires.nom',
                'destinataires.prenom',
                'destinataires.tel',
                'destinataires.agence',
                'colis.etat' 
            );

        return DataTables::of($query)
            ->addColumn('destinataire_complet', function ($row) {
                return ($row->destinataire_nom ?? '') . ' ' . ($row->destinataire_prenom ?? '');
            })
            ->editColumn('etat', function ($row) {
                switch ($row->etat) {
                    case 'Fermé':
                        return 'Colis en transit';
                    case 'Dechargé':
                        return 'Colis arrivé';
                    case 'Livré':
                        return 'Colis livré';
                    case 'En transit':
                        return 'En transit';
                    case 'Validé':
                        return 'Validé';
                    case 'En entrepot':
                        return 'En entrepôt';
                    case 'Chargé':
                        return 'Chargé';
                    default:
                        return $row->etat; 
                }
            })
            
            ->rawColumns([])
            ->make(true);
    }

    // facture
    public function get_facture(Request $request)
    {
        // Récupération de l'email de l'utilisateur connecté
        $email = auth()->user()->email;

        if ($request->ajax()) {
            // Récupérer les colis associés à l'email et à l'état "en attente"
            $colis = Les_colis::select(
                'nom_expediteur',
                'prenom_expediteur',
                'email_expediteur',
                'agence_expedition',
                'agence_destination',
                'status',
                'etat'
            )
            ->where('email_expediteur', $email) // Comparer l'email
            ->where('etat', 'Validé') // Vérifier que l'état est "en attente"
            ->get();

            // Construire et retourner la DataTable
            return DataTables::of($colis)
                // Ajouter une colonne d'action
                ->addColumn('action', function ($row) {
                    $id = auth()->user()->getIdUSer();
                    $pdfUrl = route('customer_colis.facture.pdf', ['id' => $id]); // URL pour le téléchargement PDF
                
                    return '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="fas fa-edit"></i> 
                            </button>
                            <a href="' . $pdfUrl . '" class="btn btn-sm btn-primary" target="_blank">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    ';
                })            
                // Permettre le rendu des colonnes contenant du HTML
                ->rawColumns(['action'])
                ->make(true);
        }
        // Retourner une réponse d'erreur si la requête n'est pas AJAX
        return response()->json(['message' => 'Requête non valide'], 400);
    }


    public function devis_hold()
    {
        return view('customer.devis.hold');
    }

    public function liste_contenaire()
    {
        return view('customer.devis.liste_contenaire');
    }


    public function telechargerPdf(Request $request )
    {
        $id = $request->id;
        // Récupérer les données du colis par son ID
        $colis = Les_colis::findOrFail($id);
        dd($colis);

        // Préparer les données pour le PDF
        $data = [
            'nom_expediteur' => $colis->nom_expediteur,
            'prenom_expediteur' => $colis->prenom_expediteur,
            'email_expediteur' => $colis->email_expediteur,
            'agence_expedition' => $colis->agence_expedition,
            'agence_destination' => $colis->agence_destination,
            'status' => $colis->status,
            'etat' => $colis->etat,
            'date' => now()->format('d/m/Y'),
        ];

        // Générer le PDF avec DomPDF
        $pdf = PDF::loadView('customer.facture.facture_template', $data)->setPaper('a3', 'portrait');

        // Télécharger le PDF
        return $pdf->download('facture_' . $id . '.pdf');
    }
}