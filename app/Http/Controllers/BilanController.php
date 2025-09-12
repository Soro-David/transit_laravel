<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AgentColisExport;
use App\Models\Agent;
use App\Models\Colis;
use App\Models\Expediteur;
use App\Models\OperationComptable;
use App\Models\Paiement; // Assurez-vous que ce modèle est importé
use App\Models\Produit;
use App\Services\CurrencyConverterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\OperationComptableGexport;

class BilanController extends Controller
{
    public function index(Request $request)
    {
        // Statistiques de base (GLOBALES)
        $customers_count = Expediteur::count();
        $products_count = Produit::count();
        $colisCount = Colis::where('etat', 'Validé')->count();

        // [MODIFICATION 1] : Le bilan est maintenant la somme de toutes les opérations comptables.
        $totalEntrees = OperationComptable::where('type_operation', 'ENTREE D\'ARGENT')->sum('montant');
        $totalSorties = OperationComptable::where('type_operation', 'SORTIE D\'ARGENT')->sum('montant');
        $montantBilan = $totalEntrees - $totalSorties;


        // ================ STATISTIQUES TRANSPORT (GLOBALES) ================
        // Aérien
        $volCargaisonCount = Colis::where('mode_transit', 'aérien')->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])->count();
        $colisAerienCount = Colis::where('mode_transit', 'aérien')->where('etat', 'Validé')->count();
        $volValideCount = Colis::where('mode_transit', 'aérien')->where('etat', 'Validé')->count();
        $volEnCoursCount = Colis::where('mode_transit', 'aérien')->whereIn('etat', ['En entrepôt', 'Chargé'])->count();
        $volAnnuleCount = Colis::where('mode_transit', 'aérien')->where('etat', 'Annulé')->count();

        // Maritime
        $conteneurCount = Colis::where('mode_transit', 'maritime')->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])->count();
        $colisMaritimeCount = Colis::where('mode_transit', 'maritime')->where('etat', 'Validé')->count();
        $conteneurValideCount = Colis::where('mode_transit', 'maritime')->where('etat', 'Validé')->count();
        $conteneurEnCoursCount = Colis::where('mode_transit', 'maritime')->whereIn('etat', ['En entrepôt', 'Chargé'])->count();
        $conteneurAnnuleCount = Colis::where('mode_transit', 'maritime')->where('etat', 'Annulé')->count();


        // ================ GRAPHIQUE COLIS PAR MOIS (GLOBAL) ================
        $currentYear = now()->year;
        $colisParMois = Colis::select(
            DB::raw('MONTH(updated_at) as mois'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('updated_at', $currentYear)
            ->groupBy(DB::raw('MONTH(updated_at)'))
            ->orderBy(DB::raw('MONTH(updated_at)'))
            ->get()->keyBy('mois');

        $moisNoms = ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
        $colisData = [];
        for ($i = 1; $i <= 12; $i++) {
            $colisData[] = $colisParMois->has($i) ? (int)$colisParMois[$i]->total : 0;
        }


        // ================ GESTION DES AGENTS (GLOBAL) ================
        $agents = Agent::withCount(['colisValides as colis_valides_count' => function ($query) {
            $query->where('etat', 'Validé');
        }])->orderBy('nom')->get();

        $selectedAgentId = $request->input('agent_id');
        $agentColis = [];
        $agentTotals = null;

        if ($selectedAgentId) {
            $colisCollection = Colis::with('paiements')->where('etat', 'Validé')->where('agent_id', $selectedAgentId)->get();
            $colisGroupes = $colisCollection->groupBy('reference_colis');
            foreach ($colisGroupes as $reference => $colisDuGroupe) {
                $paiementsUniques = $colisDuGroupe->flatMap(fn($colis) => $colis->paiements)->unique('id');
                $prixTotalGroupe = $paiementsUniques->sum('montant');
                $montantPayeGroupe = $paiementsUniques->sum('montant_paye');
                $resteAPayerGroupe = $prixTotalGroupe - $montantPayeGroupe;
                $dernierPaiement = $paiementsUniques->sortByDesc('date_validation')->first();
                $agentColis[] = [
                    'reference_colis' => $reference,
                    'date_paiement'   => $dernierPaiement ? $dernierPaiement->date_validation->format('Y-m-d H:i:s') : 'Non payé',
                    'mode_transit'    => $colisDuGroupe->first()->mode_transit,
                    'prix_colis'      => number_format($prixTotalGroupe, 2, '.', ''),
                    'montant_paye'    => number_format($montantPayeGroupe, 2, '.', ''),
                    'reste_a_payer'   => number_format($resteAPayerGroupe, 2, '.', ''),
                ];
            }
        }


        // ================ DONNÉES DIVERSES POUR LA VUE ================
        $operationsComptables = OperationComptable::with('agent')->latest()->take(10)->get();
        $conteneursDisponibles = Colis::whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])->whereNotNull('reference_contenaire')->distinct()->pluck('reference_contenaire');
        

        // ================ CALCUL DES TOTAUX TRANSIT PAR AGENCE ================
        $etatsColisActifs = ['Validé', 'Fermé', 'En entrepôt', 'Chargé'];
        $totalTransitLouisBleriot = Colis::whereIn('etat', $etatsColisActifs)->whereHas('agent.agence', fn($q) => $q->where('nom_agence', 'AFT Agence Louis Bleriot'))->sum('prix_transit_colis');
        $totalTransitChine = Colis::whereIn('etat', $etatsColisActifs)->whereHas('agent.agence', fn($q) => $q->where('nom_agence', 'Agence de Chine'))->sum('prix_transit_colis');
        
        $converter = new CurrencyConverterService();
        $totalTransitChineEnEuros = $converter->convertCfaToEur($totalTransitChine);
        $totalPrixTransitColis = $totalTransitLouisBleriot + $totalTransitChineEnEuros;


        // ================ CALCUL DES TOTAUX MONTANT PAYÉ PAR AGENCE ================
        $totalMontantPayeLouisBleriot = DB::table('paiements')->join('agents', 'paiements.agent_id', '=', 'agents.id')->join('agences', 'agents.agence_id', '=', 'agences.id')->where('agences.nom_agence', 'AFT Agence Louis Bleriot')->sum('paiements.montant_paye');
        $totalMontantPayeChine = DB::table('paiements')->join('agents', 'paiements.agent_id', '=', 'agents.id')->join('agences', 'agents.agence_id', '=', 'agences.id')->where('agences.nom_agence', 'Agence de Chine')->sum('paiements.montant_paye');
        $totalMontantPayeChineEnEuros = $converter->convertCfaToEur($totalMontantPayeChine);
        $totalMontantPayeGlobal = $totalMontantPayeLouisBleriot + $totalMontantPayeChineEnEuros;

        return view('admin.bilan.bilan', compact(
            'colisData', 'moisNoms', 'customers_count', 'products_count',
            'colisCount', 'totalPrixTransitColis',
            'volCargaisonCount', 'colisAerienCount', 'volValideCount', 'volEnCoursCount',
            'volAnnuleCount', 'conteneurCount', 'colisMaritimeCount', 'conteneurValideCount',
            'conteneurEnCoursCount', 'conteneurAnnuleCount', 'agents', 'agentColis', 'agentTotals',
            'selectedAgentId', 'operationsComptables', 'montantBilan', 'conteneursDisponibles', 
            'totalTransitLouisBleriot', 'totalTransitChine', 'totalTransitChineEnEuros',
            'totalMontantPayeGlobal', 'totalMontantPayeLouisBleriot',
            'totalMontantPayeChine', 'totalMontantPayeChineEnEuros'
        ));
    }


    // [NOUVELLE FONCTION] : Pour l'appel AJAX qui récupère le solde d'un colis
    public function getResteAPayer($reference)
    {
        Log::info("Recherche du reste à payer pour la référence : " . $reference);
    
        // On recherche tous les colis ayant cette référence (Validé) et non complètement payés
        $colis = Colis::where('reference_colis', $reference)
                      ->where('etat', 'Validé')
                      ->where(function($q) {
                          $q->where('status', 'non payé')
                            ->orWhere('status', 'partiellement payé');
                      })
                      ->get();
    
        if ($colis->isEmpty()) {
            Log::warning("Aucun colis non payé trouvé pour la référence : " . $reference);
            return response()->json(['error' => 'Référence non trouvée ou déjà payée.'], 404);
        }
    
        // Total dû = somme des prix de transit des colis
        $totalDu = $colis->sum('prix_transit_colis');
    
        // Calculer le total payé : on essaye d'abord de récupérer paiements via la table paiements
        $colisIds = $colis->pluck('id')->toArray();
    
        // Si dans votre structure la table paiements utilise 'colis_id' :
        $totalPaye = Paiement::whereIn('colis_id', $colisIds)->sum('montant_paye');
    
        // Si votre app associe payment via paiement_id sur le colis, on additionne aussi pour sécurité:
        $paiementIds = $colis->pluck('paiement_id')->filter()->unique()->toArray();
        if (!empty($paiementIds)) {
            $totalPaye += Paiement::whereIn('id', $paiementIds)->sum('montant_paye');
        }
    
        $resteAPayer = max(0, $totalDu - $totalPaye);
    
        Log::info("Calcul pour {$reference} : TotalDû={$totalDu}, TotalPayé={$totalPaye}, Reste={$resteAPayer}");
    
        return response()->json([
            'reste_a_payer' => $resteAPayer,
            'total_du' => $totalDu,
            'total_paye' => $totalPaye,
        ]);
    }

    /**
     * [FONCTION CORRIGÉE] : Gère la création d'un dossier de paiement s'il n'existe pas.
     */
    public function enregistrerOperation(Request $request)
    {
        $validatedData = $request->validate([
            'date_operation' => 'required|date',
            'type_operation' => 'required|string',
            'beneficiaire_fournisseur' => 'required|string',
            'objet' => 'nullable|string',
            'montant' => 'required|numeric|min:0.01',
            'conteneur_frais_fonction' => 'nullable|string',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        $referenceColis = null;
        if ($validatedData['type_operation'] === 'ENTREE D\'ARGENT' && str_starts_with($validatedData['beneficiaire_fournisseur'], 'COLIS-')) {
            $referenceColis = str_replace('COLIS-', '', $validatedData['beneficiaire_fournisseur']);
        }
        
        DB::beginTransaction();
        try {
            // Créer l'opération comptable (ceci est fait dans tous les cas)
            OperationComptable::create([
                'date_operation' => $validatedData['date_operation'],
                'type_operation' => $validatedData['type_operation'],
                'beneficiaire_fournisseur' => $validatedData['beneficiaire_fournisseur'],
                'objet' => $validatedData['objet'],
                'montant' => $validatedData['montant'],
                'conteneur_frais_fonction' => $validatedData['conteneur_frais_fonction'],
                'agent_id' => $validatedData['agent_id'],
                'reference_colis' => $referenceColis,
            ]);

            // Si c'est un paiement de colis, on gère la logique de paiement
            if ($referenceColis) {
                $colisConcerned = Colis::where('reference_colis', $referenceColis)->where('etat', 'Validé')->get();
                if ($colisConcerned->isEmpty()) {
                    throw new \Exception("Aucun colis valide trouvé pour la référence " . $referenceColis);
                }

                $paiementId = $colisConcerned->first()->paiement_id;

                $paiement = null;
                // CAS 1: Le dossier de paiement existe déjà, on le met à jour
                if ($paiementId) {
                    $paiement = Paiement::find($paiementId);
                    if ($paiement) {
                        $paiement->montant_paye += $validatedData['montant'];
                        $paiement->save();
                    }
                } 
                // CAS 2: Le dossier de paiement n'existe PAS, on le CRÉE
                else {
                    $totalDu = $colisConcerned->sum('prix_transit_colis');
                    $firstColis = $colisConcerned->first();

                    $paiement = Paiement::create([
                        'montant' => $totalDu,
                        'montant_paye' => $validatedData['montant'],
                        'statut_paiement' => 'partiellement payé', // Initialement
                        'expediteur_id' => $firstColis->expediteur_id,
                        'agent_id' => $firstColis->agent_id,
                        'methode_paiement' => 'especes', // Valeur par défaut
                        'date_validation' => now(),
                    ]);
                    
                    // On associe le nouvel ID de paiement à tous les colis concernés
                    Colis::whereIn('id', $colisConcerned->pluck('id'))->update(['paiement_id' => $paiement->id]);
                }

                // Mise à jour finale du statut du colis
                if ($paiement) {
                    $newStatus = ($paiement->montant_paye >= $paiement->montant) ? 'payé' : 'partiellement payé';
                    $paiement->statut_paiement = $newStatus;
                    $paiement->save();
                    Colis::whereIn('id', $colisConcerned->pluck('id'))->update(['status' => $newStatus]);
                }
            }

            DB::commit();
            return redirect()->route('bilan.bilan')->with('success', 'Opération comptable enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'enregistrement de l'opération: " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. L\'opération n\'a pas été enregistrée.');
        }
    }

    public function exportAgentColisToExcel(Request $request)
    {
        $agentId = $request->input('agent_id');
        if (!$agentId) {
            return redirect()->back()->withErrors(['agent_id' => 'ID agent manquant pour l\'exportation']);
        }
        return Excel::download(new AgentColisExport($agentId), 'colis_agent_' . $agentId . '_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportOperationsComptablesToExcel(Request $request)
    {
        return Excel::download(new OperationComptableGexport(null), 'operations_comptables_global_' . now()->format('Y-m-d') . '.xlsx');
    }
}