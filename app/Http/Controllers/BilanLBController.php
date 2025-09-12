<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AgentColisExport;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use App\Models\Agent;
use App\Models\Produit;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;
use App\Models\Colis;
use App\Models\Expediteur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OperationComptable;
use App\Exports\OperationsComptablesExport;

class BilanLBController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'agent connecté
        $agent = Auth::user()->agent;
        $agentId = $agent ? $agent->id : null;

        // NOUVEAU: Calcul du montant total payé reçu par l'agent
        $montantTotalPayeAgent = DB::table('paiements')
        ->join('colis', 'paiements.colis_id', '=', 'colis.id')
        ->where('colis.agent_id', $agentId)
        ->sum('paiements.montant_paye');

        // Statistiques de base filtrées par l'agent
        $customers_count = Expediteur::count();
        $products_count = Produit::count();
        
        $colisCount = Colis::where('etat', 'Validé')
            ->when($agentId, fn($q) => $q->where('agent_id', $agentId))
            ->count();
            
        // Calcul du totalPrixTransit BASIQUE (basé sur les colis, comme avant)
        $totalPrixTransitColis = DB::table('paiements')
        ->join('colis', 'paiements.colis_id', '=', 'colis.id')
        ->where('colis.agent_id', $agentId)
        ->sum('paiements.montant');

        // Récupérer TOUTES les opérations comptables de l'agent
        $operationsComptablesBilan = OperationComptable::where('agent_id', $agentId)->get();

        // Initialiser le montant du bilan avec le totalPrixTransit des colis
        if ($agentId) {
    $montantBilan = (float) DB::table('operation_comptables')
        ->select(DB::raw("
            COALESCE(SUM(
                CASE
                    WHEN type_operation = 'ENTREE D''ARGENT' THEN montant
                    WHEN type_operation = 'SORTIE D''ARGENT' THEN -montant
                    ELSE montant
                END
            ), 0) as total
        "))
        ->where('agent_id', $agentId)
        ->value('total');
} else {
    // Si pas d'agent connecté, on peut afficher 0 ou la somme globale selon ton besoin
    $montantBilan = (float) DB::table('operation_comptables')
        ->select(DB::raw("
            COALESCE(SUM(
                CASE
                    WHEN type_operation = 'ENTREE D''ARGENT' THEN montant
                    WHEN type_operation = 'SORTIE D''ARGENT' THEN -montant
                    ELSE montant
                END
            ), 0) as total
        "))
        ->value('total');
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

        // $montantBilan = $totalPrixTransit;
        
        return view('AFT_LOUIS_BLERIOT.bilan.bilanLb', compact(
            'colisData', 'moisNoms', 'customers_count', 'products_count',
            'colisCount', 'totalPrixTransitColis', 'volCargaisonCount', 'colisAerienCount',
            'volValideCount', 'volEnCoursCount', 'volAnnuleCount', 'conteneurCount',
            'colisMaritimeCount', 'conteneurValideCount', 'conteneurEnCoursCount',
            'conteneurAnnuleCount', 'agentColis', 'agentTotals', 'selectedAgentId',
            'operationsComptables', 'montantBilan', 'conteneursDisponibles', 'agentId',
            'montantTotalPayeAgent' // Ajout de la nouvelle variable
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
            'agent_id' => 'nullable|exists:agents,id', // le formulaire peut l'envoyer
        ]);
    
        // Fallback sûr : si le formulaire n'envoie pas agent_id on prend l'agent connecté
        $agentId = $validatedData['agent_id'] ?? (Auth::check() && Auth::user()->agent ? Auth::user()->agent->id : null);
    
        $referenceColis = null;
        if ($validatedData['type_operation'] === "ENTREE D'ARGENT"
            && str_starts_with($validatedData['beneficiaire_fournisseur'], 'COLIS-')) {
            $referenceColis = str_replace('COLIS-', '', $validatedData['beneficiaire_fournisseur']);
        }
    
        DB::beginTransaction();
        try {
            // Créer l'opération comptable
            OperationComptable::create([
                'date_operation' => $validatedData['date_operation'],
                'type_operation' => $validatedData['type_operation'],
                'beneficiaire_fournisseur' => $validatedData['beneficiaire_fournisseur'],
                'objet' => $validatedData['objet'] ?? null,
                'montant' => $validatedData['montant'],
                'conteneur_frais_fonction' => $validatedData['conteneur_frais_fonction'] ?? null,
                'agent_id' => $agentId,
                'reference_colis' => $referenceColis,
            ]);
    
            // Si paiement d'un colis => gérer paiements
            if ($referenceColis) {
                $colisConcerned = Colis::where('reference_colis', $referenceColis)
                                       ->where('etat','Validé')
                                       ->get();
    
                if ($colisConcerned->isEmpty()) {
                    throw new \Exception("Aucun colis valide trouvé pour la référence " . $referenceColis);
                }
    
                $colisIds = $colisConcerned->pluck('id')->toArray();
                $totalDu = $colisConcerned->sum('prix_transit_colis');
                $firstColis = $colisConcerned->first();
    
                // Cherche un paiement existant soit via paiements.colis_id, soit via colis.paiement_id
                $paiement = Paiement::whereIn('colis_id', $colisIds)->orderByDesc('date_validation')->first();
    
                if (!$paiement) {
                    $paiementIdsFromColis = $colisConcerned->pluck('paiement_id')->filter()->unique()->toArray();
                    if (!empty($paiementIdsFromColis)) {
                        $paiement = Paiement::whereIn('id', $paiementIdsFromColis)->orderByDesc('date_validation')->first();
                    }
                }
    
                if ($paiement) {
                    // Mettre à jour montant_paye
                    $paiement->montant_paye = ($paiement->montant_paye ?? 0) + $validatedData['montant'];
                    // si montant total renseigné est null, on peut le mettre à totalDu si besoin
                    if (empty($paiement->montant)) {
                        $paiement->montant = $paiement->montant ?? $totalDu;
                    }
                    $paiement->agent_id = $paiement->agent_id ?? $agentId ?? $firstColis->agent_id;
                    $paiement->date_validation = $paiement->date_validation ?? now();
                    $paiement->statut_paiement = ($paiement->montant_paye >= $paiement->montant) ? 'payé' : 'partiellement payé';
                    $paiement->save();
                } else {
                    // Créer un nouveau dossier de paiement et lier aux colis
                    $paiement = Paiement::create([
                        'colis_id' => $firstColis->id, // principal colis de référence
                        'montant' => $totalDu,
                        'montant_paye' => $validatedData['montant'],
                        'statut_paiement' => ($validatedData['montant'] >= $totalDu) ? 'payé' : 'partiellement payé',
                        'expediteur_id' => $firstColis->expediteur_id,
                        'agent_id' => $agentId ?? $firstColis->agent_id,
                        'methode_paiement' => 'especes',
                        'date_validation' => now(),
                    ]);
    
                    // Associer le paiement créé à tous les colis du groupe
                    Colis::whereIn('id', $colisIds)->update(['paiement_id' => $paiement->id]);
                }
    
                // Mise à jour finale du statut de tous les colis concernés
                $newStatus = ($paiement->montant_paye >= $paiement->montant) ? 'payé' : 'partiellement payé';
                Colis::whereIn('id', $colisIds)->update(['status' => $newStatus]);
            }
    
            DB::commit();
            return redirect()->route('bilan.lb')->with('success', 'Opération comptable enregistrée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'enregistrement de l'opération: " . $e->getMessage(), ['validated' => $validatedData]);
            return redirect()->back()->with('error', 'Une erreur est survenue. L\'opération n\'a pas été enregistrée.');
        }
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