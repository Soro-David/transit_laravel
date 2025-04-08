<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AgentColisExport;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use App\Models\Agent;
use App\Models\Produit;
use App\Models\Colis;
use App\Models\Expediteur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BilanController extends Controller
{
    public function index(Request $request)
    {
        // Statistiques de base
        $customers_count = Expediteur::count();
        $products_count = Produit::count();
        $colisCount = Colis::where('etat', 'Validé')->count();
        $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
                             ->sum('prix_transit_colis');

        // ================ STATISTIQUES TRANSPORT ================
        // Aérien
        $volCargaisonCount = Colis::where('mode_transit', 'aérien')
                               ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                               ->count();
        
        $colisAerienCount = Colis::where('mode_transit', 'aérien')
                               ->where('etat', 'Validé')
                               ->count();
        
        $volValideCount = Colis::where('mode_transit', 'aérien')
                            ->where('etat', 'Validé')
                            ->count();
        
        $volEnCoursCount = Colis::where('mode_transit', 'aérien')
                             ->whereIn('etat', ['En entrepôt', 'Chargé'])
                             ->count();
        
        $volAnnuleCount = Colis::where('mode_transit', 'aérien')
                            ->where('etat', 'Annulé')
                            ->count();

        // Maritime
        $conteneurCount = Colis::where('mode_transit', 'maritime')
                            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
                            ->count();
        
        $colisMaritimeCount = Colis::where('mode_transit', 'maritime')
                                 ->where('etat', 'Validé')
                                 ->count();
        
        $conteneurValideCount = Colis::where('mode_transit', 'maritime')
                                  ->where('etat', 'Validé')
                                  ->count();
        
        $conteneurEnCoursCount = Colis::where('mode_transit', 'maritime')
                                   ->whereIn('etat', ['En entrepôt', 'Chargé'])
                                   ->count();
        
        $conteneurAnnuleCount = Colis::where('mode_transit', 'maritime')
                                  ->where('etat', 'Annulé')
                                  ->count();

        // ================ GRAPHIQUE COLIS PAR MOIS ================
        $currentYear = now()->year;

        $colisParMois = Colis::select(
                            DB::raw('MONTH(updated_at) as mois'),
                            DB::raw('COUNT(*) as total')
                        )
                        ->whereYear('updated_at', $currentYear)
                        ->groupBy(DB::raw('MONTH(updated_at)'))
                        ->orderBy(DB::raw('MONTH(updated_at)'))
                        ->get()
                        ->keyBy('mois');

        $moisNoms = ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];

        $colisData = [];
        for ($i = 1; $i <= 12; $i++) {
            $colisData[] = $colisParMois->has($i) ? (int)$colisParMois[$i]->total : 0;
        }

        // ================ GESTION DES AGENTS ================
        $agents = Agent::withCount(['colisValides as colis_valides_count' => function($query) {
            $query->where('etat', 'Validé');
        }])
        ->orderBy('nom')
        ->get();

        $selectedAgentId = $request->input('agent_id');
        $agentColis = [];
        $agentTotals = ['totalPrix' => '0.00', 'totalPaye' => '0.00', 'totalResteAPayer' => '0.00'];

        if ($selectedAgentId) {
            $agentColisCollection = Colis::with(['paiement'])
                ->where('etat', 'Validé')
                ->where('agent_id', $selectedAgentId)
                ->get();

            $agentColis = $agentColisCollection->map(function($colis) {
                $montantPaye = optional($colis->paiement)->montant ?? 0;
                $prixColis = $colis->prix_transit_colis;
                $resteAPayer = max(0, $prixColis - $montantPaye);

                return [
                    'reference_colis' => $colis->reference_colis,
                    'date_paiement' => optional($colis->paiement)->date_validation ?? 'Non payé',
                    'mode_transit' => $colis->mode_transit,
                    'prix_colis' => number_format($prixColis, 2),
                    'montant_paye' => number_format($montantPaye, 2),
                    'reste_a_payer' => number_format($resteAPayer, 2),
                ];
            })->toArray();

            // Calcul des totaux
            $totalPrix = $agentColisCollection->sum('prix_transit_colis');
            $totalPaye = $agentColisCollection->sum(function($colis) {
                return optional($colis->paiement)->montant ?? 0;
            });
            $totalResteAPayer = $agentColisCollection->sum(function($colis) {
                $montantPaye = optional($colis->paiement)->montant ?? 0;
                return max(0, $colis->prix_transit_colis - $montantPaye);
            });

            $agentTotals = [
                'totalPrix' => number_format($totalPrix, 2),
                'totalPaye' => number_format($totalPaye, 2),
                'totalResteAPayer' => number_format($totalResteAPayer, 2),
            ];
        }
        
        return view('admin.bilan.bilan', compact(
            'colisData', 'moisNoms', 'customers_count', 'products_count',
            'colisCount', 'totalPrixTransit', 'volCargaisonCount', 'colisAerienCount',
            'volValideCount', 'volEnCoursCount', 'volAnnuleCount', 'conteneurCount',
            'colisMaritimeCount', 'conteneurValideCount', 'conteneurEnCoursCount',
            'conteneurAnnuleCount', 'agents', 'agentColis', 'agentTotals', 'selectedAgentId'
        ));
    }

    public function exportAgentColisToExcel(Request $request)
    { 
        $agentId = $request->input('agent_id');

        if (!$agentId) {
            return redirect()->back()
                ->withErrors(['agent_id' => 'ID agent manquant pour l\'exportation']);
        }

        return Excel::download(
            new AgentColisExport($agentId), 
            'colis_agent_' . $agentId . '_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}