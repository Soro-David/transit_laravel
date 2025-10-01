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
use App\Models\Paiement;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


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
        try {
            // --- ÉTAPE 1: Sélection des colis "En entrepot" ---
            $colis = Colis::select(
                'colis.id',
                'colis.reference_colis',
                'colis.quantite_colis',
                'colis.prix_transit_colis',
                'colis.expediteur_id',
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
            ->where('colis.etat', 'En entrepot') // Filtrer par état 'En entrepot'
            ->whereNull('colis.archived_at')     // Exclure les colis archivés
            ->orderBy('colis.created_at', 'desc')
            ->get();

            // --- ÉTAPE 2: Récupération optimisée des paiements ---
            $colisIds = $colis->pluck('id')->unique()->toArray();
            $paiements = Paiement::whereIn('colis_id', $colisIds)
                                ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye'))
                                ->groupBy('colis_id')
                                ->get()
                                ->keyBy('colis_id');

            // --- ÉTAPE 3: Groupement et traitement des données ---
            $colisGrouped = $colis->groupBy('reference_colis');

            $processedData = $colisGrouped->map(function ($group, $reference) use ($paiements) {
                $firstColis = $group->first();
                $quantiteTotale = $group->sum('quantite_colis');
                $prixTotalColis = $group->sum('prix_transit_colis');
                $montantTotalPaye = 0;
                $colisIdsInGroup = $group->pluck('id')->toArray();

                foreach ($colisIdsInGroup as $colisId) {
                    if (isset($paiements[$colisId])) {
                        $montantTotalPaye += $paiements[$colisId]->total_paye;
                    }
                }

                $paymentStatus = 'impaye';
                $tolerance = 0.01;

                if ($montantTotalPaye > 0) {
                    if (abs($prixTotalColis - $montantTotalPaye) < $tolerance) {
                        $paymentStatus = 'paye';
                    } elseif ($montantTotalPaye < $prixTotalColis) {
                        $paymentStatus = 'partiel';
                    }
                }

                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $quantiteTotale,
                    'expediteur_nom' => $firstColis->expediteur_nom,
                    'expediteur_prenom' => $firstColis->expediteur_prenom,
                    'expediteur_tel' => $firstColis->expediteur_tel,
                    'expediteur_agence' => $firstColis->expediteur_agence,
                    'destinataire_nom' => $firstColis->destinataire_nom,
                    'destinataire_prenom' => $firstColis->destinataire_prenom,
                    'destinataire_tel' => $firstColis->destinataire_tel,
                    'destinataire_agence' => $firstColis->destinataire_agence,
                    'etat' => $firstColis->etat,
                    'created_at' => $firstColis->created_at ? $firstColis->created_at->format('d/m/Y H:i') : 'N/A',
                    'payment_status' => $paymentStatus,
                    'prix_total' => $prixTotalColis,
                    'montant_paye' => $montantTotalPaye,
                    'colis_ids' => json_encode($colisIdsInGroup),
                    'first_colis_id' => $firstColis->id
                ];
            })->values();

            // --- ÉTAPE 4: Formatage de la réponse pour DataTables ---
            return DataTables::of($processedData)
                ->addColumn('statut_paiement', function ($row) {
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
                    $reference = $row['reference_colis'];
                    $firstColisId = $row['first_colis_id'];

                    // Définition des URLs pour la clarté
                    $editUrl    = route('colis.valide.edit', ['id' => $firstColisId]);
                    $invoiceUrl = route('colis.valide.edit.invoice', ['id' => $firstColisId]);
                    $deleteUrl  = route('colis.destroy.colis.valide', ['reference' => $reference]);
                    $scanUrl    = route('scan.colis.modifier', $reference);

                    // --- Construction des boutons d'action ---

                    // 1. Bouton "Modifier les informations du colis"
                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modifier les informations du colis groupé">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>';
                                
                    // 2. Bouton "Changer l'état via scan"
                    $scanBtn = '<a href="' . $scanUrl . '" class="btn btn-sm btn-info" title="Modifier l\'état du colis (scan)">
                                    <i class="fas fa-pen"></i>
                               </a>';

                    // 3. Bouton "Enregistrer un paiement"
                    $payBtn = '<button type="button" class="btn btn-sm btn-success pay-btn"
                                        data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                        data-total="' . $row['prix_total'] . '"
                                        data-paid="' . $row['montant_paye'] . '"
                                        data-colis-ids="' . htmlspecialchars($row['colis_ids'], ENT_QUOTES, 'UTF-8') . '"
                                        title="Enregistrer un Paiement">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>';
                    
                    // 4. Bouton "Voir la facture"
                    $invoiceBtn = '<a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Voir la Facture">
                                    <i class="fas fa-file-invoice"></i>
                                </a>';

                    // 5. Bouton "Archiver la référence"
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-reference="' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') . '"
                                            data-url="' . $deleteUrl . '"
                                            title="Archiver la référence">
                                        <i class="fas fa-trash"></i>
                                    </button>';

                    // --- Conteneur Flexbox pour l'alignement ---
                    return '<div style="display: flex; gap: 5px; justify-content: flex-start;">'
                                . $editBtn
                                . $scanBtn
                                . $payBtn
                                . $invoiceBtn
                                . $deleteBtn .
                           '</div>';
                })
                ->rawColumns(['action', 'statut_paiement'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('Erreur dans get_colis_entrepot: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur interne est survenue.'], 500);
        }
    }

    Log::warning("Requête non-AJAX reçue sur get_colis_entrepot");
    abort(404);
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

    // dd( $colisList);
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


public function updateColisCharge(Request $request)
{
    // --- 1. Vérification des paramètres obligatoires ---
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

    // --- 2. Rechercher le(s) colis correspondant(s) ---
    $colisList = Colis::where('reference_colis', $request->colisId)
                      ->where('id', $request->id)
                      ->get();

    if ($colisList->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Aucun colis trouvé avec cette référence et cet identifiant.'
        ], 404);
    }

    // --- 3. Mise à jour des colis trouvés ---
    $messages = [];
    $updatedColis = [];

    foreach ($colisList as $colis) {
        $etatActuel = strtolower(trim($colis->etat));

        if ($etatActuel === 'chargé') {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà Chargé.";
        } elseif (in_array($etatActuel, ['validé', 'en entrepot'])) {
            $colis->etat = 'Chargé';
            $colis->save();

            $updatedColis[] = [
                'id'           => $colis->id,
                'reference'    => $colis->reference_colis,
                'nouvel_etat'  => $colis->etat,
            ];

            // dd($updatedColis);
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été mis à jour en 'Chargé' avec succès.";
        } else {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est dans l'état {$colis->etat} et n'a pas été modifié.";
        }
    }
    
    // --- 4. Réponse finale JSON ---
    return response()->json([
        'success'  => !empty($updatedColis),
        'messages' => $messages,
        'colis'    => $updatedColis,
    ]);
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
