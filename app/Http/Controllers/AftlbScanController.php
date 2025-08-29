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
// use App\Services\SmsService;
use App\Services\SmsService;


class AftlbScanController extends Controller
{

    protected $smsService;
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
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
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
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
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
    //             'colis.created_at as created_at'
    //         )
    //         ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
    //         ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
    //         ->where('etat', 'Chargé') 
    //         ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
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
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
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
                    return '<a href="' . route('aftlb_scan.colis.modifier', $row['reference_colis']) . '" 
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

        return view('AFT_LOUIS_BLERIOT.scan.edit_scan', compact('colis', 'reference_colis'));
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

        return redirect()->route('aftlb_scan.chargement')->with('success', 'État des colis mis à jour avec succès.');
    }


    public function livre()
    {
        return view('AFT_LOUIS_BLERIOT.scan.livre');
    }

    public function get_colis_livre(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.reference_colis',
                'colis.quantite_colis',
                'expediteurs.nom as expediteur_nom',
                'expediteurs.prenom as expediteur_prenom',
                'expediteurs.tel as expediteur_tel',
                'expediteurs.agence as expediteur_agence',
                'destinataires.nom as destinataire_nom',
                'destinataires.prenom as destinataire_prenom',
                'destinataires.agence as destinataire_agence',
                'destinataires.tel as destinataire_tel',
                'colis.etat',
                'colis.created_at'
            )
            ->leftJoin('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->leftJoin('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.etat', 'Livré')
            ->where('destinataires.agence', 'AFT Agence Louis Bleriot')
            ->where('colis.mode_transit', 'aerien')
            ->get();
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->sum('quantite_colis'), // Somme ici
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
            } elseif ($colis->etat === 'En entrepot') {
                // Modifier l'état du colis en "En entrepot"
                $colis->etat = 'Chargé';
                $colis->save();
                $updatedColis[] = [
                    'etat'        => $colis->etat,
                ];
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été Chargé avec succès.";
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
//             $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été déchargé succès.";
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

//         //    $expediteurTelForSms = $expediteurTel;
//         $destinataireTelForSms = $colisList->first()->destinataire_tel;

//         // dd($expediteurTelForSms, $destinataireTelForSms);
        

        
//         $colisReferences = $colisList->pluck('id_reference')->implode(', ');
//         $messageSms = "Cher(e) client(e), votre colis (Réf: {$colisReferences}) a été créé avec succès et est en attente d'expédition. Merci de nous faire confiance.";


//         // Envoi du SMS au destinataire
//         if ($destinataireTelForSms) { // Utilisation du numéro complet
//             try {
//                 $smsService->sendSms($destinataireTelForSms, $messageSms);
//                 Log::info("SMS envoyé au destinataire {$destinataireTelForSms} pour le colis {$colisReferences}.");
//             } catch (\RuntimeException $e) {
//                 Log::error("⚠️ Erreur de configuration Infobip lors de l'envoi SMS au destinataire: " . $e->getMessage(), [
//                     'phone_number' => $destinataireTelForSms,
//                     'message' => $messageSms
//                 ]);
//             } catch (\Throwable $e) {
//                 Log::error("⚠️ Une erreur inattendue est survenue lors de l'envoi du SMS au destinataire ! " . $e->getMessage(), [
//                     'phone_number' => $destinataireTelForSms,
//                     'message' => $messageSms,
//                     'trace' => $e->getTraceAsString()
//                 ]);
//             }
//         }

//     return response()->json([
//         'success'  => !empty($updatedColis),
//         'messages' => $messages,
//         'colis'    => $updatedColis,
//     ]);
//     // dd( $updatedColis);

// }


    // Injection de dépendance du service SMS
    // public function __construct(SmsService $smsService)
    // {
    //     $this->smsService = $smsService;
    // }

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
        $colisSuccessfullyDecharged = [];

        // Parcourir chaque colis trouvé
        foreach ($colisList as $colis) {
            if ($colis->etat === 'Dechargé') {
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) est déjà déchargé.";
            } elseif ($colis->etat === 'Fermé' || $colis->etat === 'Arrivé') {
                
                $colis->etat = 'Déchargé';
                $colis->save();
                $updatedColis[] = [
                    'etat'        => $colis->etat,
                    'reference_colis' => $colis->reference_colis,
                    'id'          => $colis->id,
                ];
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) a été déchargé avec succès.";
                $colisSuccessfullyDecharged[] = $colis;
            } else {
                $messages[] = "Le colis avec la référence {$colis->reference_colis} (ID: {$colis->id}) n'est pas dans un état permettant le déchargement (actuellement : {$colis->etat}).";
            }
        }

        // Envoi du SMS UNIQUEMENT si des colis ont été réellement déchargés
        if (!empty($colisSuccessfullyDecharged)) {
            $destinataireTelForSms = $colisSuccessfullyDecharged[0]->destinataire_tel;
            $colisReferences = collect($colisSuccessfullyDecharged)->pluck('reference_colis')->implode(', ');
            $messageSms = "Cher(e) client(e), votre colis (Réf: {$colisReferences}) est arrivé à destination et a été déchargé. Vous pouvez le récupérer. Merci de nous faire confiance.";

            // Envoi du SMS au destinataire 
            if ($destinataireTelForSms) {
                try {
                    $this->smsService->sendSms($destinataireTelForSms, $messageSms);
                    Log::info("SMS envoyé au destinataire {$destinataireTelForSms} pour les colis {$colisReferences} (Déchargés).");
                    $messages[] = "Un SMS de notification de déchargement a été envoyé au destinataire.";
                } catch (\RuntimeException $e) {
                    Log::error("⚠️ Erreur de configuration Infobip lors de l'envoi SMS au destinataire: " . $e->getMessage(), [
                        'phone_number' => $destinataireTelForSms,
                        'message' => $messageSms
                    ]);
                    $messages[] = "Erreur lors de l'envoi du SMS de notification au destinataire (configuration).";
                } catch (\Throwable $e) {
                    Log::error("⚠️ Une erreur inattendue est survenue lors de l'envoi du SMS au destinataire ! " . $e->getMessage(), [
                        'phone_number' => $destinataireTelForSms,
                        'message' => $messageSms,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $messages[] = "Erreur inattendue lors de l'envoi du SMS de notification au destinataire.";
                }
            } else {
                $messages[] = "Numéro de téléphone du destinataire non trouvé pour l'envoi du SMS de notification.";
            }
        } else {
            $messages[] = "Aucun colis n'a été nouvellement déchargé, donc aucun SMS n'a été envoyé.";
        }

        return response()->json([
            'success'  => !empty($updatedColis),
            'messages' => $messages,
            'colis'    => $updatedColis,
        ]);
    }


}
