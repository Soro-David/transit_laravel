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
                'colis.*', 
                'colis.reference_colis',
                'colis.quantite_colis',
                'expediteurs.nom as nom_expediteur', 
                'expediteurs.prenom as prenom_expediteur', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as agence_expedition', 
                'destinataires.nom as nom_destinataire', 
                'destinataires.prenom as prenom_destinataire', 
                'destinataires.tel as destinataire_tel', 
                'destinataires.agence as agence_destination',
                'colis.created_at as created_at'
            )
            ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'En entrepot')
            ->get(); 
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->sum('quantite_colis'),
                    'expediteur_nom' => $group->first()->nom_expediteur,
                    'expediteur_prenom' => $group->first()->prenom_expediteur,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->agence_expedition, 
                    'destinataire_nom' => $group->first()->nom_destinataire,
                    'destinataire_prenom' => $group->first()->prenom_destinataire,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->agence_destination, 
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();
    
            return DataTables::of($colisWithCount)->make(true);
        }
    }
    
    

    public function get_colis_decharge(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*', 
                'expediteurs.nom as nom_expediteur', 
                'expediteurs.prenom as prenom_expediteur', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as agence_expedition', 
                'destinataires.nom as nom_destinataire', 
                'destinataires.prenom as prenom_destinataire', 
                'destinataires.tel as destinataire_tel', 
                'destinataires.agence as agence_destination',
                'colis.created_at as created_at'
            )
            ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'Dechargé')
            ->get(); 
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->sum('quantite_colis'),
                    'expediteur_nom' => $group->first()->nom_expediteur,
                    'expediteur_prenom' => $group->first()->prenom_expediteur,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->agence_expedition, 
                    'destinataire_nom' => $group->first()->nom_destinataire,
                    'destinataire_prenom' => $group->first()->prenom_destinataire,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->agence_destination, 
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();
    
            return DataTables::of($colisWithCount)->make(true);
        }
    }


    // Affichage du formulaire
    public function modifier_colis($reference_colis)
    {
        $colis = Colis::where('reference_colis', $reference_colis)->get();

        if ($colis->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun colis trouvé pour cette référence.');
        }

        return view('admin.scan.edit_scan', compact('colis', 'reference_colis'));
    }

    public function update_colis_etat(Request $request)
    {
        $request->validate([
            'reference_colis' => 'required|string',
            'etat' => 'required|string',
        ]);

        // Mise à jour des colis ayant la même référence
        Colis::where('reference_colis', $request->reference_colis)
            ->update(['etat' => $request->etat]);

        return redirect()->route('scan.chargement')->with('success', 'État des colis mis à jour avec succès.');
    }



    public function get_colis_charge(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*', 
                'expediteurs.nom as nom_expediteur', 
                'expediteurs.prenom as prenom_expediteur', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as agence_expedition', 
                'destinataires.nom as nom_destinataire', 
                'destinataires.prenom as prenom_destinataire', 
                'destinataires.tel as destinataire_tel', 
                'destinataires.agence as agence_destination',
                'colis.created_at as created_at'
            )
            ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'Chargé') 
            ->get(); 

            $colisGrouped = $colis->groupBy('reference_colis');

            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->sum('quantite_colis'),
                    'expediteur_nom' => $group->first()->nom_expediteur,
                    'expediteur_prenom' => $group->first()->prenom_expediteur,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->agence_expedition, 
                    'destinataire_nom' => $group->first()->nom_destinataire,
                    'destinataire_prenom' => $group->first()->prenom_destinataire,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->agence_destination, 
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();

            return DataTables::of($colisWithCount)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '<a href="' . route('scan.colis.modifier', $row['reference_colis']) . '" 
                                class="btn btn-warning btn-sm" title="Modifier état du colis">
                                <i class="fas fa-edit"></i>
                            </a>';
                    })
                ->rawColumns(['action'])
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



// Fonction Ajax pour le Scan En entrepot

public function updateColisEntrepot(Request $request)
{
    // Vérification des paramètres nécessaires
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

    // Recherche des colis correspondants à la référence et à l'identifiant
    $colisList = Colis::where('reference_colis', $request->colisId)
                      ->where('id', $request->id)
                      ->get();

    // Vérifier si des colis ont été trouvés
    if ($colisList->isEmpty()) {
        return response()->json([
            'success' => false,
            'messages' => ['Aucun colis trouvé avec cette référence et cet identifiant.']
        ], 404);
    }

    // Initialisation des messages et des colis mis à jour
    $messages = [];
    $updatedColis = [];

    // Parcours des colis trouvés
    foreach ($colisList as $colis) {
        if ($colis->etat === 'En entrepot') {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été mis en entrepôt avec succès.";
        } elseif ($colis->etat === 'Validé') {
            // Modification de l'état du colis en "En entrepot"
            $colis->etat = 'En entrepot';
            $colis->save();
            $updatedColis[] = [
                'reference_colis' => $colis->reference_colis,
                'etat'            => $colis->etat,
            ];
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été mis en entrepôt avec succès.";
        } else {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas encore validé. Impossible de le mettre en entrepôt.";
        }
    }

    // Retourner la réponse JSON avec les messages et colis mis à jour
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
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Chargé avec succès.";
        } elseif ($colis->etat === 'Validé') {
            $colis->etat = 'Chargé';
            $colis->save();
            $updatedColis[] = [
                'etat'        => $colis->etat,
            ];
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Chargé avec succès.";
        } else {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Chargé avec succès.";
        }
    }

    return response()->json([
        'success'  => !empty($updatedColis),
        'messages' => $messages,
        'colis'    => $updatedColis,
    ]);
    // dd( $updatedColis);

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
        $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été déchargé succès.";
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

}


}
