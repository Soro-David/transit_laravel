<?php

namespace App\Http\Controllers;

use App\Models\Colis;
use App\Models\Paiement;
use Illuminate\Http\Request;
use App\Models\Chauffeur;
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
        $chauffeur = Chauffeur::where('email', Auth::user()->email)->first();
        if (!$chauffeur) {
            return view('chauffeur.programme', ['data' => []])->with('error', 'Profil chauffeur non trouvé.');
        }
    
        $chauffeurId = $chauffeur->id;
        $programmes = Programme::where('chauffeur_id', $chauffeurId)->get();
    
        $data = $programmes->map(function ($programme) {
            $colis = Colis::where('reference_colis', $programme->reference_colis)->with('paiement')->first(); // On charge la relation 'paiement'
    
            // --- NOUVELLE LOGIQUE POUR LE STATUT DE PAIEMENT ---
            $statutPaiement = 'non payé'; // Statut par défaut
            $resteAPayer = $colis ? $colis->prix_transit_colis : 0;
    
            if ($colis && $colis->paiement) {
                $statutPaiement = $colis->paiement->statut_paiement;
                $resteAPayer = $colis->paiement->montant - $colis->paiement->montant_paye;
            }
    
            return [
                'id' => $programme->id,
                'date_programme_formatted' => Carbon::parse($programme->date_programme)->format('d/m/Y'),
                'reference_colis' => $programme->reference_colis,
                'nature_colis' => $colis ? $colis->service : 'N/A',
                'actions_a_faire' => $programme->actions_a_faire,
                'nom_expediteur' => $programme->nom_expediteur,
                'nom_destinataire' => $programme->nom_destinataire,
                'lieu_destinataire' => $programme->lieu_destinataire,
                'etat_rdv' => $programme->etat_rdv,
                'statut_paiement' => $statutPaiement, // On envoie le statut
                'reste_a_payer' => $resteAPayer > 0, // On envoie un booléen : true s'il reste à payer
            ];
        });
    
        return view('chauffeur.programme', compact('data'));
    }

    public function updateEtatRdv(Request $request, Programme $programme)
    {
        $request->validate([
            'etat_rdv' => 'required|in:en cours,effectué,à replanifié',
        ]);
    
        $programme->etat_rdv = $request->etat_rdv;
        $programme->save();
    
        return response()->json([
            'success' => true, 
            'message' => 'État du RDV mis à jour avec succès.',
            'etat_rdv' => $programme->etat_rdv // Retourner le nouvel état
        ]);
    }

    /**
     * NOUVELLE MÉTHODE : Récupère les détails de paiement pour un colis.
     */
    public function getPaymentDetails($reference_colis)
    {
        $colis = Colis::where('reference_colis', $reference_colis)->firstOrFail();
        $paiement = $colis->paiement;
    
        $details = [
            // NOUVEAU : On ajoute les infos CinetPay pour les passer à la vue
            'cinetpay_apikey' => '521006956621e4e7a6a3d16.70681548', // Remplacez par votre clé API
            'cinetpay_site_id' => '405886', // Remplacez par votre ID de site
            'notify_url' => route('colis.cinetpay.notify'), // Assurez-vous que cette route existe
        ];
    
        if (!$paiement) {
            $details = array_merge($details, [
                'status' => 'non payé',
                'montant_total' => $colis->prix_transit_colis,
                'montant_paye' => 0,
                'reste_a_payer' => $colis->prix_transit_colis
            ]);
        } else {
            $details = array_merge($details, [
                'status' => $paiement->statut_paiement,
                'montant_total' => $paiement->montant,
                'montant_paye' => $paiement->montant_paye,
                'reste_a_payer' => $paiement->montant - $paiement->montant_paye
            ]);
        }
        
        return response()->json($details);
    }

    /**
     * NOUVELLE MÉTHODE : Traite le paiement encaissé par le chauffeur.
     */
    public function processFieldPayment(Request $request)
    {
        $validatedData = $request->validate([
            'reference_colis' => 'required|string|exists:colis,reference_colis',
            'montant_encaisse' => 'required|numeric|min:0.01',
            'methode_paiement' => 'required|string|in:especes,mobile_money',
            'operateur_mobile' => 'nullable|required_if:methode_paiement,mobile_money|string',
            'numero_tel' => 'nullable|required_if:methode_paiement,mobile_money|string',
        ]);

        $chauffeurId = Auth::id(); // Récupère l'ID de l'utilisateur (chauffeur) connecté

        // On utilise une transaction pour garantir l'intégrité des données
        DB::beginTransaction();
        try {
            $colisGroupe = Colis::where('reference_colis', $validatedData['reference_colis'])->get();
            $premierColis = $colisGroupe->first();
            
            $paiement = Paiement::firstOrCreate(
                ['colis_id' => $premierColis->id], // Clé de recherche
                [ // Valeurs par défaut si le paiement n'existe pas
                    'montant' => $premierColis->prix_transit_colis,
                    'montant_paye' => 0,
                    'statut_paiement' => 'Non Payé',
                    'expediteur_id' => $premierColis->expediteur_id
                ]
            );

            // Mise à jour du paiement
            $nouveauMontantPaye = $paiement->montant_paye + $validatedData['montant_encaisse'];
            $paiement->montant_paye = $nouveauMontantPaye;
            $paiement->methode_paiement = $validatedData['methode_paiement'];
            $paiement->agent_id = $chauffeurId;
            $paiement->date_validation = now();

            if ($validatedData['methode_paiement'] === 'mobile_money') {
                $paiement->operateur = $validatedData['operateur_mobile'];
                $paiement->NumeroPaiement = $validatedData['numero_tel'];
            }
            
            // Si le montant payé couvre le total, le statut devient "Payé"
            if ($nouveauMontantPaye >= $paiement->montant) {
                $paiement->statut_paiement = 'Payé';
            }
            $paiement->save();

            // Mise à jour de tous les colis du groupe
            foreach ($colisGroupe as $colis) {
                $colis->paiement_id = $paiement->id;
                if ($paiement->statut_paiement === 'Payé') {
                    $colis->status = 'payé';
                }
                $colis->save();
            }

            DB::commit(); // Tout s'est bien passé, on valide les changements
            return response()->json(['success' => true, 'message' => 'Encaissement enregistré avec succès !']);

        } catch (\Exception $e) {
            DB::rollBack(); // Une erreur est survenue, on annule tout
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()], 500);
        }
    }
}