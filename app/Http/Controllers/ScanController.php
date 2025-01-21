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


class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chargement()
    {
        return view('admin.scan.chargement');
    }

    public function dechargement()
    {
        return view('admin.scan.dechargement');
    }
    public function entrepot()
    {
        return view('admin.scan.entrepot');
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
            ->where('etat', 'Dechargé')  // Filtre l'état des colis
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
// Fonction Ajax pour le Scan En entrepot

public function getColisEntrepot(Request $request)
{
    // Vérifier si colisId est bien présent
    if (!$request->has('colisId')) {
        return response()->json(['success' => false, 'message' => 'colisId manquant.'], 400);
    }

    // Recherche du colis en fonction de la référence
    $colis = Colis::where('reference_colis', $request->colisId)->first();

    // Vérifier si le colis existe
    if (!$colis) {
        return response()->json(['success' => false, 'message' => 'Colis introuvable.'], 404);
    }

    // Vérifier si l'état est déjà "Chargé"
    if ($colis->etat === 'Chargé') {
        return response()->json(['success' => false, 'message' => "Le colis est déjà  Chargé"], 400);
    }

    // Vérifier si l'état est "En entrepôt" avant de le marquer comme "Chargé"
    if ($colis->etat !== 'En entrepôt') {
        return response()->json([
            'success' => false,
            'message' => "Le colis n'est pas en entrepôt. Impossible de le Chargé.",
        ], 400);
    }

    // Modifier l'état du colis en "Chargé"
    $colis->etat = 'Chargé';

    // Sauvegarder les modifications dans la base de données
    $colis->save();

    return response()->json([
        'success' => true,
        'message' => 'Le colis a été Chargé avec succès.',
        'colis' => [
            'reference' => $colis->reference_colis,
            'etat' => $colis->etat,
            'description' => $colis->description,
        ],
    ]);
}

// Fonction Ajax pour le Scan chargement
// {{ route("scan.get.colis.charge") }}
public function getColisCharge(Request $request)
{
    // Vérifier si colisId est bien présent
    if (!$request->has('colisId')) {
        return response()->json(['success' => false, 'message' => 'colisId manquant.'], 400);
    }

    // Recherche du colis en fonction de la référence
    $colis = Colis::where('reference_colis', $request->colisId)->first();

    // Vérifier si le colis existe
    if (!$colis) {
        return response()->json(['success' => false, 'message' => 'Colis introuvable.'], 404);
    }

    // Vérifier si l'état est déjà "Déchargé"
    if ($colis->etat === 'Déchargé') {
        return response()->json(['success' => false, 'message' => "Le colis est déjà Déchargé."], 400);
    }

    // Vérifier si l'état est déjà "Arrivé"
    if ($colis->etat === 'Arrivé') {
        return response()->json(['success' => false, 'message' => "Le colis est déjà Arrivé."], 400);
    }

    // Vérifier si l'état est "Chargé" avant de le marquer comme "Déchargé"
    if ($colis->etat !== 'Chargé') {
        return response()->json([
            'success' => false,
            'message' => "Le colis n'est pas chargé. Impossible de le décharger.",
        ], 400);
    }

    // Modifier l'état du colis en "Déchargé"
    $colis->etat = 'Déchargé';

    // Sauvegarder les modifications dans la base de données
    $colis->save();

    return response()->json([
        'success' => true,
        'message' => 'Le colis a été Déchargé avec succès.',
        'colis' => [
            'reference' => $colis->reference_colis,
            'etat' => $colis->etat,
            'description' => $colis->description,
        ],
    ]);
}


// Fonction Ajax pour le Scan dechargement
public function getColisDecharge(Request $request)
{
    // Vérifier si colisId est bien présent
    if (!$request->has('colisId')) {
        return response()->json(['success' => false, 'message' => 'colisId manquant.'], 400);
    }

    // Recherche du colis en fonction de la référence
    $colis = Colis::where('reference_colis', $request->colisId)->first();

    // Vérifier si le colis existe
    if (!$colis) {
        return response()->json(['success' => false, 'message' => 'Colis introuvable.'], 404);
    }

    // Vérifier si l'état est déjà "Arrivé"
    if ($colis->etat === 'Arrivé') {
        return response()->json(['success' => false, 'message' => "Le colis est Arrivé"], 400);
    }

    // Vérifier si l'état est "Déchargé" avant de marquer comme "Arrivé"
    if ($colis->etat !== 'Déchargé') {
        return response()->json([
            'success' => false,
            'message' => "Le colis n'est encore Arrivé.",
        ], 400);
    }

    // Modifier l'état du colis en "Arrivé"
    $colis->etat = 'Arrivé';

    // Sauvegarder les modifications dans la base de données
    $colis->save();

    return response()->json([
        'success' => true,
        'message' => 'Le colis a été Arrivé avec succès.',
        'colis' => [
            'reference' => $colis->reference_colis,
            'etat' => $colis->etat,
            'description' => $colis->description,
        ],
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
