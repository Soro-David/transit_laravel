<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
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
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


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
        // Exemple : "COLIS-12202423-XXXXXX"
        return 'COLIS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }


    public function storeStep1(Request $request)
    {
        
        // Validation des données
        $request->validate([
            // Ajouter les règles de validation si nécessaires
        ]);

        // Stockage des données en session
        session(['step1' => $request->only([
            'nom_expediteur', 'prenom_expediteur', 'email_expediteur', 'tel_expediteur',
            'agence_expedition', 'adresse_expediteur', 'nom_destinataire', 'prenom_destinataire',
            'email_destinataire', 'tel_destinataire', 'agence_destination', 'adresse_destinataire',
        ])]);

        return redirect()->route('customer_colis.create.step2');
    }

    /**
     * Étape 2 : Détails du colis.
     */
    public function createStep2()
    {
        return view('customer.colis.add.step2');
    }

    public function storeStep2(Request $request)
    {

        $request->validate([
            'quantite_colis' => 'required',
            'type_embalage' => 'required',
            'dimension_colis' => 'required',
            'description_colis' => 'required',
            'poids_colis' => 'required',
            'valeur_colis' => 'required',
        ]);
    
        // Ajout du champ 'etat' avec la valeur 'En attente'
        session(['step2' => array_merge(
            $request->only([
                'quantite_colis',
                'type_embalage', 
                'dimension_colis', 
                'description_colis', 
                'poids_colis', 
                'valeur_colis',
            ]),
            // ['etat' => 'En attente'] // Ajout du champ 'etat'
        )]);
    

        return redirect()->route('customer_colis.create.step3');
    }
    /**
     * Étape 3 : Informations de transit.
     */
    public function createStep3()
    {
        return view('customer.colis.add.step3');
    }

    public function storeStep3(Request $request)
    {

        $request->validate([
            'articles.*.description' => 'required|string|max:255',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.dimension' => 'required|string|max:255',
            'articles.*.poids' => 'required|numeric|min:0',
        ]);
        session(['step3' => $request->only([
            'description', 
            'quantite', 
            'dimension', 
            'poids'
            ])]);
    
        return redirect()->route('customer_colis.create.step4');
    }

    public function createStep4(Request $request)
    {
        $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
        // dd($referenceColis);
        return view('customer.colis.add.step4',['referenceColis' => $referenceColis]);
    }

    public function storeStep4(Request $request)
    {

       
        $request->validate([
            'mode_transit' => 'required',
            'reference_colis' => 'required',
        ]);
        session(['step4' => $request->only([
            'mode_transit', 'reference_colis'
        ])]);
        return redirect()->route('customer_colis.complete');
    }
    
    public function complete(Request $request)
    {
        $data = array_merge(
            session('step1', []),
            session('step2', []),
            session('step3', []),
            session('step4', []) 
        );
        // Ajouter le statut au tableau de données
        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'En attente';
        // dd($data);


        // Vérifiez que les données sont bien réparties pour chaque table
        $expediteurData = [
            'nom' => $data['nom_expediteur'] ,
            'prenom' => $data['prenom_expediteur'] ,
            'email' => $data['email_expediteur'] ,
            'tel' => $data['tel_expediteur'] ,
            'agence' => $data['agence_expedition'] ,
            'adresse' => $data['adresse_expediteur'] ,
        ];


        $destinataireData = [
            'nom' => $data['nom_destinataire'] ,
            'prenom' => $data['prenom_destinataire'] ,
            'email' => $data['email_destinataire'] ,
            'tel' => $data['tel_destinataire'] ,
            'agence' => $data['agence_destination'] ,
            'adresse' => $data['adresse_destinataire'] ,
        ];
        // dd($data);

        // Vérification que les tableaux nécessaires existent et sont synchronisés
    if (
        isset($data['description'], $data['quantite'], $data['dimension'], $data['poids']) &&
        is_array($data['description']) &&
        is_array($data['quantite']) &&
        is_array($data['dimension']) &&
        is_array($data['poids']) &&
        count($data['description']) === count($data['quantite']) &&
        count($data['quantite']) === count($data['dimension']) &&
        count($data['dimension']) === count($data['poids'])
    ) {
        // Construction du tableau d'articles
        $articleData = [];
        foreach ($data['description'] as $index => $description) {
            $articleData[] = [
                'description' => $description,
                'quantite' => $data['quantite'][$index],
                'dimension' => $data['dimension'][$index],
                'poids' => $data['poids'][$index],
            ];
        }
    

        // Afficher ou traiter $articleData
        // dd($articleData); // Affiche les données sous forme de tableau structuré
    } else {
        // Gestion d'erreur
        throw new \Exception('Les données des articles sont manquantes ou incohérentes.');
    }

        $colisData = [
            'reference_colis' => $data['reference_colis'] ,
            'reference_contenaire' => $data['reference_contenaire'] ?? null,
            'quantite_colis' => $data['quantite_colis'] ,
            'type_embalage' => $data['type_embalage'] ,
            'valeur_colis' => $data['valeur_colis'],
            'poids_colis' => $data['poids_colis'] ,
            'dimension_colis' => $data['dimension_colis'] ,
            'mode_transit' => $data['mode_transit'] ,
            'status' => $data['status'],
            'etat' => $data['etat'],
            // 'client_id' => $data['etat'],
            // 'client_id' => auth()->user()->getIdUSer(),
        ];
        // $payementData = [
        //     'mode_de_payement' => $data['mode_payement'] ,
        //     'montant_reçu' => $data['montant_reçu'] ,
        //     'operateur_mobile' => $data['operateur_mobile'] ,
        //     'numero_compte' => $data['numero_compte'],
        //     'nom_banque' => $data['nom_banque'] ,
        //     'id_transaction' => $data['transaction_id'],
        //     'numero_tel' => $data['numero_tel'],
        //     'numero_cheque' => $data['numero_cheque'] ,
        // ];

        // dd($expediteurData, $destinataireData, $articleData, $colisData, $payementData);

// dd(auth()->user()->getIdUSer());
        // Insérer les données dans chaque table
        $expediteur = Expediteur::create($expediteurData);
        $destinataire = Destinataire::create($destinataireData);
        // $article = Article::create($articleData);
        // $payement = Paiement::create($payementData);
        $colis = Colis::create(array_merge($colisData, [
            'expediteur_id' => $expediteur->id,
            'destinataire_id' => $destinataire->id,
            // 'client_id' => auth()->user()->getIdUSer(),

            // 'paement_id' => $payement->id,
        ]));
        // dd($colis);
        foreach ($articleData as $article) {
            $article['colis_id'] = $colis->id;
            Article::create($article);
        }
        // Nettoyer les sessions
        session()->forget(['step1', 'step2', 'step3', 'step4']);
        return view('customer.colis.add.complete',compact('colis','data'));
    }
    /**
     * Enregistre les informations de paiement.
     */
    // public function stepPayement()
    // {
    //     return view('customer.colis.add.payement');
    // }
    // public function storePayement(Request $request)
    // {
    //     $request->validate([
    //         'mode_payement' => 'required|in:bank,mobile_money,cheque,cash',
    //         // Validation pour le paiement bancaire
    //         'numero_compte' => 'required_if:mode_payement,bank|max:255',
    //         'nom_banque' => 'required_if:mode_payement,bank|max:255',
    //         'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
    //         // Validation pour Mobile Money
    //         'tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
    //         'operateur' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
    //         // Validation pour le paiement par chèque
    //         'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
    //         'nom_banque' => 'required_if:mode_payement,cheque|max:255',
    //         // Validation pour le paiement en espèces
    //         'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:1',
    //     ]);
    //     session(['step4' => $request->only([
    //         'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
    //         'tel', 'operateur', 'numero_cheque', 'montant_reçu',
    //     ])]);
    //     return redirect()->route('customer_colis.create.qrcode');
    // }
    // public function qrcode(Request $request)
    // {
    //     $data = array_merge(
    //         session('step1', []),
    //         session('step2', []),
    //         session('step3', []),
    //         session('step4', []) 
    //     );
    //     $data['status'] = $data['mode_payement'] ?? 'non payé';
    //     $colis = Les_colis::create($data);
    //     // Générer le contenu du QR code (par exemple : ID du colis + statut)
    //     // Nettoyer les sessions
    //     session()->forget(['step1', 'step2', 'step3', 'step4']);
    //     // return redirect()->route('colis.complete');
    //     return view('customer.colis.add.complete',compact('colis','data'));
    // }
    
    /**
     * Étape finale : Confirmation.
     */
    // public function complete()
    // {

    //     return view('customer.colis.add.complete');
    // }

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
        $colis = Les_colis::where('email', $email);
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
    public function facture()
    {
        return view('customer.facture.invoice1');
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
        // Vérifiez que la requête est AJAX
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }

        // Validez les données du formulaire
        $validatedData = $request->validate([
            'mode_de_paiement' => 'required|string',
            'numero_compte' => 'nullable|string',
            'nom_banque' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'numero_tel' => 'nullable|string',
            'operateur_mobile' => 'nullable|string',
            'numero_cheque' => 'nullable|string',
            'colis_id' => 'required|exists:colis,id', // Assurez-vous que colis_id existe dans la table colis
        ]);

        // Vérifiez si un paiement a déjà été effectué pour ce colis
        $existingPayment = Paiement::where('colis_id', $validatedData['colis_id'])->first();
        if ($existingPayment) {
            return response()->json(['message' => 'Le paiement a déjà été effectué pour ce colis.'], 400);
        }

        // Enregistrez le paiement dans la base de données
        Paiement::create($validatedData);

        // Mettez à jour le champ 'etat' du colis à 'Validé'
        $colis = Colis::findOrFail($validatedData['colis_id']); // Récupérez le colis par son ID
        $colis->etat = 'Validé'; // Modifiez le champ 'etat'
        $colis->save(); // Enregistrez les modifications

        return response()->json(['message' => 'Paiement enregistré avec succès et colis marqué comme validé !']);
    }


    public function edit_payement($id)
    {
        // dd($id);
        // Récupérer tous les programmes pour le chauffeur avec l'ID spécifié
        $colis = Colis::findOrfail($id);
// dd($colis);
        return view('customer.colis.add.edit_payement', compact('colis'));
    }
// AJAX pour récupérer la liste des colis en attente
    public function get_colis(Request $request)
    {
        // Vérifie que la requête est AJAX
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
        // Récupération de l'email de l'utilisateur connecté
        $email = auth()->user()->email;
        // Récupérer les colis associés à l'email et à l'état "en attente"
        $colis = Colis::select(
            'colis.*', // Toutes les colonnes de la table colis
            'expediteurs.nom as expediteur_nom',
            'expediteurs.prenom as expediteur_prenom',
            'expediteurs.email as expediteur_email',
            'expediteurs.agence as expediteur_agence',
            'destinataires.nom as destinataire_nom',
            'destinataires.prenom as destinataire_prenom',
            'destinataires.agence as destinataire_agence',
            'destinataires.email as destinataire_email',
            'colis.reference_colis as reference_colis',
            'colis.etat as etat',
            'colis.created_at as created_at'
        )
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id') // Jointure avec la table expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id') // Jointure avec la table destinataires
        ->where('expediteurs.email', $email) // Vérifie que l'expéditeur correspond à l'utilisateur connecté
        ->where('etat', 'en attente') 
        ->get();
        // Construire et retourner la DataTable
        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                $editUrl = route('customer_colis.payement.edit', ['id' => $row->id]);
                return '
                    <div class="btn-group">
                        <a href="' . $editUrl . '" class="btn btn-sm btn-success" title="View">
                            <i class="fas fa-hand-holding-usd"></i>
                        </a>
                    </div>';
            })
            ->rawColumns(['action']) // Permet le rendu des colonnes contenant du HTML
            ->make(true);
    }


// colis valider
    public function get_colis_valide(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
        // Récupération de l'email de l'utilisateur connecté
        $email = auth()->user()->email;
        // Récupérer les colis associés à l'email et à l'état "en attente"
        $colis = Colis::select(
            'colis.*', // Toutes les colonnes de la table colis
            'expediteurs.nom as expediteur_nom',
            'expediteurs.prenom as expediteur_prenom',
            'expediteurs.email as expediteur_email',
            'expediteurs.agence as expediteur_agence',
            'destinataires.nom as destinataire_nom',
            'destinataires.prenom as destinataire_prenom',
            'destinataires.agence as destinataire_agence',
            'destinataires.email as destinataire_email',
            'colis.reference_colis as reference_colis',
            'colis.etat as etat',
            'colis.created_at as created_at'
        )
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id') // Jointure avec la table expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id') // Jointure avec la table destinataires
        ->where('expediteurs.email', $email) // Vérifie que l'expéditeur correspond à l'utilisateur connecté
        ->where('etat', 'Validé') 
        ->get();
        // Construire et retourner la DataTable
        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                return '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal" data-id="' . $row->id . '">
                        <i class="fas fa-hand-holding-usd"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['action']) // Permet le rendu des colonnes contenant du HTML
            ->make(true);
        // Retourner une réponse d'erreur si la requête n'est pas AJAX
        return response()->json(['message' => 'Requête non valide'], 400);
    }

    // AJAX pour récupérer la liste des colis en Suivi
    public function get_colis_suivi(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
        // Récupération de l'email de l'utilisateur connecté
        $email = auth()->user()->email;
        // Récupérer les colis associés à l'email et à l'état "en attente"
        $colis = Colis::select(
            'colis.*',
            'expediteurs.nom as expediteur_nom',
            'expediteurs.prenom as expediteur_prenom',
            'expediteurs.email as expediteur_email',
            'expediteurs.agence as expediteur_agence',
            'destinataires.nom as destinataire_nom',
            'destinataires.prenom as destinataire_prenom',
            'destinataires.agence as destinataire_agence',
            'destinataires.email as destinataire_email',
            'colis.reference_colis as reference_colis',
            'colis.etat as etat',
            'colis.created_at as created_at'
        )
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id') // Jointure avec la table expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id') // Jointure avec la table destinataires
        ->where('expediteurs.email', $email) // Vérifie que l'expéditeur correspond à l'utilisateur connecté
        ->whereIn('etat', ['En transit','En entrepot', 'Déchargé', 'Chargé'])  
        ->get();
        // Construire et retourner la DataTable    
        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                return '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal" data-id="' . $row->id . '">
                        <i class="fas fa-hand-holding-usd"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['action']) // Permet le rendu des colonnes contenant du HTML
            ->make(true);
        // Retourner une réponse d'erreur si la requête n'est pas AJAX 
        return response()->json(['message' => 'Requête non valide'], 400);
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
