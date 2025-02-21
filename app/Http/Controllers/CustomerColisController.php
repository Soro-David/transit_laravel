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


    public function add_colis(Request $request)
    {
        $id = auth()->user()->getIdUSer();
        // dd($id);
        $user = User::findOrfail($id);
        // Chargement des données
        // dd($user);
        // Récupérer les pays sans doublons
    $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')
    ->distinct()
    ->pluck('pays_agence');


        // Récupérer les agences avec leur pays associé
        $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
        $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
        $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();

        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();
        $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
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
        $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
        return view('customer.colis.fin_colis', compact('agences','referenceColis', 'client_expediteurs', 'client_destinataires','user'));
    }

    public function store_colis(Request $request)
    {
        // dd($request->all());
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
            $data = array_merge(session('step1', []));

            $data['status'] = $data['mode_payement'] ?? 'non payé';
            $data['etat'] = $data['etat'] ?? 'En attente';

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
    
            // $payementData = [
            //     'mode_de_payement' => $data['mode_payement'],
            //     'montant_reçu' => $data['montant_reçu'],
            //     'operateur_mobile' => $data['operateur_mobile'],
            //     'numero_compte' => $data['numero_compte'],
            //     'nom_banque' => $data['nom_banque'],
            //     'id_transaction' => $data['transaction_id'],
            //     'numero_tel' => $data['numero_tel'],
            //     'numero_cheque' => $data['numero_cheque'],
            // ];
        // dd($payementData);
            // Insérer les données dans chaque table
            $expediteur = Expediteur::create($expediteurData);
            $destinataire = Destinataire::create($destinataireData);
            // $payement = Paiement::create($payementData);
    
            $colis = [];
        foreach ($colisData as $colisItem) {
            $colis[] = Colis::create(array_merge($colisItem, [
                'expediteur_id' => $expediteur->id,
                'destinataire_id' => $destinataire->id,
            ]));
        }

        // dd($colis);
            session()->forget(['step1']);
            return redirect()->route('customer_colis.hold');

        } catch (\Exception $e) {
            // Enregistre l'erreur dans les logs
            \Log::error('Erreur lors de l\'enregistrement du colis : ' . $e->getMessage());

            // Retourne une réponse avec un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du colis. Veuillez réessayer.');
        }
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
        // Vérifier que la requête est bien une requête AJAX
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
    
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'mode_de_paiement'  => 'required|string',
            'numero_compte'     => 'nullable|string',
            'nom_banque'        => 'nullable|string',
            'transaction_id'    => 'nullable|string',
            'numero_tel'        => 'nullable|string',
            'operateur_mobile'  => 'nullable|string',
            'numero_cheque'     => 'nullable|string',
        ]);
    
        // Récupérer le colis en utilisant le paramètre $id
        $colis = Colis::findOrFail($id);
        
        // Associer le paiement au colis
        $validatedData['colis_id'] = $id;
    
        // Vérifier si un paiement a déjà été effectué pour ce colis
        $existingPayment = Paiement::where('colis_id', $id)->first();
        if ($existingPayment) {
            return response()->json(['message' => 'Le paiement a déjà été effectué pour ce colis.'], 400);
        }
    
        // Créer le paiement
        Paiement::create($validatedData);
    
        // Mettre à jour le champ 'etat' du colis en le marquant comme "Validé"
        $colis->etat = 'Validé';
        $colis->save();
    
        return response()->json(['message' => 'Paiement enregistré avec succès et colis marqué comme validé !']);
        return redirect()->route('customer_colis.index');    
    }
    
    
    public function edit_payement($id)
    {
        // Récupérer le colis par son ID
        $colis = Colis::findOrFail($id);
    
        return view('customer.colis.edit_payement', compact('colis'));
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
            'destinataires.tel as destinataire_tel',
            'colis.reference_colis as reference_colis',
            'colis.etat as etat',
            'colis.created_at as created_at'
        )
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id') // Jointure avec la table expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id') // Jointure avec la table destinataires
        ->where('expediteurs.email', $email) // Vérifie que l'expéditeur correspond à l'utilisateur connecté
        ->where('etat', 'En attente') 
        ->get();
        // Construire et retourner la DataTable
        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                $editUrl = route('customer_colis.payement.edit', ['id' => $row->id]);
                return '
                    <div class="btn-group">
                        <a href="' . $editUrl . '" class="btn btn-sm btn-delete"   title="View">
                            <i class="fas fa-trash"></i>
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
            'colis.prix_transit_colis as prix_transit_colis',
            'destinataires.email as destinataire_email',
            'colis.reference_colis as reference_colis',
            'colis.etat as etat',
            'colis.created_at as created_at'
        )
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id') // Jointure avec la table expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id') // Jointure avec la table destinataires
        ->where('expediteurs.email', $email) // Vérifie que l'expéditeur correspond à l'utilisateur connecté
        ->where('etat', 'Devis') 
        ->get();
        // Construire et retourner la DataTable
        return DataTables::of($colis)
            ->addColumn('etat', function ($row) {
                return $row->etat === 'Devis' ? 'Dévis validé' : $row->etat; // Changer l'affichage de l'état
            })
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
        // Retourner une réponse d'erreur si la requête n'est pas AJAX
        return response()->json(['message' => 'Requête non valide'], 400);
    }

    // AJAX pour récupérer la liste des colis en Suivi
    public function get_colis_suivi(Request $request)
    {
        // Vérifier si la requête est une requête AJAX
        if (!$request->ajax()) {
            return response()->json(['message' => 'Requête non valide'], 400);
        }
    
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
    
        // Récupération de l'email de l'utilisateur connecté
        $email = auth()->user()->email;
    
        // Récupérer les colis associés à l'email et à l'état "en attente"
        $colis = Colis::select(
            'colis.*',
            'colis.reference_colis as reference_colis',
            'expediteurs.agence as expediteur_agence',
            'destinataires.tel as destinataire_tel',
            'destinataires.agence as destinataire_agence',
            'colis.etat as etat',
            'colis.updated_at as updated_at',
            'expediteurs.nom as expediteur_nom',
            'expediteurs.prenom as expediteur_prenom',
            'expediteurs.email as expediteur_email',
            'destinataires.nom as destinataire_nom',
            'destinataires.prenom as destinataire_prenom',
            
            

        )
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id') // Jointure avec la table expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id') // Jointure avec la table destinataires
        ->where('expediteurs.email', $email) // Vérifie que l'expéditeur correspond à l'utilisateur connecté
        ->whereIn('colis.etat', ['En transit', 'Validé', 'En entrepot', 'Dechargé', 'Chargé', 'Fermé', 'Livré'])  
        ->get();
        foreach ($colis as $colisItem) {
            switch ($colisItem->etat) {
                case 'Fermé':
                    $colisItem->etat = 'Colis en transit';
                    break;
                case 'Dechargé':
                    $colisItem->etat = 'Colis arrivé';
                    break;
                case 'Livré':
                    $colisItem->etat = 'Colis livré';
                    break;
            }
        }
        return DataTables::of($colis)->make(true);
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
