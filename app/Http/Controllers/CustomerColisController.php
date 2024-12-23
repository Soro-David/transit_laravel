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
    public function storeStep1(Request $request)
    {
        // Validation des données
        $request->validate([
            // Ajouter les règles de validation si nécessaires
        ]);

        // Stockage des données en session
        session(['step1' => $request->only([
            'nom_expediteur', 'prenom_expediteur', 'email_expediteur', 'tel_expediteur',
            'agence_expedition', 'lieu_expedition', 'nom_destinataire', 'prenom_destinataire',
            'email_destinataire', 'tel_destinataire', 'agence_destination', 'lieu_destination'
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
            'description' => 'required',
            'quantite' => 'required',
            'dimension' => 'required',
            'poids' => 'required',
        ]);
        session(['step2' => $request->only([
            'description', 'quantite', 'dimension', 'dimension', 'poids'
            ])]);
// dd($request->all());
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
            'quantite' => 'required',
            'type_emballage' => 'required',
            'dimension' => 'required',
            'description_colis' => 'required',
            'poids_colis' => 'required',
            'valeur_colis' => 'required',
        ]);
    
        // Ajout du champ 'etat' avec la valeur 'En attente'
        session(['step3' => array_merge(
            $request->only([
                'quantite',
                'type_emballage',
                'dimension',
                'description_colis',
                'poids_colis',
                'valeur_colis'
            ]),
            ['etat' => 'En attente'] // Ajout du champ 'etat'
        )]);
    
    

        return redirect()->route('customer_colis.create.step4');
    }

    public function createStep4()
    {
        return view('customer.colis.add.step4');
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

        // $data['status'] = $data['mode_payement'] ?? 'non payé';
        $colis = Les_colis::create($data);
        // Nettoyer les sessions
        session()->forget(['step1', 'step2', 'step3', 'step4']);
        return view('customer.colis.add.complete',compact('colis','data'));
    }
    /**
     * Enregistre les informations de paiement.
     */
    public function stepPayement()
    {
        return view('customer.colis.add.payement');
    }
    public function storePayement(Request $request)
    {
        $request->validate([
            'mode_payement' => 'required|in:bank,mobile_money,cheque,cash',
            // Validation pour le paiement bancaire
            'numero_compte' => 'required_if:mode_payement,bank|max:255',
            'nom_banque' => 'required_if:mode_payement,bank|max:255',
            'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
            // Validation pour Mobile Money
            'tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
            'operateur' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
            // Validation pour le paiement par chèque
            'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
            'nom_banque' => 'required_if:mode_payement,cheque|max:255',
            // Validation pour le paiement en espèces
            'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:1',
        ]);
        session(['step4' => $request->only([
            'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
            'tel', 'operateur', 'numero_cheque', 'montant_reçu',
        ])]);
        return redirect()->route('customer_colis.create.qrcode');
    }
    public function qrcode(Request $request)
    {
        $data = array_merge(
            session('step1', []),
            session('step2', []),
            session('step3', []),
            session('step4', []) 
        );
        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $colis = Les_colis::create($data);
        // Générer le contenu du QR code (par exemple : ID du colis + statut)
        // Nettoyer les sessions
        session()->forget(['step1', 'step2', 'step3', 'step4']);
        // return redirect()->route('colis.complete');
        return view('customer.colis.add.complete',compact('colis','data'));
    }
    
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

    public function hold()
    {
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

    public function get_colis(Request $request)
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
                ->where('etat', 'en attente') // Vérifier que l'état est "en attente"
                ->get();

                // Construire et retourner la DataTable
                return DataTables::of($colis)
                    // Ajouter une colonne d'action
                    ->addColumn('action', function ($row) {
                        $editUrl = url('/users/' . $row->id . '/edit'); // Génère une URL pour l'édition

                        return '
                        <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="fas fa-edit"></i> 
                                </button>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="fas fa-hand-holding-usd"></i> 
                                </button>
                                <button type="button" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i> 
                                </button>

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

// colis valider
public function colis_valide(Request $request)
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
                $editUrl = url('/users/' . $row->id . '/edit'); // Génère une URL pour l'édition

                return '
                   <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal">
                            <i class="fas fa-edit"></i> 
                        </button>
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
