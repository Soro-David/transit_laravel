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
use Illuminate\Support\Facades\Log;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use App\Services\InfobipSmsService;
use App\Services\InfobipEmailService;


class ApmsAngreScanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chargement()
    {
        return view('IPMS_SIMEXCI_ANGRE.scan.chargement');
    }

    public function dechargement()
    {
        return view('IPMS_SIMEXCI_ANGRE.scan.dechargement');
    }

    public function livre()
    {
        return view('IPMS_SIMEXCI_ANGRE.scan.livre');
    }

    public function entrepot()
    {
        return view('IPMS_SIMEXCI_ANGRE.scan.entrepot');
    }



    public function get_colis_entrepot(Request $request)
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
            ->where('etat', 'En entrepot')
            ->where('expediteurs.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
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
    
  
    public function get_colis_decharge(Request $request)
    {
        if ($request->ajax()) {
            try {
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
                ->where('colis.etat', 'Dechargé')
                ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                ->where('colis.mode_transit', 'aerien')
                ->whereNull('colis.archived_at')
                ->orderBy('colis.created_at', 'desc')
                ->get();

                $colisIds = $colis->pluck('id')->unique()->toArray();

                $paiements = Paiement::whereIn('colis_id', $colisIds)
                                    ->select('colis_id', DB::raw('SUM(montant_paye) as total_paye'))
                                    ->groupBy('colis_id')
                                    ->get()
                                    ->keyBy('colis_id'); 

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
                            $paymentStatus = 'paye'; // Totalement payé
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
                        'created_at' => $firstColis->created_at ? $firstColis->created_at->format('d/m/Y H:i') : 'N/A', // Formatage de la date
                        'payment_status' => $paymentStatus,
                        'prix_total' => $prixTotalColis,
                        'montant_paye' => $montantTotalPaye,
                        'colis_ids' => json_encode($colisIdsInGroup),
                        'first_colis_id' => $firstColis->id
                    ];
                })->values();

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


    public function get_colis_livre(Request $request)
    {

        if ($request->ajax()) {
            $colis = Colis::select(
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
            ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'Livré')  // Filtre l'état des colis
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
            ->where('colis.mode_transit', 'aerien')
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

    // Ajax pour récupérer la liste des colis en Charge
    // public function get_colis_charge(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $colis = Colis::select(
    //             'colis.*',  // Sélectionne toutes les colonnes de colis
    //             'expediteurs.nom as nom_expediteur', 
    //             'expediteurs.prenom as prenom_expediteur', 
    //             'expediteurs.tel as tel_expediteur', 
    //             'expediteurs.agence as agence_expedition', 
    //             'destinataires.nom as nom_destinataire', 
    //             'destinataires.prenom as prenom_destinataire', 
    //             'destinataires.tel as tel_destinataire', 
    //             'destinataires.agence as agence_destination',
    //             'colis.etat as etat',
    //             'colis.created_at as created_at'
    //         )
    //         ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
    //         ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
    //         ->where('etat', 'Chargé')  // Filtre l'état des colis
    //         ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
    //         ->get(); 
    //         return DataTables::of($colis)
    //             ->addColumn('action', function ($row) {
    //                 $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis

    //                 return '
    //                     <div class="btn-group">
    //                         <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
    //                             <i class="fas fa-eye"></i>
    //                         </a>
    //                         <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
    //                             <i class="fas fa-credit-card"></i>
    //                         </a>
    //                     </div>
    //                 ';
    //             })
    //             ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
    //             ->make(true);
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->where('etat', 'Chargé')  // Filtre l'état des colis
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
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
                    return '<a href="' . route('ipms_angre_scan.colis.modifier', $row['reference_colis']) . '" 
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

        return view('IPMS_SIMEXCI_ANGRE.scan.edit_scan', compact('colis', 'reference_colis'));
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

        return redirect()->route('ipms_angre_scan.chargement')->with('success', 'État des colis mis à jour avec succès.');
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
        // dd($colisList);
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
        // dd( $updatedColis);

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
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Chargé succès";
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
        // dd( $updatedColis);

    }
    

   



//     public function updateColisDecharge(Request $request, InfobipSmsService $smsService)
// {
//     if (!$request->has('colisIds')) {
//         return response()->json([
//             'success'  => false,
//             'messages' => ['Paramètre manquant : colisIds (liste des colis à décharger)']
//         ], 400);
//     }

//     $colisIds = (array) $request->input('colisIds'); // Liste de colis scannés
//     $agenceCible = 'IPMS-SIMEX-CI Angre 8ème Tranche';

//     try {
//         // Charger les colis avec leurs expéditeurs et destinataires
//         $colisList = Colis::with('expediteur', 'destinataire')
//             ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
//             ->whereIn('colis.id', $colisIds)
//             ->where('destinataires.agence', $agenceCible)
//             ->select('colis.*')
//             ->get();

//         if ($colisList->isEmpty()) {
//             return response()->json([
//                 'success' => false,
//                 'messages' => ["Aucun colis trouvé pour l'agence '{$agenceCible}'."]
//             ], 404);
//         }

//         $messages = [];
//         $updatedColis = [];
//         $expediteursNotifies = []; // Pour éviter d'envoyer plusieurs fois

//         foreach ($colisList as $colis) {
//             if ($colis->etat === 'Déchargé') {
//                 $messages[] = "Colis {$colis->reference_colis} (ID: {$colis->id}) déjà déchargé.";
//                 continue;
//             }

//             if (in_array($colis->etat, ['Fermé', 'Arrivé'])) {
//                 $colis->etat = 'Déchargé';
//                 $colis->save();
//                 $updatedColis[] = [
//                     'id' => $colis->id,
//                     'etat' => $colis->etat,
//                     'reference_colis' => $colis->reference_colis
//                 ];
//                 $messages[] = "Colis {$colis->reference_colis} (ID: {$colis->id}) déchargé.";

//                 // Préparer SMS uniquement si expéditeur valide
//                 if ($colis->expediteur && $colis->expediteur->tel) {
//                     $tel = $colis->expediteur->tel;

//                     // Vérifier si on a déjà envoyé un SMS à ce numéro
//                     if (!in_array($tel, $expediteursNotifies)) {
//                         $nom_expediteur = $colis->expediteur->nom ?? '';
//                         $prenom_expediteur = $colis->expediteur->prenom ?? '';
//                         $agence_dest = $colis->destinataire->agence ?? $agenceCible;

//                         $messageSms = "Bonjour {$nom_expediteur} {$prenom_expediteur}, vos colis ont été déchargés à l'agence {$agence_dest}. AFT IMPORT/EXPORT vous remercie.";

//                         try {
//                             $smsService->sendSms($tel, $messageSms);
//                             $messages[] = "SMS envoyé à l'expéditeur {$tel}.";
//                             $expediteursNotifies[] = $tel; // Marquer comme déjà notifié
//                         } catch (\Exception $e) {
//                             Log::error("Erreur SMS pour {$tel} : " . $e->getMessage());
//                             $messages[] = "Erreur SMS pour {$tel}.";
//                         }
//                     }
//                 }
//             } else {
//                 $messages[] = "Colis {$colis->reference_colis} (ID: {$colis->id}) est dans l'état '{$colis->etat}', non déchargeable.";
//             }
//         }

//         return response()->json([
//             'success' => count($updatedColis) > 0,
//             'messages' => $messages,
//             'colis' => $updatedColis
//         ]);

//     } catch (\Exception $e) {
//         Log::error("Erreur déchargement colis : " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
//         return response()->json([
//             'success' => false,
//             'messages' => ["ERREUR technique : " . $e->getMessage()]
//         ], 500);
//     }
// }

    public function updateColisDecharge(Request $request, InfobipSmsService $smsService)
    {
        // 1. Validation des paramètres d'entrée
        if (!$request->has('colisIds')) {
            return response()->json([
                'success'  => false,
                'messages' => ['Paramètre manquant : colisIds (liste des colis à décharger)']
            ], 400);
        }

        $colisIds = (array) $request->input('colisIds'); // Liste de colis scannés
        $agenceCible = 'IPMS-SIMEX-CI Angre 8ème Tranche'; // Définir l'agence cible

        try {
            // 2. Charger les colis avec leurs expéditeurs et destinataires
            $colisList = Colis::with('expediteur', 'destinataire')
                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                ->whereIn('colis.id', $colisIds)
                ->where('destinataires.agence', $agenceCible)
                ->select('colis.*')
                ->get();

            if ($colisList->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'messages' => ["Aucun colis trouvé pour l'agence '{$agenceCible}' parmi les IDs fournis."]
                ], 404);
            }

            $messages = [];
            $updatedColis = [];
            // Utiliser un tableau associatif pour stocker les expéditeurs à notifier et leur message unique
            $expediteursToNotify = [];

            // 3. Traitement de chaque colis
            foreach ($colisList as $colis) {
                if ($colis->etat === 'Déchargé') {
                    $messages[] = "Colis {$colis->reference_colis} (ID: {$colis->id}) est déjà déchargé.";
                    continue; // Passer au colis suivant
                }

                if (in_array($colis->etat, ['Fermé', 'Arrivé'])) {
                    $colis->etat = 'Déchargé';
                    $colis->save(); // Sauvegarde l'état mis à jour
                    $updatedColis[] = [
                        'id' => $colis->id,
                        'etat' => $colis->etat,
                        'reference_colis' => $colis->reference_colis
                    ];
                    $messages[] = "Colis {$colis->reference_colis} (ID: {$colis->id}) a été déchargé.";

                    // Préparer les données de l'expéditeur pour notification unique
                    if ($colis->expediteur && $colis->expediteur->tel) {
                        $tel = $colis->expediteur->tel;
                        $nom_expediteur = $colis->expediteur->nom ?? '';
                        $prenom_expediteur = $colis->expediteur->prenom ?? '';
                        $agence_dest = $colis->destinataire->agence ?? $agenceCible;

                        // Stocker le message par numéro de téléphone unique
                        if (!isset($expediteursToNotify[$tel])) {
                            $expediteursToNotify[$tel] = "Bonjour {$nom_expediteur} {$prenom_expediteur}, vos colis ont été déchargés à l'agence Angre 8ème Tranche (Abidjan, Côte d'Ivoire), AFT vous remercie de votre confiance. Suivi: https://aft-app.com .";
                        }
                    
                    }
                } else {
                    $messages[] = "Colis {$colis->reference_colis} (ID: {$colis->id}) est dans l'état '{$colis->etat}', non déchargeable.";
                }
            }

            // 4. Envoi des SMS consolidés (un SMS par expéditeur unique)
            foreach ($expediteursToNotify as $tel => $messageSms) {
                try {
                    $smsService->sendSms($tel, $messageSms);
                    $messages[] = "SMS envoyé à l'expéditeur {$tel}.";
                } catch (\Exception $e) {
                    Log::error("Erreur SMS pour {$tel} lors du déchargement en lot : " . $e->getMessage());
                    $messages[] = "ERREUR : L'envoi du SMS à {$tel} a échoué.";
                }
            }

            // 5. Retourne la réponse JSON
            return response()->json([
                'success'  => count($updatedColis) > 0, // Succès si au moins un colis a été mis à jour
                'messages' => $messages,
                'colis'    => $updatedColis
            ]);

        } catch (\Exception $e) {
            // Gérer toutes les exceptions inattendues
            Log::error("Erreur inattendue lors du déchargement des colis : " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'messages' => ["ERREUR technique : Une erreur est survenue lors du traitement. Veuillez réessayer. Détails: " . $e->getMessage()]
            ], 500);
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
        if ($colis->etat === 'Livré') {
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Livré succès.";
        } elseif ($colis->etat === 'Dechargé') {
            // Modifier l'état du colis en "En entrepot"
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
    // dd( $updatedColis);

}

public function liste_bateau()
{
    // Récupérer uniquement les bateaux non récupérés
    $bateaux = Bateaux::select('id', 'reference_bateau', 'date_arriver', 'reference_conteneur')
                      ->where('recuperer', '!=', 'oui') // Exclure les bateaux déjà récupérés
                      ->get();

    return view('IPMS_SIMEXCI.bateau.liste_bateau', compact('bateaux'));
}

public function validerBateau(Request $request)
{
    // Validation des données entrantes
    $request->validate([
        'reference_conteneur' => 'required|string',
    ]);

    try {
        // Utilisation d'une transaction pour garantir la cohérence des mises à jour
        return DB::transaction(function () use ($request) {
            $bateau = Bateaux::where('reference_bateau', $request->reference_bateau)->first();

            // dd($bateau);
            if (!$bateau) {
                throw new Exception('🚢 Bateau non trouvé.');
            }

            // Vérifier si le bateau est déjà récupéré
            if ($bateau->recuperer === 'oui') {
                throw new Exception('⚠️ Ce bateau a déjà été récupéré.');
            }

            $referenceConteneur = $bateau->reference_conteneur;

            // Récupérer tous les colis liés à ce conteneur
            $colis = Colis::where('reference_contenaire', $referenceConteneur)->get();

            if ($colis->isEmpty()) {
                throw new Exception('📦 Aucun colis trouvé pour ce conteneur.');
            }

            // Mise à jour en masse des colis récupérés
            Colis::where('reference_contenaire', $referenceConteneur)->update(['recup' => 'oui']);

            // Mettre à jour le champ "recuperer" du bateau
            $bateau->update(['recuperer' => 'oui']);

            return response()->json([
                'success' => true,
                'message' => '✅ Tous les colis et le bateau ont été récupérés avec succès.',
            ]);
        });

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400);
    }
}

public function get_bateau(Request $request)
{
    if ($request->ajax()) {
        $bateaux = Bateaux::select(
            'reference_bateau',
            'created_at as date_depart', // Création comme date de départ
            'date_arriver'
        )->where('agence_destination', 'IPMS-SIMEX-CI')
        ->where('recuperer', '=', 'oui')
        ->get();

        return DataTables::of($bateaux)
            ->editColumn('date_depart', function ($row) {
                return $row->date_depart ? \Carbon\Carbon::parse($row->date_depart)->format('d/m/Y H:i') : 'N/A';
            })
            ->editColumn('date_arriver', function ($row) {
                return $row->date_arriver ? \Carbon\Carbon::parse($row->date_arriver)->format('d/m/Y H:i') : 'N/A';
            })
            ->make(true);
    }
}


}
