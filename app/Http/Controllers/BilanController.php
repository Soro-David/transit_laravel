<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AgentColisExport; // Garder si vous l'utilisez ailleurs dans l'admin
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use App\Models\Agent;
use App\Models\Produit;
use App\Models\Colis;
use App\Models\Expediteur;
use App\Services\CurrencyConverterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OperationComptable;
use App\Exports\OperationComptableGexport; // Assurez-vous que l'import est correct

class BilanController extends Controller
{
    public function index(Request $request)
    {
        // Statistiques de base (GLOBALES - plus de filtre agent)
        $customers_count = Expediteur::count();
        $products_count = Produit::count();
        $colisCount = Colis::where('etat', 'Validé')->count(); // GLOBAL - plus de filtre agent

        // Calcul du totalPrixTransit GLOBAL (basé sur tous les colis validés, etc.)
        $totalPrixTransitColis = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
            ->sum('prix_transit_colis'); // GLOBAL - plus de filtre agent

        // Récupérer TOUTES les opérations comptables (GLOBALES - plus de filtre agent)
        $operationsComptablesBilan = OperationComptable::get(); // GLOBAL - plus de filtre agent

        // Initialiser le montant du bilan avec le totalPrixTransit des colis
        $montantBilan = $totalPrixTransitColis;

        // Appliquer les opérations comptables au montant du bilan (GLOBAL - toutes les opérations)
        foreach ($operationsComptablesBilan as $operation) {
            if ($operation->type_operation === 'ENTREE D\'ARGENT') {
                $montantBilan += $operation->montant;
            } elseif ($operation->type_operation === 'SORTIE D\'ARGENT') {
                $montantBilan -= $operation->montant;
            }
        }

        // ================ STATISTIQUES TRANSPORT (GLOBALES - plus de filtre agent) ================
        // Aérien
        $volCargaisonCount = Colis::where('mode_transit', 'aérien')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count(); // GLOBAL - plus de filtre agent

        $colisAerienCount = Colis::where('mode_transit', 'aérien')
            ->where('etat', 'Validé')
            ->count(); // GLOBAL - plus de filtre agent

        $volValideCount = Colis::where('mode_transit', 'aérien')
            ->where('etat', 'Validé')
            ->count(); // GLOBAL - plus de filtre agent

        $volEnCoursCount = Colis::where('mode_transit', 'aérien')
            ->whereIn('etat', ['En entrepôt', 'Chargé'])
            ->count(); // GLOBAL - plus de filtre agent

        $volAnnuleCount = Colis::where('mode_transit', 'aérien')
            ->where('etat', 'Annulé')
            ->count(); // GLOBAL - plus de filtre agent

        // Maritime
        $conteneurCount = Colis::where('mode_transit', 'maritime')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->count(); // GLOBAL - plus de filtre agent

        $colisMaritimeCount = Colis::where('mode_transit', 'maritime')
            ->where('etat', 'Validé')
            ->count(); // GLOBAL - plus de filtre agent

        $conteneurValideCount = Colis::where('mode_transit', 'maritime')
            ->where('etat', 'Validé')
            ->count(); // GLOBAL - plus de filtre agent

        $conteneurEnCoursCount = Colis::where('mode_transit', 'maritime')
            ->whereIn('etat', ['En entrepôt', 'Chargé'])
            ->count(); // GLOBAL - plus de filtre agent

        $conteneurAnnuleCount = Colis::where('mode_transit', 'maritime')
            ->where('etat', 'Annulé')
            ->count(); // GLOBAL - plus de filtre agent

        // ================ GRAPHIQUE COLIS PAR MOIS (GLOBAL) ================
        $currentYear = now()->year;

        $colisParMois = Colis::select(
            DB::raw('MONTH(updated_at) as mois'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('updated_at', $currentYear)
            ->groupBy(DB::raw('MONTH(updated_at)'))
            ->orderBy(DB::raw('MONTH(updated_at)'))
            ->get()
            ->keyBy('mois'); // GLOBAL - plus de filtre agent

        $moisNoms = ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];

        $colisData = [];
        for ($i = 1; $i <= 12; $i++) {
            $colisData[] = $colisParMois->has($i) ? (int)$colisParMois[$i]->total : 0;
        }

        // ================ GESTION DES AGENTS (GLOBAL - tous les agents) ================
        $agents = Agent::withCount(['colisValides as colis_valides_count' => function ($query) {
            $query->where('etat', 'Validé');
        }])
            ->orderBy('nom')
            ->get(); // GLOBAL - tous les agents

        $selectedAgentId = $request->input('agent_id'); // Garder pour la sélection d'agent dans la vue admin
        $agentColis = [];
        $agentTotals = ['totalPrix' => '0.00', 'totalPaye' => '0.00', 'totalResteAPayer' => '0.00'];

        if ($selectedAgentId) {
            // Récupérer les colis de l'agent avec leurs paiements
            $colisCollection = Colis::with('paiements')
                ->where('etat', 'Validé')
                ->where('agent_id', $selectedAgentId)
                ->get();
        
            // Grouper les colis par leur référence
            $colisGroupes = $colisCollection->groupBy('reference_colis');
        
            $agentColis = []; // Tableau final pour la vue
        
            // Itérer sur chaque groupe de colis (ex: tous les colis 'CA-0001-A1')
            foreach ($colisGroupes as $reference => $colisDuGroupe) {
                
                // --- NOUVELLE LOGIQUE ---
                // 1. Récupérer tous les paiements liés au groupe et les dédoublonner
                $paiementsUniques = $colisDuGroupe->flatMap(function ($colis) {
                    return $colis->paiements;
                })->unique('id'); // 'id' est la clé primaire de la table paiements
        
                // 2. Calculer les totaux à partir de la table PAIEMENTS uniquement
                // Le "Prix Total" est la somme de la colonne 'montant' des paiements
                $prixTotalGroupe = $paiementsUniques->sum('montant');
                
                // Le "Montant Payé" est la somme de la colonne 'montant_paye' des paiements
                $montantPayeGroupe = $paiementsUniques->sum('montant_paye');
        
                // 3. Calculer le reste à payer
                $resteAPayerGroupe = $prixTotalGroupe - $montantPayeGroupe;
        
                // 4. Récupérer la date du paiement le plus récent pour l'affichage
                $dernierPaiement = $paiementsUniques->sortByDesc('date_validation')->first();
        
                // 5. Assembler la ligne finale pour le tableau
                $agentColis[] = [
                    'reference_colis' => $reference,
                    'date_paiement'   => $dernierPaiement ? $dernierPaiement->date_validation->format('Y-m-d H:i:s') : 'Non payé',
                    'mode_transit'    => $colisDuGroupe->first()->mode_transit, // Le mode est le même pour tout le groupe
                    'prix_colis'      => number_format($prixTotalGroupe, 2, '.', ''),
                    'montant_paye'    => number_format($montantPayeGroupe, 2, '.', ''),
                    'reste_a_payer'   => number_format($resteAPayerGroupe, 2, '.', ''),
                ];
            }
        
            // On supprime le calcul des totaux globaux comme demandé
            $agentTotals = null; 
        }

        // Récupérer les opérations comptables (les 10 dernières GLOBAL)
        $operationsComptables = OperationComptable::with('agent')->latest()->take(10)->get(); // GLOBAL - plus de filtre agent

        // Récupérer la liste des conteneurs uniques depuis les colis validés/en cours/chargés (GLOBAL)
        $conteneursDisponibles = Colis::whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->whereNotNull('reference_contenaire')
            ->distinct()
            ->pluck('reference_contenaire'); // GLOBAL - plus de filtre agent
// ================ CALCUL DES TOTAUX PAR AGENCE ================
$etatsColisActifs = ['Validé', 'Fermé', 'En entrepôt', 'Chargé'];

// Total pour l'agence Louis Bleriot
$totalTransitLouisBleriot = Colis::whereIn('etat', $etatsColisActifs)
    ->whereHas('agent.agence', function ($query) {
        $query->where('nom_agence', 'AFT Agence Louis Bleriot');
    })
    ->sum('prix_transit_colis');

// Total pour l'agence de Chine
$totalTransitChine = Colis::whereIn('etat', $etatsColisActifs)
    ->whereHas('agent.agence', function ($query) {
        $query->where('nom_agence', 'Agence de Chine');
    })
    ->sum('prix_transit_colis');
// --- NOUVELLES MODIFICATIONS ---

        // 1. Instancier le service de conversion
        $converter = new CurrencyConverterService();

        // 2. Convertir le total de la Chine en Euros pour l'affichage secondaire
        $totalTransitChineEnEuros = $converter->convertCfaToEur($totalTransitChine);

        // 3. RECALCULER le total global en additionnant les montants DANS LA MÊME DEVISE (€)
        $totalPrixTransitColis = $totalTransitLouisBleriot + $totalTransitChineEnEuros;

        // --- FIN DES MODIFICATIONS ---

        return view('admin.bilan.bilan', compact(
            'colisData', 'moisNoms', 'customers_count', 'products_count',
            'colisCount', 'totalPrixTransitColis', // Cette variable contient maintenant le total correct
            'volCargaisonCount', 'colisAerienCount', 'volValideCount', 'volEnCoursCount',
            'volAnnuleCount', 'conteneurCount', 'colisMaritimeCount', 'conteneurValideCount',
            'conteneurEnCoursCount', 'conteneurAnnuleCount', 'agents', 'agentColis', 'agentTotals',
            'selectedAgentId', 'operationsComptables', 'montantBilan', 'conteneursDisponibles', 
            'totalTransitLouisBleriot', 'totalTransitChine',
            'totalTransitChineEnEuros' // On passe la nouvelle variable à la vue
        ));
    }

    public function enregistrerOperation(Request $request)
    {
        // 1. Validation des données (important !)
        $validatedData = $request->validate([
            'date_operation' => 'required|date',
            'type_operation' => 'required|string',
            'beneficiaire_fournisseur' => 'nullable|string',
            'objet' => 'nullable|string',
            'montant' => 'required|numeric',
            'conteneur_frais_fonction' => 'nullable|string',
            'agent_id' => 'nullable|exists:agents,id', // Agent ID est optionnel pour admin, mais doit exister si fourni
        ]);

        // 2. Enregistrer l'opération comptable dans la base de données
        OperationComptable::create($validatedData);

        // 3. Redirection avec un message de succès (optionnel)
        return redirect()->route('bilan.bilan')->with('success', 'Opération comptable enregistrée avec succès.');
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

    public function exportOperationsComptablesToExcel(Request $request)
    {
        return Excel::download(
            new OperationComptableGexport(null), // Passer agentId à null pour export GLOBAL
            'operations_comptables_global_' . now()->format('Y-m-d') . '.xlsx' // Nom de fichier GLOBAL
        );
    }
}