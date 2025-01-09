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
use App\Models\Colis;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Paiement;
use App\Models\Article;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
/**
 * Génère une référence de Contenaire unique
 *
 * @return string
 */
private function generateReferenceContenaire()
{
    // Exemple : "CNT-24201-XXX" (année 2024, mois janvier)
    return 'CNT-' . now()->format('y-M');
}


    public function storeStep3(Request $request)
    {

        $request->validate([
            'articles.*.description' => 'required|string|max:255',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.dimension' => 'required|string|max:255',
            'articles.*.poids' => 'required|numeric|min:0',
        ]);
    
        // Stocker les données validées dans la session
        session(['step2' => $request->only(['description', 'quantite', 'dimension', 'poids'])]);

        return redirect()->route('colis.create.step4');
    }
    /**
     * Étape 3 : Informations de transit.
     */
    public function createStep4(Request $request)
    {
       $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
        // dd($referenceColis);
        return view('admin.colis.add.step4',['referenceColis' => $referenceColis]);
    }
    public function storeStep4(Request $request)
    {
       
        $request->session()->put('step4', $request->all());
        $request->validate([
            // 'mode_transit' => 'required',
            // 'reference_colis' => 'required',
        ]);

        session(['step4' => $request->only([
            'mode_transit', 'reference_colis'
        ])]);

        return redirect()->route('colis.create.payement');
    }
    /**
     * Enregistre les informations de paiement.
     */
    public function stepPayement()
    {
       
        return view('admin.colis.add.payement',['stepProgress' => 100]);
    }
    public function storePayement(Request $request)
    {

        $request->validate([
            // 'mode_payement' => 'required|in:bank,mobile_money,cheque,cash',
            // // Validation pour le paiement bancaire
            // 'numero_compte' => 'required_if:mode_payement,bank|max:255',
            // 'nom_banque' => 'required_if:mode_payement,bank|max:255',
            // 'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
        
            // // Validation pour Mobile Money
            // 'tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
            // 'operateur' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
            // // Validation pour le paiement par chèque
            // 'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
            // 'nom_banque' => 'required_if:mode_payement,cheque|max:255',
            // // Validation pour le paiement en espèces
            // 'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:1',
        ]);

        session(['step5' => $request->only([
            'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
            'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu',
        ])]);

        return response()->json([
            'success' => true,
            'redirect' => route('colis.create.qrcode'),
        ]);
        
    }

    public function qrcode(Request $request)
    {
        // Fusionner toutes les données de session dans un tableau
        $data = array_merge(
            session('step1', []),
            session('step2', []),
            session('step3', []),
            session('step4', []),
            session('step5', [])
        );

        // Ajouter le statut au tableau de données
        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'Validé';


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
        ];
        $payementData = [
            'mode_de_payement' => $data['mode_payement'] ,
            'montant_reçu' => $data['montant_reçu'] ,
            'operateur_mobile' => $data['operateur_mobile'] ,
            'numero_compte' => $data['numero_compte'],
            'nom_banque' => $data['nom_banque'] ,
            'id_transaction' => $data['transaction_id'],
            'numero_tel' => $data['numero_tel'],
            'numero_cheque' => $data['numero_cheque'] ,
        ];

        // dd($expediteurData, $destinataireData, $articleData, $colisData, $payementData);


        // Insérer les données dans chaque table
        $expediteur = Expediteur::create($expediteurData);
        $destinataire = Destinataire::create($destinataireData);
        // $article = Article::create($articleData);
        $payement = Paiement::create($payementData);
        $colis = Colis::create(array_merge($colisData, [
            'expediteur_id' => $expediteur->id,
            'destinataire_id' => $destinataire->id,
            'paement_id' => $payement->id,
        ]));
        foreach ($articleData as $article) {
            $article['colis_id'] = $colis->id;
            Article::create($article);
        }
        
        // dd($article);

        

    // dd($colis );

        // Format lisible pour le QR code
        $qrData = [
            'Référence colis' => $data['reference_colis'],
            'Statut' => $colis->status,
            'Nom Expéditeur' => $data['nom_expediteur'] . ' ' . $data['prenom_expediteur'],
            'Nom Destinataire' => $data['nom_destinataire'] . ' ' . $data['prenom_destinataire'],
            'Téléphone Destinataire' => $data['tel_destinataire'],
            'Agence Destination' => $data['agence_destination'] ?? '',
            'Lieu de Destination' => $data['lieu_destination'] ?? '',
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

        // Définir le chemin du fichier QR code
        $filePath = 'qrcodes/colis_' . $colis->id . '.png';
        $fullPath = public_path($filePath);

        // Vérifier et créer le répertoire cible si nécessaire
        $directory = dirname($fullPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Sauvegarder le fichier QR code dans le storage
        file_put_contents($fullPath, $pngData);

        // Mettre à jour le chemin du QR code dans la base de données
        $colis->update(['qr_code_path' => $filePath]);

        // Réinitialiser les sessions après traitement
        session()->forget(['step1', 'step2', 'step3', 'step4', 'step5']);
    // dd($fullPath);
        // Retourner la vue avec les informations nécessaires
        // return redirect()->route('colis.create.complete', compact('colis', 'filePath'));
        return view('admin.colis.add.complete', compact('colis', 'filePath'));
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

    // AJAX pour les colis arrivés
    public function get_colis_dump(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->where('etat', 'Dechargé')
            ->get(); 
            return DataTables::of($colis)
                ->make(true);
        }
    }
    // AJAX pour les devis colis
    public function get_devis_colis(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->where('etat', 'Validé')  // Filtre l'état des colis
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


public function get_colis_hold(Request $request)
{
    if ($request->ajax()) {
        $colis = Colis::select(
            'colis.*',  // Sélectionne toutes les colonnes de colis
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
        ->where('etat', 'En attente','Validé')  // Filtre l'état des colis
        ->get(); // Exécute la requête une seule fois

        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                // $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis
                $editUrl = route('colis.hold.edit', ['id' => $row->id]);
// dd($editUrl);
                return '
                    <div class="btn-group">
                        <a href="{{ $editUrl }}" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modify" data-bs-target="#modifModal">
                            <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
            ->make(true);
    }
}
// Fonction edit pour les colis en attente
public function edit_hold($id)
    {
        $colis = Colis::findOrFail($id);
        // dd($colis);
        return view('admin.colis.edit_hold', compact('colis'));
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
            'status' => 'payé', // Ajout du statut
            'etat' => 'Validé', // Ajout du statut
        ]);
        // Redirection avec un message de succès
        return redirect()->route('colis.hold')->with('success', 'Colis mis à jour avec succès !');
    }
    


// AJX pour les contenaire 
public function get_colis_contenaire(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->where('etat', 'Validé')  // Filtre l'état des colis
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

public function contenaire_fermer(Request $request)
{

try {
    // Compter les enregistrements avant la mise à jour
    $count = Colis::where('etat', 'Validé')->count();
    if ($count === 0) {
        return redirect()->back()->with('warning', 'Aucun colis avec l’état "validé" trouvé.');
    }
     // Générer une référence unique pour le conteneur
     $referenceContenaire = 'CNT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(3));
    // Mise à jour des enregistrements
    $colisData = Colis::where('etat', 'Validé')
        ->update(['etat' => 'En entrepôt', 'reference_contenaire' => $referenceContenaire]);

    return redirect()->back()->with('success', "$colisData colis mis à jour avec succès.");
} catch (\Exception $e) {
    return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
}
}

public function devis_hold(Request $request)
{
    return view('admin.devis.hold');
}

public function liste_contenaire(Request $request)
{
    $referenceContenaire = $request->input('reference_contenaire', $this->generateReferenceContenaire());
    return view('admin.devis.liste_contenaire',compact('referenceContenaire'));
}

}
