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
    private function generateReferenceColis()
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        // dd($user);
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            throw new \Exception("Utilisateur non connecté.");
        }
    
        // Récupérer la première lettre du nom et du prénom
        $firstLetterNom = strtoupper(substr($user->last_name, 0, 1)); // Première lettre du nom
        $firstLetterPrenom = strtoupper(substr($user->first_name, 0, 1)); // Première lettre du prénom
        // dd($firstLetterNom, $firstLetterPrenom);
        // Récupérer la première lettre du mois actuel
        $monthLetter = strtoupper(now()->format('F')[0]); // Première lettre du mois
    
        // Initialiser le chiffre à 1
        $increment = 1;
    
        // Construire la référence de base
        $baseReference = "{$firstLetterNom}{$firstLetterPrenom}-{$monthLetter}-{$increment}";
    
        // Vérifier si la référence existe déjà dans la table colis
        while (DB::table('colis')->where('reference_colis', $baseReference)->exists()) {
            // Incrémenter le chiffre
            $increment++;
            // Mettre à jour la référence avec le nouvel incrément
            $baseReference = "{$firstLetterNom}{$firstLetterPrenom}-{$monthLetter}-{$increment}";
        }
    
        return $baseReference; // Retourner la référence finale
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
            ->where('etat', '!=', 'Fermé')
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
        $id = auth()->user()->getIdUSer();

        $user = User::findOrfail($id);
      
        $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')
                            ->distinct()
                            ->pluck('pays_agence');


        $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
        $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();

        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();
        // $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
        $referenceColis = $this->generateReferenceColisComplet();
        return view('customer.colis.add_colis', compact('agences','agencesExpedition','agencesDestination','paysUniques','referenceColis', 'client_expediteurs', 'client_destinataires','user'));
    }


    public function end_colis(Request $request)
    {
        $id = auth()->user()->getIdUSer();
        // dd($id);
        $user = User::findOrfail($id);
        // Chargement des données
        // dd($user);
        $agences = Agence::select('nom_agence', 'id')->get();
        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        $referenceColis = $this->generateReferenceColisComplet();

        return view('customer.colis.fin_colis', compact('agences','referenceColis', 'client_expediteurs', 'client_destinataires','user'));
    }

    public function store_colis(Request $request)
    {
        $data = $request->all();

        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'En attente';

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

        try {
            $expediteur = Expediteur::create($expediteurData);
            $destinataire = Destinataire::create($destinataireData);
        } catch (\Exception $e) {
            Log::error('Erreur création Expediteur/Destinataire: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde des informations expéditeur/destinataire.');
        }

        $colisEnregistres = [];
        $erreursCreation = [];
        $referenceColisPrincipale = $data['reference_colis'] ?? ('REF-' . strtoupper(uniqid()));

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
                    'reference_contenaire' => $data['reference_contenaire'] ?? null,
                    'quantite_colis' => 1,
                    'service' => $data['service'][$index] ?? null,
                    'prix_transit_colis' => $prixUnitairePourCetteLigne,
                    'poids_colis' => $data['poids_colis'][$index] ?? null,
                    'mode_transit' => $data['mode_transit'] ?? null,
                    'status' => $data['status'], 
                    'etat' => $data['etat'],
                    'type_colis' => $data['type_colis'][$index] ?? null,
                    'dimension_result' => $dimension_result,
                    'description_colis' => $data['description_colis'][$index] ?? null,
                    'expediteur_id' => $expediteur->id,
                    'destinataire_id' => $destinataire->id,
                    // 'agent_id' => $agentId,
                    'qr_code_path' => null,
                ];
                try {
                    $colisModel = Colis::create($colisItemData);

                } catch (\Exception $e) {
                    Log::error("Erreur création colis/paiement/QR pour index {$index}, item {$i}: " . $e->getMessage(), ['data' => $colisItemData, 'exception' => $e]);
                    $erreursCreation[] = "Erreur lors de la création du colis (Réf: {$referenceColisPrincipale}, item {$i}).";
                }
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
        // Vérifier que la requête est bien une requête AJAX
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }

        // Valider les données du formulaire (adapté à tous les types de paiement)
        $validatedData = $request->validate([
            'mode_payement'         => 'required|string',
            'numero_compte'        => 'nullable|string',
            'nom_banque'           => 'nullable|string',
            'transaction_id'       => 'nullable|string',
            'numero_tel'           => 'nullable|string',
            'operateur_mobile'     => 'nullable|string',
            'numero_cheque'        => 'nullable|string',
            'montant_reçu'         => 'nullable|numeric', // Pour paiement en espèces
            'cinetpay_transaction_id' => 'nullable|string', // Pour CinetPay
        ]);

        // Récupérer le colis en utilisant le paramètre $id
        $colis = Colis::findOrFail($id);

        // Préparer les données pour la table paiements
        $paiementData = [
            'colis_id'          => $id,
            'methode_paiement'  => $validatedData['mode_payement'],
            'expediteur_id'     => $colis->expediteur_id, // Récupérer expediteur_id du colis
            'date_validation'   => now(), // Date de validation du paiement
            'statut_paiement'   => 'Payé', // Statut de paiement mis à 'Payé'
            'agent_id'          => null, // Vous pouvez récupérer l'agent connecté si nécessaire Auth::user()->id
        ];

        // Remplir les champs spécifiques en fonction du mode de paiement
        if ($validatedData['mode_payement'] === 'bank') {
            $paiementData['banque']         = $validatedData['nom_banque'] ?? null;
            $paiementData['NumeroPaiement'] = $validatedData['numero_compte'] ?? null;
            $paiementData['id_transaction'] = $validatedData['transaction_id'] ?? null;
        } elseif ($validatedData['mode_payement'] === 'mobile_money') {
            $paiementData['operateur']      = $validatedData['operateur_mobile'] ?? null;
            $paiementData['NumeroPaiement'] = $validatedData['numero_tel'] ?? null;
            // Priorité à la transaction CinetPay si elle existe, sinon transaction ID classique
            $paiementData['id_transaction'] = $validatedData['cinetpay_transaction_id'] ?? $validatedData['transaction_id'] ?? null;
        } elseif ($validatedData['mode_payement'] === 'cheque') {
            $paiementData['banque']         = $validatedData['nom_banque'] ?? null;
            $paiementData['NumeroPaiement'] = $validatedData['numero_cheque'] ?? null;
        } elseif ($validatedData['mode_payement'] === 'cash') {
            $paiementData['montant']        = $validatedData['montant_reçu'] ?? null; // Enregistrer le montant reçu pour les espèces
        }

        // Enregistrer le montant du colis dans la table paiement
        $paiementData['montant'] = $colis->prix_transit_colis;


        // Vérifier si un paiement a déjà été effectué pour ce colis (optionnel, selon votre logique)
        $existingPayment = Paiement::where('colis_id', $id)->first();
        if ($existingPayment) {
            return response()->json(['message' => 'Le paiement a déjà été effectué pour ce colis.'], 400);
        }

        // Créer le paiement
        Paiement::create($paiementData);

        // Mettre à jour le champ 'etat' du colis en le marquant comme "Validé"
        $colis->etat = 'Validé';
        $colis->save();

         // Envoyer l'email de confirmation de paiement
         try {
            \Mail::to($colis->expediteur->email)->send(new \App\Mail\PaymentConfirmedMail($colis, $paiementData));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de confirmation de paiement pour le colis ' . $colis->id . ': ' . $e->getMessage());
            // Log l'erreur, mais ne bloque pas le processus principal
        }

        return response()->json([
            'redirect' => route('customer_colis.history')
        ]);
    }

    //    // function pour le payement
    //    public function step_payement(Request $request, $id)
    //    {
    //        // Vérifier que la requête est bien une requête AJAX
    //        if (!$request->ajax()) {
    //            return response()->json(['message' => 'Requête non valide'], 400);
    //        }
   
    //        // Valider les données du formulaire (adapté à tous les types de paiement)
    //        $validatedData = $request->validate([
    //            'mode_payement'         => 'required|string',
    //            'numero_compte'        => 'nullable|string',
    //            'nom_banque'           => 'nullable|string',
    //            'transaction_id'       => 'nullable|string',
    //            'numero_tel'           => 'nullable|string',
    //            'operateur_mobile'     => 'nullable|string',
    //            'numero_cheque'        => 'nullable|string',
    //            'montant_reçu'         => 'nullable|numeric', // Pour paiement en espèces
    //            'cinetpay_transaction_id' => 'nullable|string', // Pour CinetPay
    //        ]);
   
    //        // Récupérer le colis en utilisant le paramètre $id
    //        $colis = Colis::findOrFail($id);
   
    //        // Préparer les données pour la table paiements
    //        $paiementData = [
    //            'colis_id'          => $id,
    //            'methode_paiement'  => $validatedData['mode_payement'],
    //            'expediteur_id'     => $colis->expediteur_id, // Récupérer expediteur_id du colis
    //            'date_validation'   => now(), // Date de validation du paiement
    //            'statut_paiement'   => 'Payé', // Statut de paiement mis à 'Payé'
    //            // **Récupérer l'agent_id du colis et l'assigner au paiement**
    //            'agent_id'          => $colis->agent_id, // Récupérer agent_id du colis
    //        ];
   
    //        // Remplir les champs spécifiques en fonction du mode de paiement
    //        if ($validatedData['mode_payement'] === 'bank') {
    //            $paiementData['banque']         = $validatedData['nom_banque'] ?? null;
    //            $paiementData['NumeroPaiement'] = $validatedData['numero_compte'] ?? null;
    //            $paiementData['id_transaction'] = $validatedData['transaction_id'] ?? null;
    //        } elseif ($validatedData['mode_payement'] === 'mobile_money') {
    //            $paiementData['operateur']      = $validatedData['operateur_mobile'] ?? null;
    //            $paiementData['NumeroPaiement'] = $validatedData['numero_tel'] ?? null;
    //            // Priorité à la transaction CinetPay si elle existe, sinon transaction ID classique
    //            $paiementData['id_transaction'] = $validatedData['cinetpay_transaction_id'] ?? $validatedData['transaction_id'] ?? null;
    //        } elseif ($validatedData['mode_payement'] === 'cheque') {
    //            $paiementData['banque']         = $validatedData['nom_banque'] ?? null;
    //            $paiementData['NumeroPaiement'] = $validatedData['numero_cheque'] ?? null;
    //        } elseif ($validatedData['mode_payement'] === 'cash') {
    //            $paiementData['montant']        = $validatedData['montant_reçu'] ?? null; // Enregistrer le montant reçu pour les espèces
    //        }
   
    //        // Enregistrer le montant du colis dans la table paiement
    //        $paiementData['montant'] = $colis->prix_transit_colis;
   
   
    //        // Vérifier si un paiement a déjà été effectué pour ce colis (optionnel, selon votre logique)
    //        $existingPayment = Paiement::where('colis_id', $id)->first();
    //        if ($existingPayment) {
    //            return response()->json(['message' => 'Le paiement a déjà été effectué pour ce colis.'], 400);
    //        }
   
    //        // Créer le paiement
    //        Paiement::create($paiementData);
   
    //        // Mettre à jour le champ 'etat' du colis en le marquant comme "Validé"
    //        $colis->etat = 'Validé';
    //        $colis->save();
   
    //         // Envoyer l'email de confirmation de paiement
    //         try {
    //            \Mail::to($colis->expediteur->email)->send(new \App\Mail\PaymentConfirmedMail($colis, $paiementData));
    //        } catch (\Exception $e) {
    //            Log::error('Erreur lors de l\'envoi de l\'email de confirmation de paiement pour le colis ' . $colis->id . ': ' . $e->getMessage());
    //            // Log l'erreur, mais ne bloque pas le processus principal
    //        }
   
    //        return response()->json(['message' => 'Paiement enregistré avec succès et colis marqué comme validé !']);
    //        return redirect()->route('customer_colis.index'); // Redirection à ajuster si nécessaire
    //    }
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

        $colis_grouped = Colis::select(
            'colis.reference_colis',
            DB::raw('COUNT(colis.id) as nombre_colis'),
            DB::raw('MIN(expediteurs.agence) as expediteur_agence'),
            DB::raw('MIN(CONCAT(destinataires.nom, " ", destinataires.prenom)) as destinataire_nom_complet'),
            DB::raw('MIN(destinataires.tel) as destinataire_tel'),
            DB::raw('MIN(destinataires.agence) as destinataire_agence'),
            DB::raw('MAX(colis.updated_at) as last_updated_at') 
        )
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
        ->where('expediteurs.email', $email)
        ->where('colis.etat', 'En attente')
        ->groupBy('colis.reference_colis')
        ->get();

        return DataTables::of($colis_grouped)
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
                'colis.etat'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('expediteurs.email', $email)
            ->where('colis.etat', 'Devis')
            ->groupBy(
                'colis.reference_colis',
                'expediteurs.agence',
                'destinataires.nom',
                'destinataires.prenom',
                'destinataires.agence',
                'destinataires.tel',
                'colis.etat'
            );

        return DataTables::of($query)
            ->addColumn('etat_display', function ($row) {
                return 'Devis validé';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('customer_colis.payement.edit', ['id' => $row->representative_colis_id]);
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