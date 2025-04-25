<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use App\Models\Bateaux;
use App\Models\Paiement;
use App\Models\Article;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Invoice;

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
    /**
     * Génère une référence de Contenaire unique
     *
     * @return string
     */
        private function generateReferenceContenaire()
        {
            // Récupérer l'année et le mois actuel
            $year = now()->format('y'); // Année sur 2 chiffres
            $monthLetter = now()->format('F')[0]; // Première lettre du mois (ex: 'J' pour Janvier)
    
            // Initialiser le chiffre à 1
            $increment = 1;
    
            // Construire la référence de base
            $baseReference = "CNT-{$monthLetter}-{$increment}";
    
            // Vérifier si la référence existe déjà dans la table colis
            while (DB::table('colis')->where('reference_contenaire', $baseReference)->exists()) {
                // Incrémenter le chiffre
                $increment++;
                // Mettre à jour la référence avec le nouvel incrément
                $baseReference = "CNT-{$monthLetter}-{$increment}";
            }
    
            return $baseReference; // Retourner la référence finale
        }
    
        private function generateReferenceVol()
        {
            // Récupérer l'année et le mois actuel
            $year = now()->format('y'); // Année sur 2 chiffres
            $monthLetter = now()->format('F')[0]; // Première lettre du mois (ex: 'J' pour Janvier)
    
            // Initialiser le chiffre à 1
            $increment = 1;
    
            // Construire la référence de base
            $baseReference = "Vol-{$monthLetter}-{$increment}";
    
            // Vérifier si la référence existe déjà dans la table conteneurs
            while (DB::table('colis')->where('reference_vol', $baseReference)->exists()) {
                // Incrémenter le chiffre
                $increment++;
                // Mettre à jour la référence avec le nouvel incrément
                $baseReference = "Vol-{$monthLetter}-{$increment}";
            }
    
            return $baseReference; // Retourner la référence finale
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

     public function add_colis(Request $request)
    {
        
        $agences = Agence::select('nom_agence', 'id')->get();
        $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
        return view('IPMS_SIMEXCI_ANGRE.colis.add_colis', compact('agences','referenceColis'));
    }

    public function store_colis(Request $request)
    {
        try {
            // Sauvegarde des données de la première étape dans la session
            $request->session()->put('step1', $request->all());

            session(['step1' => $request->only([
                'nom_expediteur',
                'prenom_expediteur', 
                'email_expediteur', 
                'tel_expediteur',
                'adresse_expediteur',
                'agence_expedition', 
                'nom_destinataire', 
                'prenom_destinataire',
                'email_destinataire', 
                'tel_destinataire',
                'adresse_destinataire',
                'agence_destination',
                'mode_transit',
                'reference_colis',
                'quantite_colis',
                'type_embalage',
                'hauteur',
                'largeur',
                'longueur',
                'dimension_result',
                'type_colis',
                'poids',
                'description_colis',
            ])]);

            return redirect()->route('ipms_angre_colis.create.payement');

        } catch (\Exception $e) {
            // Enregistre l'erreur dans les logs
            \Log::error('Erreur lors de l\'enregistrement du colis : ' . $e->getMessage());

            // Retourne une réponse avec un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du colis. Veuillez réessayer.');
        }
    }


    public function stepPayment()
    {
       
        return view('IPMS_SIMEXCI_ANGRE.colis.add.payement');
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
    

    public function generer_qrcode(Request $request)
    {
        // Fusionner toutes les données de session dans un tableau
        $data = array_merge(
            session('step1', []),
            session('step2', [])
        );
    // dd($data);
        // Ajouter le statut au tableau de données
        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'Validé';
    
        // Vérifiez que les données sont bien réparties pour chaque table
        $expediteurData = [
            'nom' => $data['nom_expediteur'],
            'prenom' => $data['prenom_expediteur'],
            'email' => $data['email_expediteur'],
            'tel' => $data['tel_expediteur'],
            'agence' => $data['agence_expedition'],
            'adresse' => $data['adresse_expediteur'],
        ];
    
        $destinataireData = [
            'nom' => $data['nom_destinataire'],
            'prenom' => $data['prenom_destinataire'],
            'email' => $data['email_destinataire'],
            'tel' => $data['tel_destinataire'],
            'agence' => $data['agence_destination'],
            'adresse' => $data['adresse_destinataire'],
        ];
    
        // Initialisation du tableau pour stocker les données des colis
        $colisData = [];
    
        // Parcourir les tableaux pour construire $colisData
        foreach ($data['quantite_colis'] as $index => $quantite) {
            $hauteur = $data['hauteur'][$index] ?? null;
            $largeur = $data['largeur'][$index] ?? null;
            $longueur = $data['longueur'][$index] ?? null;
            
            if (isset($hauteur, $largeur, $longueur)) {
                $dimension_result = "{$hauteur}x{$largeur}x{$longueur}";
            } else {
                $dimension_result = null;
            }
            
            // dd($dimension_result);
            
            $colisData[] = [
                'reference_colis' => $data['reference_colis'],
                'reference_contenaire' => $data['reference_contenaire'] ?? null,
                'quantite_colis' => $quantite,
                'type_embalage' => $data['type_embalage'][$index] ?? null,
                'poids_colis' => $data['poids_colis'][$index] ?? null,
                // 'dimension_result' => $data['dimension_result'][$index] ?? null,
                'mode_transit' => $data['mode_transit'] ?? null,
                'status' => $data['status'] ?? null,
                'etat' => $data['etat'] ?? null,
                'type_colis' => $data['type_colis'][$index] ?? null,
                'dimension_result' => $dimension_result,
                'description_colis' => $data['description_colis'][$index] ?? null,
            ];
        }
        
    // dd($colisData);
        $nombreQuantiteColis = count($data['quantite_colis']);
    
        $payementData = [
            'mode_de_payement' => $data['mode_payement'],
            'montant_reçu' => $data['montant_reçu'],
            'operateur_mobile' => $data['operateur_mobile'],
            'numero_compte' => $data['numero_compte'],
            'nom_banque' => $data['nom_banque'],
            'id_transaction' => $data['transaction_id'],
            'numero_tel' => $data['numero_tel'],
            'numero_cheque' => $data['numero_cheque'],
        ];
    // dd($payementData);
        // Insérer les données dans chaque table
        $expediteur = Expediteur::create($expediteurData);
        $destinataire = Destinataire::create($destinataireData);
        $payement = Paiement::create($payementData);
    
        // Créer les colis
        $colis = [];
        foreach ($colisData as $colisItem) {
            $colis[] = Colis::create(array_merge($colisItem, [
                'expediteur_id' => $expediteur->id,
                'destinataire_id' => $destinataire->id,
                'paiement_id' => $payement->id,
            ]));
        }
    // dd($colis);
        // Générer les QR codes pour chaque colis
        foreach ($colis as $colisItem) {
            // Données à encoder dans le QR code
            $qrData = [
                'Identifiant' => $colisItem->id,
                'Référence colis' => $colisItem->reference_colis,
                'Statut' => $colisItem->etat,
                'Nom Expéditeur' => $expediteur->nom . ' ' . $expediteur->prenom,
                'Nom Destinataire' => $destinataire->nom . ' ' . $destinataire->prenom,
                'Téléphone Destinataire' => $destinataire->tel,
                'Agence Destination' => $destinataire->agence ?? '',
                'Lieu de Destination' => $destinataire->adresse ?? '',
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
            $filePath = 'qrcodes/colis_' . $colisItem->id . '.png';
            $fullPath = public_path($filePath);
    
            // Vérifier et créer le répertoire cible si nécessaire
            $directory = dirname($fullPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
    
            // Sauvegarder le fichier QR code dans le storage
            file_put_contents($fullPath, $pngData);
    
            // Mettre à jour le chemin du QR code dans la base de données
            $colisItem->update(['qr_code_path' => $filePath]);
        }
    
        // Réinitialiser les sessions après traitement
        session()->forget(['step1', 'step2']);
    
        // Retourner la vue avec les informations nécessaires
        return view('IPMS_SIMEXCI_ANGRE.colis.add.complete', compact('colis', 'filePath', 'fullPath', 'result'));
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
            'lieu_rraison' => $request->lieu_livraison,
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
        // dd($id);
        $colis = Colis::findOrFail($id);
        // dd($colis);
        return view('IPMS_SIMEXCI_ANGRE.colis.edit_hold', compact('colis'));
    }

    // Fonction update pour les colis en attente
    public function update_hold(Request $request, $id)
    {
    // dd($request->all());

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
            'etat' => 'Devis', // Ajout du statut
        ]);
        // Redirection avec un message de succès
        return redirect()->route('ipms_angre_colis.hold')->with('success', 'Colis mis à jour avec succès !');
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
    // Ajax pour les colis arrivés

    // public function get_colis_dump(Request $request)
    // {

    //     if ($request->ajax()) {
    //         $colis = Colis::query()
    //             ->select(
    //                 'colis.*',
    //                 'colis.reference_colis as reference_colis',
    //                 'expediteurs.nom as expediteur_nom',
    //                 'expediteurs.prenom as expediteur_prenom',
    //                 'expediteurs.tel as expediteur_tel',
    //                 'expediteurs.agence as expediteur_agence',
    //                 'destinataires.nom as destinataire_nom',
    //                 'destinataires.prenom as destinataire_prenom',
    //                 'destinataires.agence as destinataire_agence',
    //                 'destinataires.tel as destinataire_tel',
    //                 'colis.etat as etat',
    //                 'colis.created_at as created_at'
    //             )
    //             ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
    //             ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
    //             ->where('etat', 'Dechargé','Livré')  // Filtre l'état des colis
    //             ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
    //             ->where('colis.mode_transit', 'aerien')
    //             ->where('colis.recup', 'oui')
    //             ->get();
    //         $colisGrouped = $colis->groupBy('reference_colis');


    //         $colisWithCount = $colisGrouped->map(function ($group, $reference) {
    //             return [
    //                 'reference_colis' => $reference,
    //                 'nombre_de_colis' => $group->count(),
    //                 'expediteur_nom' => $group->first()->expediteur_nom,
    //                 'expediteur_prenom' => $group->first()->expediteur_prenom,
    //                 'expediteur_tel' => $group->first()->expediteur_tel,
    //                 'expediteur_agence' => $group->first()->expediteur_agence,
    //                 'destinataire_nom' => $group->first()->destinataire_nom,
    //                 'destinataire_prenom' => $group->first()->destinataire_prenom,
    //                 'destinataire_tel' => $group->first()->destinataire_tel,
    //                 'destinataire_agence' => $group->first()->destinataire_agence,
    //                 'etat' => $group->first()->etat, // conserve l'état d'origine ici
    //                 'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
    //                 'colis' => $group
    //             ];
    //         })->values();

    //         return DataTables::of($colisWithCount)
    //             ->addColumn('etat', function ($row) {
    //                 return $row['etat'] === 'Dechargé' ? 'Colis validé' : $row['etat'];
    //             })
    //             ->addColumn('action', function ($row) {
    //                 $firstColis = $row['colis']->first();
    //                 $editUrl = route('ipms_angre_colis.valide.edit', ['id' => $firstColis->id]);
    //                 // $deleteUrl = route('ipms_colis.destroy.colis.valide', ['id' => $firstColis->id]);
    //                 $invoiceUrl = route('ipms_angre_colis.valide.edit.invoice', ['id' => $firstColis->id]);
    
    //                 return '
    //                 <div class="d-flex align-items-center gap-2">
    //                     <div class="btn-group">
    //                         <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modifier" data-bs-target="#modifModal">
    //                             <i class="fas fa-credit-card" style="font-size: 15px;"></i>
    //                         </a>
    //                     </div> 
    //                     <div class="btn-group">
    //                         <a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Facture" data-bs-target="#modifModal">
    //                             <i class="fas fa-file-invoice" style="font-size: 15px;"></i>
    //                         </a>
    //                     </div> 
                         
    //                 </div>
    //                 ';
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }
    // }

    public function get_colis_dump(Request $request)
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
                ->where('etat', 'Dechargé','Livré')  // Filtre l'état des colis
                ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                ->where('colis.mode_transit', 'aerien')
                ->where('colis.recup', 'oui')
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



    public function get_colis_suivi(Request $request)
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
                ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                ->where('colis.mode_transit', 'maritime')
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
            $colis = Colis::select(
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
                ->where('colis.etat', 'Validé')
                ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                ->get();

            return DataTables::of($colis)
                ->addColumn('etat', function ($row) {
                    return ($row->etat === 'Validé') ? 'Colis validé' : $row->etat;
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('ipms_angre_colis.valide.edit', ['id' => $row->id]);
                    $deleteUrl = route('ipms_angre_colis.destroy.colis.valide', ['id' => $row->id]);
                    $printUrl = route('ipms_angre_colis.facture.colis.print', ['id' => $row->id]);
                    $invoiceUrl = route('ipms_angre_colis.valide.edit.invoice', ['id' => $firstColis->id]);

                    return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modifier" data-bs-target="#modifModal">
                                <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                            </a>
                            <a href="' . $printUrl . '" class="btn btn-sm btn-info" title="Imprimer" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                            <i class="fas fa-trash"></i>
                        </button>
                        <a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Facture" data-bs-target="#modifModal">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
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
    public function destroy_colis_valide($id)
    {
        // dd($id);
        try {
            $colis = Colis::findOrFail($id);
            
            $colis->delete();
            return redirect()->route('ipms_angre_colis.hold')->with('success', 'Colis supprimé avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('ipms_angre_colis.hold')->with('error', 'Une erreur est survenue lors de la suppression du colis : ' . $e->getMessage());
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

    public function get_colis_vol(Request $request)
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table destinataires
            ->where('colis.mode_transit', 'Aerien')  // Filtre pour le mode de transit
            ->where('colis.etat', 'Chargé')  // Filtre l'état des colis
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
            ->get(); // Exécute la requête 

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
   

public function cargaison_ferme(Request $request)
{
    return view('IPMS_SIMEXCI_ANGRE.cargaison.cargaison_ferme');
}
public function contenaire_fermer(Request $request)
{

try {
    // Compter les enregistrements avant la mise à jour
    $count = Colis::where('etat', 'Chargé')->count();
    if ($count === 0) {
        return redirect()->back()->with('warning', 'Aucun colis avec l’état "validé" trouvé.');
    }
     // Générer une référence unique pour le conteneur
     $referenceContenaire = 'CNT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(3));
    // Mise à jour des enregistrements
    $colisData = Colis::where('etat', 'Chargé')
        ->update(['etat' => 'Fermé', 'reference_contenaire' => $referenceContenaire]);

    return redirect()->back()->with('success', "$colisData colis sont enregistrer dans le conteneur $referenceContenaire avec succès.");
} catch (\Exception $e) {
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
                      ->where('recuperer', '!=', 'oui') // Exclure les bateaux déjà récupérés
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

            if (!$ballon) {
                throw new Exception('🚢 Bateau non trouvé.');
            }

            // Vérifier si le bateau est déjà récupéré
            if ($ballon->recuperer === 'oui') {
                throw new Exception('⚠️ Ce bateau a déjà été récupéré.');
            }

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
                'created_at as date_depart', // Création comme date de départ
                'date_arriver'
            )->where('agence_destination', 'IPMS-SIMEX-CI Angre 8ème Tranche')
            ->where('recuperer', '=', 'oui')
            ->get();
    
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
    public function editInvoice($id)
    {
        
        
        $colis_principal = Colis::find($id);
        $colis_info = Colis::with(['expediteur', 'destinataire'])
                                ->select(
                                    'colis.reference_colis',
                                    'expediteurs.nom as expediteur_nom',
                                    'expediteurs.prenom as expediteur_prenom',
                                    'expediteurs.tel as expediteur_tel',
                                    'destinataires.nom as destinataire_nom',
                                    'destinataires.prenom as destinataire_prenom',
                                    'destinataires.tel as destinataire_tel'
                                )
                                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                                ->find($id);

        if (!$colis_principal) {
            return redirect()->route('ipms_angre_colis.hold')->with('error', 'Colis non trouvé.');
        }

        $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

        if ($colis->isEmpty()) {
            return redirect()->route('ipms_angre_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
        }

        return view('IPMS_SIMEXCI_ANGRE.invoice.edit', compact('colis','colis_info'));
    }

    public function inprimerEtiquette($id)
    {
            $colis_principal = Colis::find($id);

            if (!$colis_principal) {
                return redirect()->route('ipms_angre_colis.hold')->with('error', 'Colis non trouvé.');
            }

            $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

            if ($colis->isEmpty()) {
                return redirect()->route('ipms_angre_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
            }       

            foreach ($colis as $colisItem) {
                   $qrData = [
                       'Référence colis'       => $colisItem->reference_colis,
                       'Statut'                => $colisItem->status,
                       'Nom Expéditeur'        => $colisItem->expediteur->nom . ' ' . $colisItem->expediteur->prenom,
                       'Nom Destinataire'      => $colisItem->destinataire->nom . ' ' . $colisItem->destinataire->prenom,
                       'Téléphone Destinataire'=> $colisItem->destinataire->tel,
                       'Agence Destination'    => $colisItem->destinataire->agence ?? '',
                       'Lieu de Destination'   => $colisItem->destinataire->lieu_destination ?? '',
                   ];
       
                   $qrCodeContent = '';
                   foreach ($qrData as $key => $value) {
                       $qrCodeContent .= "{$key}: {$value}\n";
                   }
       
                   $qrCode = new QrCode($qrCodeContent);
                   $writer = new PngWriter();
                   $result = $writer->write($qrCode);
                   $pngData = $result->getString();
       
                   $filePath = 'qrcodes/colis_' . $colisItem->id . '.png';
                   $fullPath = public_path($filePath);
       
                   $directory = dirname($fullPath);
                   if (!File::exists($directory)) {
                       File::makeDirectory($directory, 0755, true);
                   }
       
                   file_put_contents($fullPath, $pngData);
       
                   $colisItem->update(['qr_code_path' => $filePath]);
               }
       
        return view('IPMS_SIMEXCI_ANGRE.invoice.edit_etiquette', compact('colis'));
    }

    public function imprimerFacture($id)
    {
        
        $colis_principal = Colis::find($id);

            if (!$colis_principal) {
                return redirect()->route('ipms_angre_colis.hold')->with('error', 'Colis non trouvé.');
            }

            $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

            if ($colisCollection->isEmpty()) {
                return redirect()->route('ipms_angre_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
            }   

        if ($colisCollection->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun colis trouvé avec cette référence.');
        }

        // Récupération du premier colis
        $firstColis = $colisCollection->first();
        
        $date_facture = now();
        $expediteur = $firstColis->expediteur->nom . ' ' . $firstColis->expediteur->prenom;
        $tel_expediteur = $firstColis->expediteur->tel;
        $destinataire = $firstColis->destinataire->nom . ' ' . $firstColis->destinataire->prenom;
        $tel_destinataire = $firstColis->destinataire->tel;
        $numero_facture = '00' . str_pad($firstColis->id, 3, '0', STR_PAD_LEFT);
        $reference_colis = $firstColis->reference_colis;
        
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
        return view('IPMS_SIMEXCI_ANGRE.invoice.edit_invoice', compact(
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
            'tel_destinataire'
        ));
    }

}
