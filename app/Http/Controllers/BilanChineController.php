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
use App\Models\OperationComptable;
use Illuminate\Support\Facades\Auth;
use App\Exports\OperationsComptablesExport;
class BilanChineController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'agent connecté
        $agent = Auth::user()->agent;
        $agentId = $agent ? $agent->id : null;

        // Statistiques de base filtrées par l'agent
        $customers_count = Expediteur::count();
        $products_count = Produit::count();
        
        $colisCount = Colis::where('etat', 'Validé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
            
        // Calcul du totalPrixTransit BASIQUE (basé sur les colis, comme avant)
    $totalPrixTransitColis = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
    ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
    ->sum('prix_transit_colis');

// Récupérer TOUTES les opérations comptables de l'agent
$operationsComptablesBilan = OperationComptable::where('agent_id', $agentId)->get();

// Initialiser le montant du bilan avec le totalPrixTransit des colis
$montantBilan = $totalPrixTransitColis;

// Appliquer les opérations comptables au montant du bilan
foreach ($operationsComptablesBilan as $operation) {
    if ($operation->type_operation === 'ENTREE D\'ARGENT') {
        $montantBilan += $operation->montant; // Ajouter pour ENTREE D'ARGENT
    } elseif ($operation->type_operation === 'SORTIE D\'ARGENT') {
        $montantBilan -= $operation->montant; // Soustraire pour SORTIE D'ARGENT
    }
}

        // ================ STATISTIQUES TRANSPORT ================
        // Aérien
        $volCargaisonCount = Colis::where('mode_transit', 'aérien')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $colisAerienCount = Colis::where('mode_transit', 'aérien')
            ->where('etat', 'Validé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $volValideCount = Colis::where('mode_transit', 'aérien')
            ->where('etat', 'Validé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $volEnCoursCount = Colis::where('mode_transit', 'aérien')
            ->whereIn('etat', ['En entrepôt', 'Chargé'])
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $volAnnuleCount = Colis::where('mode_transit', 'aérien')
            ->where('etat', 'Annulé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();

        // Maritime
        $conteneurCount = Colis::where('mode_transit', 'maritime')
            ->whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $colisMaritimeCount = Colis::where('mode_transit', 'maritime')
            ->where('etat', 'Validé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $conteneurValideCount = Colis::where('mode_transit', 'maritime')
            ->where('etat', 'Validé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $conteneurEnCoursCount = Colis::where('mode_transit', 'maritime')
            ->whereIn('etat', ['En entrepôt', 'Chargé'])
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
        
        $conteneurAnnuleCount = Colis::where('mode_transit', 'maritime')
            ->where('etat', 'Annulé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();

        // ================ GRAPHIQUE COLIS PAR MOIS ================
        $currentYear = now()->year;

        $colisParMois = Colis::select(
                DB::raw('MONTH(updated_at) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
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
        $selectedAgentId = $agentId;
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

        // Récupérer les opérations comptables avec l'agent associé
        $operationsComptables = OperationComptable::with('agent')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->latest()
            ->take(10)
            ->get();

        // Récupérer la liste des conteneurs
        $conteneursDisponibles = Colis::whereIn('etat', ['Validé', 'En entrepôt', 'Chargé'])
            ->whereNotNull('reference_contenaire')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->distinct()
            ->pluck('reference_contenaire');

       
        
        return view('AGENCE_CHINE.bilan.bilanchine', compact(
            'colisData', 'moisNoms', 'customers_count', 'products_count',
            'colisCount', 'totalPrixTransitColis', 'volCargaisonCount', 'colisAerienCount',
            'volValideCount', 'volEnCoursCount', 'volAnnuleCount', 'conteneurCount',
            'colisMaritimeCount', 'conteneurValideCount', 'conteneurEnCoursCount',
            'conteneurAnnuleCount', 'agentColis', 'agentTotals', 'selectedAgentId',
            'operationsComptables', 'montantBilan', 'conteneursDisponibles', 'agentId'
        ));
    }

    public function enregistrerOperation(Request $request)
    {
        $validatedData = $request->validate([
            'date_operation' => 'required|date',
            'type_operation' => 'required|string|in:ENTREE D\'ARGENT,SORTIE D\'ARGENT',
            'beneficiaire_fournisseur' => 'nullable|string|max:255',
            'objet' => 'nullable|string|max:255',
            'montant' => 'required|numeric|min:0',
            'conteneur_frais_fonction' => 'nullable|string|max:255',
        ]);
    
        // Ajout automatique de l'agent connecté
        $validatedData['agent_id'] = Auth::user()->agent->id;
        $agentId = $validatedData['agent_id']; // Récupérer agentId pour la requête
    
        // Récupérer le totalPrixTransit ACTUEL (avant l'opération)
        $totalPrixTransit = Colis::whereIn('etat', ['Validé', 'Fermé', 'En entrepôt', 'Chargé'])
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->sum('prix_transit_colis');
    
        $operation = OperationComptable::create($validatedData);
    
        // Mettre à jour le montant du bilan (vous pouvez choisir de mettre à jour un champ dans la base de données si vous le souhaitez,
        // ou simplement recalculer totalPrixTransit à chaque fois dans index() comme vous le faites déjà, ce qui est suffisant ici)
    
        return redirect()->route('bilan.chine')
            ->with('success', 'Opération comptable enregistrée avec succès.');
    }

    public function exportAgentColisToExcel(Request $request)
    {
        $agentId = Auth::check() && Auth::user()->agent 
            ? Auth::user()->agent->id 
            : $request->input('agent_id');

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
    // Récupérer l'agent connecté (vous l'avez déjà fait dans index(), réutilisez ce code)
    $agent = Auth::user()->agent;
    $agentId = $agent ? $agent->id : null;

    // ... (vérification de $agentId si nécessaire)

    return Excel::download(
        new OperationsComptablesExport($agentId), // **CORRECTION : Passer $agentId ici**
        'operations_comptables_agent_' . $agentId . '_' . now()->format('Y-m-d') . '.xlsx'
    );
}
}