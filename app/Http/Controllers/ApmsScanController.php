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
use App\Services\InfobipService;
use Illuminate\Support\Facades\Log;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;



class ApmsScanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chargement()
    {
        return view('IPMS_SIMEXCI.scan.chargement');
    }

    public function dechargement()
    {
        return view('IPMS_SIMEXCI.scan.dechargement');
    }
    public function entrepot()
    {
        return view('IPMS_SIMEXCI.scan.entrepot');
    }




    public function get_colis_entrepot(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',
                'expediteurs.nom as nom_expediteur',
                'expediteurs.prenom as prenom_expediteur',
                'expediteurs.tel as expediteur_tel',
                'destinataires.nom as nom_destinataire',
                'destinataires.prenom as prenom_destinataire',
                'destinataires.tel as destinataire_tel',
                'destinataires.agence as agence_destination',
                'colis.created_at as created_at'
            )
            ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'En entrepot')
            ->where('colis.mode_transit', 'maritime')
            ->where('destinataires.agence', 'IPMS-SIMEX-CI')
            ->get();
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                $firstColis = $group->first(); // Get the first Colis object from the group
    
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'nom_expediteur' => $firstColis->nom_expediteur,
                    'prenom_expediteur' => $firstColis->prenom_expediteur,
                    'expediteur_tel' => $firstColis->expediteur_tel,
                    'nom_destinataire' => $firstColis->nom_destinataire,
                    'prenom_destinataire' => $firstColis->prenom_destinataire,
                    'destinataire_tel' => $firstColis->destinataire_tel,
                    'destination_agence' => $firstColis->agence_destination,
                    'created_at' => $firstColis->created_at ? $firstColis->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();
    
            return DataTables::of($colisWithCount)->make(true);
        }
    }


    // public function get_colis_decharge(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $colis = Colis::select(
    //             'colis.reference_colis as reference_colis',
    //             'expediteurs.nom as expediteur_nom', 
    //             'expediteurs.prenom as expediteur_prenom', 
    //             'expediteurs.tel as expediteur_tel', 
    //             'expediteurs.agence as expediteur_agence', 
    //             'destinataires.nom as destinataire_nom', 
    //             'destinataires.prenom as destinataire_prenom', 
    //             'destinataires.agence as destinataire_agence', 
    //             'destinataires.tel as destinataire_tel',
    //             'colis.etat as etat',
    //             'colis.created_at as created_at'
    //         )
    //         ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
    //         ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
    //         ->where('colis.etat', 'Dechargé')
    //         ->where('colis.mode_transit', 'maritime')
    //         ->where('destinataires.agence', 'IPMS-SIMEX-CI')
    //         ->get(); 
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
    //                 'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
    //                 'colis' => $group
    //             ];
    //         })->values();
    
    //         return DataTables::of($colisWithCount)->make(true);
    //     }
    // }

    public function get_colis_decharge(Request $request)
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
                ->where('colis.etat', 'Dechargé')
                ->where('colis.mode_transit', 'maritime')
                ->where('destinataires.agence', 'IPMS-SIMEX-CI')
                ->whereNull('colis.archived_at') // Exclure les colis archivés
                ->orderBy('colis.created_at', 'desc') // Optionnel: trier
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

                        $invoiceUrl = route('ipms_colis.valide.edit.invoice', ['id' => $firstColisId]); // Route pour la facture (utilise l'ID)

                      

                      

                        $invoiceBtn = '<a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Voir la Facture">
                                        <i class="fas fa-file-invoice"></i>
                                       </a>';

                        return '<div class="action-buttons-container">'
                               . $invoiceBtn
                               . '</div>';
                    })
                    ->rawColumns(['action', 'statut_paiement'])
                    ->make(true); 

                } catch (\Exception $e) {
                    dd($e->getMessage());
                    Log::error('Erreur dans get_colis_decharge: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response()->json(['error' => 'Une erreur interne est survenue.'], 500);
                }
        }

        Log::warning("Requête non-AJAX reçue sur get_colis_valide");
        abort(404); 
    }
    

    // Ajax pour récupérer la liste des colis en Charge
    // public function get_colis_charge(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $colis = Colis::select(
    //             'colis.*', 
    //             'expediteurs.nom as nom_expediteur', 
    //             'expediteurs.prenom as prenom_expediteur', 
    //             'expediteurs.tel as expediteur_tel', 
    //             'expediteurs.agence as agence_expedition', 
    //             'destinataires.nom as nom_destinataire', 
    //             'destinataires.prenom as prenom_destinataire', 
    //             'destinataires.tel as destinataire_tel', 
    //             'destinataires.agence as agence_destination',
    //             'colis.etat as etat',
    //             'colis.created_at as created_at'
    //         )
    //         ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
    //         ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
    //         ->where('etat', 'Chargé') 
    //         ->where('destinataires.agence', 'IPMS-SIMEX-CI')
    //         ->get(); 
    
    //         $colisGrouped = $colis->groupBy('reference_colis');
    
    //         $colisWithCount = $colisGrouped->map(function ($group, $reference) {
    //             return [
    //                 'reference_colis' => $reference,
    //                 'nombre_de_colis' => $group->count(),
    //                 'expediteur_nom' => $group->first()->nom_expediteur,
    //                 'expediteur_prenom' => $group->first()->prenom_expediteur,
    //                 'expediteur_tel' => $group->first()->expediteur_tel,
    //                 'expediteur_agence' => $group->first()->agence_expedition, 
    //                 'destinataire_nom' => $group->first()->nom_destinataire,
    //                 'destinataire_prenom' => $group->first()->prenom_destinataire,
    //                 'destinataire_tel' => $group->first()->destinataire_tel,
    //                 'destinataire_agence' => $group->first()->agence_destination, 
    //                 'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
    //                 'colis' => $group
    //             ];
    //         })->values();
    
    //         return DataTables::of($colisWithCount)->make(true);
    //     }
    // }

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
            ->where('destinataires.agence', 'IPMS-SIMEX-CI')
            ->get(); 

            $colisGrouped = $colis->groupBy('reference_colis');

            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
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
                    return '<a href="' . route('ipms_scan.colis.modifier', $row['reference_colis']) . '" 
                                class="btn btn-warning btn-sm" title="Modifier état du colis">
                                <i class="fas fa-edit"></i>
                            </a>';
                    })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    
        // Affichage du formulaire
    public function modifier_colis($reference_colis)
    {
        $colis = Colis::where('reference_colis', $reference_colis)->get();

        if ($colis->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun colis trouvé pour cette référence.');
        }

        return view('IPMS_SIMEXCI.scan.edit_scan', compact('colis', 'reference_colis'));
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

        return redirect()->route('ipms_scan.chargement')->with('success', 'État des colis mis à jour avec succès.');
    }

    public function get_colis_livre(Request $request)
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
                'colis.etat as etat',
                'colis.created_at as created_at'
            )
            ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'Livré') 
            ->where('destinataires.agence', 'IPMS-SIMEX-CI')
            ->get(); 
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
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


    public function livre()
    {
        return view('IPMS_SIMEXCI.scan.livre');
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
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été mis en entrepôt avec succès.";
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

    }
    
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
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Chargé succès.";
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


public function updateColisDecharge(Request $request) // Nom de variable standardisé
{
    // dd($request->all()); // Debug pour voir les données reçues
    if (!$request->has('colisId') || !$request->has('id')) {
        $missingParams = [];
        if (!$request->has('colisId')) $missingParams[] = 'Référence (colisId)';
        if (!$request->has('id')) $missingParams[] = 'Identifiant (id)';
        return response()->json([
            'success'  => false,
            'messages' => ['Paramètre(s) manquant(s) : ' . implode(" et ", $missingParams)] // Message plus précis
        ], 400); // Bad Request
    }

    $referenceColis = $request->input('colisId');
    $identifiantColis = $request->input('id');
    $agenceCible = 'IPMS-SIMEX-CI'; // Nom de l'agence cible ->where('destinations.agence', 'IPMS-SIMEX-CI')

    try {
        $colis = Colis::with('expediteur', 'destinataire') // Pré-charger les relations pour le SMS
                    ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                    ->where('colis.reference_colis', $referenceColis)
                    ->where('colis.id', $identifiantColis)
                    ->where('destinataires.agence', $agenceCible)
                    ->select('colis.*') // Sélectionner uniquement les colonnes de colis après la jointure
                    ->firstOrFail(); // Lance une exception ModelNotFoundException si non trouvé

        $messages = [];
        $updated = false; // Flag pour savoir si une mise à jour a eu lieu

        if ($colis->etat === 'Dechargé') {
            // Cas : Déjà déchargé
            $messages[] = "SUCCÈS : Le colis Réf {$colis->reference_colis} (ID: {$colis->id}) a été déchargé.";

        } elseif ($colis->etat === 'Fermé' || $colis->etat === 'Arrivé') { // États permettant le déchargement
            // Cas : Peut être déchargé
            $colis->etat = 'Déchargé';
            $colis->save(); // Sauvegarder le changement d'état
            $updated = true; // Marquer qu'une mise à jour a été effectuée
            $messages[] = "SUCCÈS : Le colis Réf {$colis->reference_colis} (ID: {$colis->id}) a été déchargé.";

            // 4. Envoyer le SMS UNIQUEMENT si la mise à jour a été faite
            if ($colis->expediteur && $colis->expediteur->tel) {
                $numero_expediteur = $colis->expediteur->tel; // !! Utilisation du VRAI numéro !!
                $nom_expediteur = $colis->expediteur->nom ?? '';
                $prenom_expediteur = $colis->expediteur->prenom ?? '';
                $agence_dest = $colis->destinataire->agence ?? $agenceCible; // Nom de l'agence

                $messageSms = "Bonjour {$nom_expediteur} {$prenom_expediteur}, votre colis (Réf: {$colis->reference_colis}) a bien été déchargé à l'agence {$agence_dest}. AFT IMPORT/EXPORT vous remercie.";

                try {
                    // Utilisation du service injecté
                    $response = $this->infobipService->sendSms($numero_expediteur, $messageSms);
                    Log::info('SMS de déchargement envoyé à ' . $numero_expediteur . ': ' . json_encode($response));
                    $messages[] = "SMS de notification envoyé à l'expéditeur.";
                } catch (\Exception $e) {
                    Log::error("Erreur lors de l'envoi du SMS de déchargement pour colis ID {$colis->id} à {$numero_expediteur}: " . $e->getMessage());
                    // Informer l'utilisateur sans bloquer la réponse principale
                    $messages[] = "ATTENTION : Erreur lors de l'envoi du SMS de notification à l'expéditeur.";
                }
            } else {
                 Log::warning("Impossible d'envoyer le SMS de déchargement pour colis ID {$colis->id}: informations expéditeur ou téléphone manquantes.");
                 $messages[] = "ATTENTION : Informations expéditeur/téléphone manquantes, SMS non envoyé.";
            }

        } else {
            // Cas : État ne permettant pas le déchargement
            $messages[] = "ERREUR : Le colis Réf {$colis->reference_colis} (ID: {$colis->id}) est dans l'état '{$colis->etat}'. Il ne peut pas être déchargé directement.";
            // Pas de mise à jour, pas de SMS
        }

        // 5. Retourner la réponse JSON
        return response()->json([
            'success'  => $updated, // Vrai seulement si l'état a été changé en 'Déchargé'
            'messages' => $messages, // Tous les messages collectés
            'colis'    => $updated ? [['id' => $colis->id, 'etat' => $colis->etat]] : [] // Renvoyer l'info si mis à jour
        ]);

    } catch (ModelNotFoundException $e) {
        // Cas : Colis non trouvé avec ces critères (ID, Réf, Agence)
         Log::warning("Tentative de déchargement échouée: Colis non trouvé ou pas pour l'agence '{$agenceCible}'. Ref: {$referenceColis}, ID: {$identifiantColis}");
        return response()->json([
            'success' => false,
            // Utiliser 'messages' (pluriel et tableau) pour la cohérence avec le JS
            'messages' => ["ERREUR : Aucun colis trouvé avec la Réf '{$referenceColis}' (ID: {$identifiantColis}) pour l'agence '{$agenceCible}'."]
        ], 404); // Not Found

    } catch (\Exception $e) {
        // Cas : Autre erreur inattendue (DB, etc.)
        Log::error("Erreur inattendue lors du déchargement du colis Ref: {$referenceColis}, ID: {$identifiantColis}: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'messages' => ["ERREUR : Une erreur technique est survenue lors du traitement. Veuillez réessayer."]
        ], 500); // Internal Server Error
    }
} 
   

public function updateColisLivre(Request $request)
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

    if ($colisList->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Aucun colis trouvé avec cette référence et cet identifiant.'
        ], 404);
    }

    $messages = [];
    $updatedColis = [];

    foreach ($colisList as $colis) {
        if ($colis->etat === 'Livré') {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Livré succès";
        } elseif ($colis->etat === 'Dechargé') {
            $colis->etat = 'Livré';
            $colis->save();
            $updatedColis[] = [
                'etat'        => $colis->etat,
            ];
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Livré succès.";
        } else {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas encore Déchargé. Impossible de le mettre Livré.";
        }
    }

    return response()->json([
        'success'  => !empty($updatedColis),
        'messages' => $messages,
        'colis'    => $updatedColis,
    ]);

}
}
