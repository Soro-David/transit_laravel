<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Les_colis;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class ColisController extends Controller
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
        return view('admin.colis.add', compact('agences', 'client_expediteurs', 'client_destinataires'));
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
        $agences = Agence::select('nom_agence', 'id')->get();
        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        return view('admin.colis.add.step1', compact('agences', 'client_expediteurs', 'client_destinataires'));
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

        return redirect()->route('colis.create.step2');
    }

    /**
     * Étape 2 : Détails du colis.
     */
    public function createStep2()
    {
        return view('admin.colis.add.step2');
    }

    public function storeStep2(Request $request)
    {
        $request->validate([
            'quantite' => 'required',
            'type_emballage' => 'required',
            'dimension' => 'required',
            'description_colis' => 'required',
            'poids_colis' => 'required',
            'valeur_colis' => 'required',
        ]);

        session(['step2' => $request->only([
            'quantite', 'type_emballage', 'dimension', 'description_colis', 'poids_colis', 'valeur_colis',
        ])]);

        return redirect()->route('colis.create.step3');
    }
    /**
     * Étape 3 : Informations de transit.
     */
    public function createStep3()
    {
        return view('admin.colis.add.step3');
    }
    public function storeStep3(Request $request)
    {
        $request->validate([
            // 'mode_transit' => 'required',
            // 'reference_colis' => 'required',
        ]);

        session(['step3' => $request->only([
            'mode_transit', 'reference_colis'
        ])]);

        return redirect()->route('colis.create.payement');
    }
    /**
     * Enregistre les informations de paiement.
     */
    public function stepPayement()
    {
        return view('admin.colis.add.payement');
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
        
        return redirect()->route('colis.create.qrcode');
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
        $qrData = [
            'id_colis' => $colis->id,
            'status' => $colis->status,
            'expediteur' => $data['nom_expediteur'] . ' ' . $data['prenom_expediteur'],
            'destinataire' => $data['nom_destinataire'] . ' ' . $data['prenom_destinataire'],
        ];
        // Générer le QR code
        $qrCodeContent = json_encode($qrData);
        $qrCode = new QrCode($qrCodeContent);
    
        // Spécifiez le writer pour générer l'image en PNG
        $writer = new PngWriter();
        $qrCodeImage = $writer->write($qrCode); // Utilisation de 'write()' pour obtenir l'image
    
        // Définir le chemin du fichier QR code
        $filePath = 'qrcodes/colis_' . $colis->id . '.png';
    
        // Sauvegarder l'image dans le stockage public
        Storage::disk('public')->put($filePath, $qrCodeImage->getString()); // Utiliser 'getString()' pour obtenir le contenu binaire
    
        // Mettre à jour le chemin du QR code dans la base de données
        $colis->update(['qr_code_path' => $filePath]);
    
        // Nettoyer les sessions
        session()->forget(['step1', 'step2', 'step3', 'step4']);
    
        // Rediriger vers la page de confirmation
        // return redirect()->route('colis.complete');
        return view('admin.colis.add.complete',compact('colis','qrCodeImage','data'));
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
        dd($request);
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
        return view('admin.colis.hold');
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
        $colis = Les_colis::select('nom_expediteur',
         'prenom_expediteur',
         'tel_expediteur',
         'agence_expedition', 
         'nom_destinataire', 
         'prenom_destinataire',
         'agence_destination', 
         'tel_destinataire',
         'etat',
         'created_at'
                )
        ->where('etat', 'en attente')
        ->get();
        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                $editUrl = '/users/' . $row->id . '/edit';

                return '
                    <div class="btn-group">
                        <a href="#" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
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
public function devis_hold()
    {
        return view('admin.devis.hold');
    }

public function liste_contenaire()
    {
        return view('admin.devis.liste_contenaire');
    }

}
