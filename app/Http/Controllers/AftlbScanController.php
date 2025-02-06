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
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class AftlbScanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chargement()
    {
        return view('AFT_LOUIS_BLERIOT.scan.chargement');
    }

    public function dechargement()
    {
        return view('AFT_LOUIS_BLERIOT.scan.dechargement');
    }
    public function entrepot()
    {
        return view('AFT_LOUIS_BLERIOT.scan.entrepot');
    }




    public function get_colis_entrepot(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
                'expediteurs.nom as nom_expediteur', 
                'expediteurs.prenom as prenom_expediteur', 
                'expediteurs.tel as tel_expediteur', 
                'expediteurs.agence as agence_expedition', 
                'destinataires.nom as nom_destinataire', 
                'destinataires.prenom as prenom_destinataire', 
                'destinataires.tel as tel_destinataire', 
                'destinataires.agence as agence_destination',
                'colis.etat as etat',
                'colis.created_at as created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->where('etat', 'En entrepot')  // Filtre l'état des colis
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get(); 
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
    
// Ajax pour récupérer la liste des colis en Decharge
    public function get_colis_decharge(Request $request)
    {
        if ($request->ajax()) {
            // Récupérer les colis avec les relations expediteur et destinataire
            $colis = Colis::with(['expediteur', 'destinataire'])
                ->where('etat', 'Dechargé')  // Filtre l'état des colis
                ->whereHas('expediteur', function ($query) {
                    $query->where('agence', 'AFT Agence Louis Bleriot');
                })
                ->get();

            return DataTables::of($colis)
                ->addColumn('action', function ($row) {
                    $editUrl = route('colis.edit', $row->id); // Assurez-vous que cette route existe
                    $paymentUrl = route('colis.payment', $row->id); // Assurez-vous que cette route existe

                    return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="Voir" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="' . $paymentUrl . '" class="btn btn-sm btn-success" title="Paiement" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fas fa-credit-card"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
                ->make(true);
        }
    }


    // Ajax pour récupérer la liste des colis en Charge
    public function get_colis_charge(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
                'expediteurs.nom as nom_expediteur', 
                'expediteurs.prenom as prenom_expediteur', 
                'expediteurs.tel as tel_expediteur', 
                'expediteurs.agence as agence_expedition', 
                'destinataires.nom as nom_destinataire', 
                'destinataires.prenom as prenom_destinataire', 
                'destinataires.tel as tel_destinataire', 
                'destinataires.agence as agence_destination',
                'colis.etat as etat',
                'colis.created_at as created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->where('etat', 'Chargé')  // Filtre l'état des colis
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get(); 
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

    public function updateColisEntrepot(Request $request)
    {
        
        if (!$request->has('colisId')) {
            return response()->json(['success' => false, 'message' => 'colisId manquant.'], 400);
        }
    
        // Rechercher tous les colis correspondant à la référence et à l'identifiant fournis
        $colisList = Colis::where('reference_colis', $request->colisId)
                          ->where('id', $request->id)
                          ->get();
        // dd( $colisList);
    
        // Vérifier si des colis ont été trouvés
        if ($colisList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun colis trouvé avec cette référence et cet identifiant.'
            ], 404);
        }
    
        $messages = [];
        $updatedColis = [];
    
        // Parcourir chaque colis trouvé
        foreach ($colisList as $colis) {
            if ($colis->etat === 'En entrepot') {
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà en entrepôt.";
            } elseif ($colis->etat === 'Validé') {
                // Modifier l'état du colis en "En entrepot"
                $colis->etat = 'En entrepot';
                $colis->save();
                $updatedColis[] = [
                    'reference'   => $colis->reference_colis,
                    'id'          => $colis->id,
                    'etat'        => $colis->etat,
                    'description' => $colis->description,
                ];
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été mis en entrepôt avec succès.";
            } else {
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas encore validé. Impossible de le mettre en entrepôt.";
            }
        }
    
        return response()->json([
            'success'  => !empty($updatedColis),
            'messages' => $messages,
            'colis'    => $updatedColis,
        ]);
    }
    
    // Fonction Ajax pour le Scan chargement
    // {{ route("scan.get.colis.charge") }}
    public function updateColisCharge(Request $request)
{
    // Vérifier si 'colisId' et 'id' sont présents dans la requête
    if (!$request->has('colisId') || !$request->has('id')) {
        return response()->json([
            'success' => false,
            'message' => 'Référence et/ou identifiant du colis manquant.'
        ], 400);
    }

    // Recherche de tous les colis correspondant à la référence et à l'identifiant fournis
    $colisList = Colis::where('reference_colis', $request->colisId)
                      ->where('id', $request->id)
                      ->get();

    // Vérifier si aucun colis n'a été trouvé
    if ($colisList->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Colis introuvable.'
        ], 404);
    }

    // Vérification des états de chaque colis
    foreach ($colisList as $colis) {
        // Si le colis est déjà chargé, renvoyer une erreur
        if ($colis->etat === 'Chargé') {
            return response()->json([
                'success' => false,
                'message' => "Le colis avec référence {$colis->reference_colis} est déjà Chargé."
            ], 400);
        }
        // Si le colis est déjà arrivé, renvoyer une erreur
        if ($colis->etat === 'Arrivé') {
            return response()->json([
                'success' => false,
                'message' => "Le colis avec référence {$colis->reference_colis} est déjà Arrivé."
            ], 400);
        }
        // Seuls les colis en entrepot peuvent être chargés
        if ($colis->etat !== 'En entrepot') {
            return response()->json([
                'success' => false,
                'message' => "Le colis avec référence {$colis->reference_colis} n'est pas en entrepot. Impossible de le charger."
            ], 400);
        }
    }

    // Mise à jour de l'état de tous les colis validés
    foreach ($colisList as $colis) {
        $colis->etat = 'Chargé';
        $colis->save();
    }

    // Préparation de la réponse avec les informations mises à jour
    $updatedColis = $colisList->map(function ($colis) {
        return [
            'reference'   => $colis->reference_colis,
            'etat'        => $colis->etat,
            'description' => $colis->description,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Les colis ont été chargés avec succès.',
        'colis'   => $updatedColis,
    ]);
}

    
    
public function updateColisDecharge(Request $request)
{
    // Vérifier si 'colisId' est bien présent dans la requête
    if (!$request->has('colisId')) {
        return response()->json(['success' => false, 'message' => 'colisId manquant.'], 400);
    }

    // Recherche de tous les colis correspondant à la référence fournie
    $colisList = Colis::where('reference_colis', $request->colisId)
    ->where('id', $request->id)
    ->get();
    // Vérifier si aucun colis n'a été trouvé
    if ($colisList->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Colis introuvable.'], 404);
    }

    // Vérifier les états de chaque colis
    foreach ($colisList as $colis) {
        // Si le colis est déjà déchargé, renvoyer une erreur
        if ($colis->etat === 'Dechargé') {
            return response()->json([
                'success' => false,
                'message' => "Le colis avec référence {$colis->reference_colis} a déjà été déchargé."
            ], 400);
        }
        // Seul un colis dont l'état est "Fermé" peut être déchargé
        if ($colis->etat !== 'Fermé') {
            return response()->json([
                'success' => false,
                'message' => "Le colis avec référence {$colis->reference_colis} n'est pas encore arrivé (Fermé) et ne peut pas être déchargé."
            ], 400);
        }
    }

    // Mise à jour de l'état de tous les colis validés
    foreach ($colisList as $colis) {
        $colis->etat = 'Dechargé';
        $colis->save();
    }

    // Préparation de la réponse avec les informations mises à jour
    $updatedColis = $colisList->map(function ($colis) {
        return [
            'reference'   => $colis->reference_colis,
            'etat'        => $colis->etat,
            'description' => $colis->description,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Les colis ont été déchargés avec succès.',
        'colis'   => $updatedColis,
    ]);
}

    



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
