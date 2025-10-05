<?php

namespace App\Http\Controllers;

use App\Models\Colis;
use App\Models\Paiement;
use Illuminate\Http\Request;
use App\Models\User; // Ajouter l'import User
use App\Models\Programme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChauffeurColisController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:chauffeur');
    }

    public function index()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'chauffeur') {
            return view('chauffeur.programme', ['data' => []])->with('error', 'Profil chauffeur non trouvé.');
        }
    
        $userId = $user->id;
        
        // UTILISER user_id AU LIEU DE chauffeur_id
        $programmes = Programme::where('etat_rdv','en attente')
                    ->where('user_id', $userId)->get();
    
        return view('chauffeur.programme', compact('programmes'));
    }

    // public function updateEtatRdv(Request $request, Programme $programme)
    // {
    //     $request->validate([
    //         'etat_rdv' => 'required|in:en cours,effectué,à replanifié',
    //     ]);
    
    //     $programme->etat_rdv = $request->etat_rdv;
    //     $programme->save();
    
    //     return response()->json([
    //         'success' => true, 
    //         'message' => 'État du RDV mis à jour avec succès.',
    //         'etat_rdv' => $programme->etat_rdv
    //     ]);
    // }

    /**
     * Récupère les détails de paiement pour un colis.
     */
    // public function getPaymentDetails($reference_colis)
    // {
    //     $colis = Colis::where('reference_colis', $reference_colis)->firstOrFail();
    //     $paiement = $colis->paiement;

    //     $details = [
    //         'cinetpay_apikey' => '521006956621e4e7a6a3d16.70681548',
    //         'cinetpay_site_id' => '405886',
    //         'notify_url' => route('colis.cinetpay.notify'),
    //     ];

    //     if (!$paiement) {
    //         $details = array_merge($details, [
    //             'status' => 'non payé',
    //             'montant_total' => $colis->prix_transit_colis,
    //             'montant_paye' => 0,
    //             'reste_a_payer' => $colis->prix_transit_colis
    //         ]);
    //     } else {
    //         $details = array_merge($details, [
    //             'status' => $paiement->statut_paiement,
    //             'montant_total' => $paiement->montant,
    //             'montant_paye' => $paiement->montant_paye,
    //             'reste_a_payer' => $paiement->montant - $paiement->montant_paye
    //         ]);
    //     }
        
    //     return response()->json($details);
    // }

    /**
     * Traite le paiement encaissé par le chauffeur.
     */
    // public function processFieldPayment(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'reference_colis' => 'required|string|exists:colis,reference_colis',
    //         'montant_encaisse' => 'required|numeric|min:0.01',
    //         'methode_paiement' => 'required|string|in:especes,mobile_money',
    //         'operateur_mobile' => 'nullable|required_if:methode_paiement,mobile_money|string',
    //         'numero_tel' => 'nullable|required_if:methode_paiement,mobile_money|string',
    //     ]);

    //     $userId = Auth::id();

    //     DB::beginTransaction();
    //     try {
    //         $colisGroupe = Colis::where('reference_colis', $validatedData['reference_colis'])->get();
    //         $premierColis = $colisGroupe->first();
            
    //         $paiement = Paiement::firstOrCreate(
    //             ['colis_id' => $premierColis->id],
    //             [
    //                 'montant' => $premierColis->prix_transit_colis,
    //                 'montant_paye' => 0,
    //                 'statut_paiement' => 'Non Payé',
    //                 'expediteur_id' => $premierColis->expediteur_id
    //             ]
    //         );

    //         $nouveauMontantPaye = $paiement->montant_paye + $validatedData['montant_encaisse'];
    //         $paiement->montant_paye = $nouveauMontantPaye;
    //         $paiement->methode_paiement = $validatedData['methode_paiement'];
    //         $paiement->agent_id = $userId;
    //         $paiement->date_validation = now();

    //         if ($validatedData['methode_paiement'] === 'mobile_money') {
    //             $paiement->operateur = $validatedData['operateur_mobile'];
    //             $paiement->NumeroPaiement = $validatedData['numero_tel'];
    //         }
            
    //         if ($nouveauMontantPaye >= $paiement->montant) {
    //             $paiement->statut_paiement = 'Payé';
    //         }
    //         $paiement->save();

    //         foreach ($colisGroupe as $colis) {
    //             $colis->paiement_id = $paiement->id;
    //             if ($paiement->statut_paiement === 'Payé') {
    //                 $colis->status = 'payé';
    //             }
    //             $colis->save();
    //         }

    //         DB::commit();
    //         return response()->json(['success' => true, 'message' => 'Encaissement enregistré avec succès !']);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['success' => false, 'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()], 500);
    //     }
    // }
}