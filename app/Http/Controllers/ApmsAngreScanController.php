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
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
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
            ->where('etat', 'Dechargé')  // Filtre l'état des colis
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
            ->where('colis.mode_transit', 'aerien')
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
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
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
    //         ->where('etat', 'Dechargé')  // Filtre l'état des colis
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

    public function get_colis_dump(Request $request)
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
                ->where('etat', 'Dechargé')  // Filtre l'état des colis
                ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
                ->where('colis.mode_transit', 'aerien')
                ->where('colis.recup', 'oui')
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
                ->addColumn('action', function ($row) {
                    $firstColis = $row['colis']->first();
                    $editUrl = route('ipms_colis.valide.edit', ['id' => $firstColis->id]);
                    // $deleteUrl = route('ipms_colis.destroy.colis.valide', ['id' => $firstColis->id]);
                    $invoiceUrl = route('ipms_colis.valide.edit.invoice', ['id' => $firstColis->id]);
    
                    return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modifier" data-bs-target="#modifModal">
                                <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                            </a>
                        </div> 
                        <div class="btn-group">
                            <a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Facture" data-bs-target="#modifModal">
                                <i class="fas fa-file-invoice" style="font-size: 15px;"></i>
                            </a>
                        </div> 
                         
                    </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
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
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence, 
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence, 
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();
    
            return DataTables::of($colisWithCount)->make(true);
        }
    }


    // public function editInvoice($id)
    // {
        
        
    //     $colis_principal = Colis::find($id);
    //     $colis_info = Colis::with(['expediteur', 'destinataire'])
    //                             ->select(
    //                                 'colis.reference_colis',
    //                                 'expediteurs.nom as expediteur_nom',
    //                                 'expediteurs.prenom as expediteur_prenom',
    //                                 'expediteurs.tel as expediteur_tel',
    //                                 'destinataires.nom as destinataire_nom',
    //                                 'destinataires.prenom as destinataire_prenom',
    //                                 'destinataires.tel as destinataire_tel'
    //                             )
    //                             ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
    //                             ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
    //                             ->find($id);

    //     if (!$colis_principal) {
    //         return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
    //     }

    //     $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

    //     if ($colis->isEmpty()) {
    //         return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
    //     }

    //     return view('IPMS_SIMEXCI.invoice.edit', compact('colis','colis_info'));
    // }




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
            ->where('destinataires.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
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
                ->rawColumns(['action'])
                ->make(true);
        }
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
        // dd( $updatedColis);

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
    

    // public function updateColisDecharge(Request $requestuse, InfobipService $infobipService)
    // {
            
    //     if (!$request->has('colisId') || !$request->has('id')) {
    //         $missingParams = [];
    //         if (!$request->has('colisId')) {
    //             $missingParams[] = 'colisId';
    //         }
    //         if (!$request->has('id')) {
    //             $missingParams[] = 'id';
    //         }
    //         return response()->json([
    //             'success'  => false,
    //             'messages' => [implode(" et ", $missingParams) . ' manquant(s).']
    //         ], 400);
    //     }


    //     // Rechercher tous les colis correspondant à la référence et à l'identifiant fournis
    //     $colisList = Colis::where('reference_colis', $request->colisId)
    //                     ->where('id', $request->id)
    //                     ->where('destinations.agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
    //                     ->get();
    //     // dd($colisList);
    //     // Vérifier si des colis ont été trouvés
    //     if ($colisList->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Aucun colis trouvé avec cette référence et cet identifiant.'
    //         ], 404);
    //     }

    //     $messages = [];
    //     $updatedColis = [];

    //     // Parcourir chaque colis trouvé
    //     foreach ($colisList as $colis) {
    //         if ($colis->etat === 'Dechargé') {
    //             $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà Déchargé.";
    //         } elseif ($colis->etat === 'Fermé') {
    //             // Modifier l'état du colis en "En entrepot"
    //             $colis->etat = 'Déchargé';
    //             $colis->save();
    //             $updatedColis[] = [
    //                 'etat'        => $colis->etat,
    //             ];
    //             $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été déchargé succès.";
    //         } else {
    //             $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas encore Arrivé. Impossible de le mettre déchargé.";
    //         }
    //     }

    //     // SMS DATA
    //     $colisData = $colisList;
    //     // dd($colisData);
    //     foreach ($colisData as $colisId => $data) {
    //         // dd($data);
    //         try {
    //             $colis = Colis::findOrFail($data->id);
    //             $numero_expediteur = +2250546158376;
    //             // dd($numero_expediteur);
    //             // dd( $colis->prix_transit_colis);
    //             // Mise à jour du colis
    //             // $colis->prix_transit_colis = $data['prix_transit_colis'];
    //             // $colis->status = 'payé';
    //             // $colis->etat = 'Devis';
    //             // $colis->save();

    //             // Message SMS
    //             $message = "Bonjour " . $colis->expediteur->nom ." ". $colis->expediteur->prenom . ", votre colis (Réf: " . $colis->reference_colis . ") a Arrivé au niveau de Agence AFT IMPORT/EXPORT. Veuillez nous contacter pour plus d'informations. AFT IMPORT/EXPORT vous remercie pour votre confiance.";

    //             // Envoi du SMS
    //             $response = $infobipService->sendSms($numero_expediteur, $message);
    //             Log::info('SMS envoyé à ' . $numero_expediteur . ': ' . json_encode($response));

    //         } catch (\Exception $e) {
    //             Log::error('Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
    //             return back()->with('error', 'Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
    //         }
    //     }
    //     // END SMS DATA

    //     return response()->json([
    //         'success'  => !empty($updatedColis),
    //         'messages' => $messages,
    //         'colis'    => $updatedColis,
    //     ]);
    //     // dd( $updatedColis);

    // }


    public function updateColisDecharge(Request $request) // Nom de variable standardisé
    {
        // 1. Validation des paramètres d'entrée
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
        $agenceCible = 'IPMS-SIMEX-CI Angre 8ème Tranche'; // Nom de l'agence cible

        try {
            // 2. Recherche du colis spécifique avec jointure pour vérifier l'agence de destination
            //    Utilisation de firstOrFail pour obtenir un seul colis ou une exception si non trouvé/mauvaise agence.
            $colis = Colis::with('expediteur', 'destinataire') // Pré-charger les relations pour le SMS
                        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                        ->where('colis.reference_colis', $referenceColis)
                        ->where('colis.id', $identifiantColis)
                        ->where('destinataires.agence', $agenceCible)
                        ->select('colis.*') // Sélectionner uniquement les colonnes de colis après la jointure
                        ->firstOrFail(); // Lance une exception ModelNotFoundException si non trouvé

            $messages = [];
            $updated = false; // Flag pour savoir si une mise à jour a eu lieu

            // 3. Vérification de l'état actuel du colis
            if ($colis->etat === 'Dechargé') {
                // Cas : Déjà déchargé
                $messages[] = "INFO : Le colis Réf {$colis->reference_colis} (ID: {$colis->id}) a déjà été déchargé.";
                // Pas de mise à jour, pas de SMS supplémentaire requis pour cette action

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

    
// public function updateColisDecharge(Request $request)
// {
        
//     if (!$request->has('colisId') || !$request->has('id')) {
//         $missingParams = [];
//         if (!$request->has('colisId')) {
//             $missingParams[] = 'colisId';
//         }
//         if (!$request->has('id')) {
//             $missingParams[] = 'id';
//         }
//         return response()->json([
//             'success'  => false,
//             'messages' => [implode(" et ", $missingParams) . ' manquant(s).']
//         ], 400);
//     }


//     // Rechercher tous les colis correspondant à la référence et à l'identifiant fournis
//     $colisList = Colis::where('reference_colis', $request->colisId)
//                       ->where('id', $request->id)
//                     //   ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
//                       ->get();

//     // Vérifier si des colis ont été trouvés
//     if ($colisList->isEmpty()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Aucun colis trouvé avec cette référence et cet identifiant.'
//         ], 404);
//     }

//     $messages = [];
//     $updatedColis = [];

//     // Parcourir chaque colis trouvé
//     foreach ($colisList as $colis) {
//         if ($colis->etat === 'Dechargé') {
//             $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà Déchargé.";
//         } elseif ($colis->etat === 'Fermé') {
//             // Modifier l'état du colis en "En entrepot"
//             $colis->etat = 'Déchargé';
//             $colis->save();
//             $updatedColis[] = [
//                 'etat'        => $colis->etat,
//             ];
//             $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été déchargé succès.";
//         } else {
//             $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas encore Arrivé. Impossible de le mettre déchargé.";
//         }
//     }

//         // SMS DATA
//         $colisData = $colisList;
//         // dd($colisData);
//         foreach ($colisData as $colisId => $data) {
//             // dd($data);
//             try {
//                 $colis = Colis::findOrFail($data->id);
//                 $numero_expediteur = +2250546158376;
//                 // dd($numero_expediteur);
//                 // dd( $colis->prix_transit_colis);
//                 // Mise à jour du colis
//                 // $colis->prix_transit_colis = $data['prix_transit_colis'];
//                 // $colis->status = 'payé';
//                 // $colis->etat = 'Devis';
//                 // $colis->save();
    
//                 // Message SMS
//                 $message = "Bonjour " . $colis->expediteur->nom ." ". $colis->expediteur->prenom . ", votre colis (Réf: " . $colis->reference_colis . ") a Arrivé au niveau de Agence AFT IMPORT/EXPORT. Veuillez nous contacter pour plus d'informations. AFT IMPORT/EXPORT vous remercie pour votre confiance.";
    
//                 // Envoi du SMS
//                 $response = $infobipService->sendSms($numero_expediteur, $message);
//                 Log::info('SMS envoyé à ' . $numero_expediteur . ': ' . json_encode($response));
    
//             } catch (\Exception $e) {
//                 Log::error('Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
//                 return back()->with('error', 'Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
//             }
//         }
//         // END SMS DATA

//     return response()->json([
//         'success'  => !empty($updatedColis),
//         'messages' => $messages,
//         'colis'    => $updatedColis,
//     ]);
//     // dd( $updatedColis);

// }
    
   

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
            $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà Déchargé.";
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
            $bateau = Bateaux::where('reference_conteneur', $request->reference_conteneur)->first();

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
