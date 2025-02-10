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


class ChineScanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chargement()
    {
        return view('AGENCE_CHINE.scan.chargement');
    }

    public function dechargement()
    {
        return view('AGENCE_CHINE.scan.dechargement');
    }
    public function entrepot()
    {
        return view('AGENCE_CHINE.scan.entrepot');
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
            ->where('expediteurs.agence', 'Agence de Chine')
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
            ->where('expediteurs.agence', 'Agence de Chine')
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
            ->where('expediteurs.agence', 'Agence de Chine')
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
    
       // Vérifier si l'état est déjà "Chargé", "Déchargé", "En entrepôt" ou "Fermé"
        if (in_array($colis->etat, ['Chargé', 'Déchargé', 'En entrepot', 'Fermé'])) {
            return response()->json(['success' => false, 'message' => "Le colis est déjà mis en entrepôt."], 400);
        }
    
        // Vérifier si l'état est "En entrepôt" avant de le marquer comme "Chargé"
        if ($colis->etat !== 'Validé') {
            return response()->json([
                'success' => false,
                'message' => "Le colis n'est pas encore validé. Impossible de le mettre en entrepôt.",
            ], 400);
        }
    
        // Modifier l'état du colis en "Chargé"
        $colis->etat = 'En entrepot';
    
        // Sauvegarder les modifications dans la base de données
        $colis->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Le colis a été mis en entrepôt avec succès.',
            'colis' => [
                'reference' => $colis->reference_colis,
                'etat' => $colis->etat,
                'description' => $colis->description,
            ],
        ]);
    }
    
    public function updateColisEntrepot(Request $request)
    {
        
        if (!$request->has('colisId') || !$request->has('id')) {
            $missingParams = [];
            if (!$request->has('colisId')) {
                $missingParams[] = 'colisId';
            }
            if (!$request->has('id')) {
                $missingParams[] = 'id';
            }
            return response()->json([
                'success'  => false,
                'messages' => [implode(" et ", $missingParams) . ' manquant(s).']
            ], 400);
        }
    
    
        // Rechercher tous les colis correspondant à la référence et à l'identifiant fournis
        $colisList = Colis::where('reference_colis', $request->colisId)
                          ->where('id', $request->id)
                        //   ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                          ->get();
    
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
                    'etat'        => $colis->etat,
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
        dd( $updatedColis);

    }
    
    // Fonction Ajax pour le Scan chargement
    // {{ route("scan.get.colis.charge") }}
    public function updateColisCharge(Request $request)
    {
        
        if (!$request->has('colisId') || !$request->has('id')) {
            $missingParams = [];
            if (!$request->has('colisId')) {
                $missingParams[] = 'colisId';
            }
            if (!$request->has('id')) {
                $missingParams[] = 'id';
            }
            return response()->json([
                'success'  => false,
                'messages' => [implode(" et ", $missingParams) . ' manquant(s).']
            ], 400);
        }
    
    
        // Rechercher tous les colis correspondant à la référence et à l'identifiant fournis
        $colisList = Colis::where('reference_colis', $request->colisId)
                          ->where('id', $request->id)
                        //   ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                          ->get();
    
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
            if ($colis->etat === 'Chargé') {
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà Chargé.";
            } elseif ($colis->etat === 'En entrepot') {
                // Modifier l'état du colis en "En entrepot"
                $colis->etat = 'Chargé';
                $colis->save();
                $updatedColis[] = [
                    'etat'        => $colis->etat,
                ];
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Chargé succès.";
            } else {
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas encore mis en Entrepot. Impossible de le mettre chargé.";
            }
        }
    
        return response()->json([
            'success'  => !empty($updatedColis),
            'messages' => $messages,
            'colis'    => $updatedColis,
        ]);
        dd( $updatedColis);

    }
    
    
public function updateColisDecharge(Request $request)
{
        
    if (!$request->has('colisId') || !$request->has('id')) {
        $missingParams = [];
        if (!$request->has('colisId')) {
            $missingParams[] = 'colisId';
        }
        if (!$request->has('id')) {
            $missingParams[] = 'id';
        }
        return response()->json([
            'success'  => false,
            'messages' => [implode(" et ", $missingParams) . ' manquant(s).']
        ], 400);
    }


    // Rechercher tous les colis correspondant à la référence et à l'identifiant fournis
    $colisList = Colis::where('reference_colis', $request->colisId)
                      ->where('id', $request->id)
                    //   ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                      ->get();

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
        if ($colis->etat === 'Dechargé') {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà Déchargé.";
        } elseif ($colis->etat === 'Fermé') {
            // Modifier l'état du colis en "En entrepot"
            $colis->etat = 'Déchargé';
            $colis->save();
            $updatedColis[] = [
                'etat'        => $colis->etat,
            ];
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été déchargé succès.";
        } else {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas encore Arrivé. Impossible de le mettre déchargé.";
        }
    }

    return response()->json([
        'success'  => !empty($updatedColis),
        'messages' => $messages,
        'colis'    => $updatedColis,
    ]);
    dd( $updatedColis);

}
}
