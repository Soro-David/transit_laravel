<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Programme;
use App\Models\Paiement;
use App\Models\Colis;
use Carbon\Carbon;

class ChauffeurAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'chauffeur') {
                return redirect()->route('chauffeur.dashboard');
            }
            Auth::logout();
            return redirect()->back()->with('error', 'Vous n\'avez pas le rôle chauffeur.');
        }

        return redirect('/login')->with('error', 'Email ou mot de passe incorrect.');
    }
    
    public function dashboard()
    {
        $chauffeur = Chauffeur::where('email', Auth::user()->email)->first();
        if (!$chauffeur) {
            return view('chauffeur.programme', ['data' => []])->with('error', 'Profil chauffeur non trouvé.');
        }
        
        // Debug: Vérifions l'ID du chauffeur
        // dd($chauffeur->id);
        
        // 1. Missions aujourd'hui
        $missionsAujourdhui = Programme::where('chauffeur_id', $chauffeur->id)
            ->whereDate('date_programme', Carbon::today())
            ->count();

        // 2. Missions effectuées
        $missionsEffectuees = Programme::where('chauffeur_id', $chauffeur->id)
            ->where('etat_rdv', 'effectué')
            ->count();
        
        // 3. Total encaissé
        $totalEncaisse = 0;
        
        // Récupérer les références des colis effectués
        $referencesColisEffectues = Programme::where('chauffeur_id', $chauffeur->id)
            ->where('etat_rdv', 'effectué')
            ->pluck('reference_colis');
        
        // Debug: Vérifions les références de colis
        // dd($referencesColisEffectues);
        
        if ($referencesColisEffectues->isNotEmpty()) {
            // Trouver les IDs des colis correspondants
            $colisIds = Colis::whereIn('reference_colis', $referencesColisEffectues)
                ->pluck('id');
                
            // Debug: Vérifions les IDs de colis
            // dd($colisIds);
            
            if ($colisIds->isNotEmpty()) {
                $totalEncaisse = Paiement::whereIn('colis_id', $colisIds)
                    ->sum('montant_paye');
            }
        }
        
        // 4. Répartition des statuts de RDV
        $statutsRdv = Programme::where('chauffeur_id', $chauffeur->id)
            ->select('etat_rdv', DB::raw('count(*) as total'))
            ->groupBy('etat_rdv')
            ->pluck('total', 'etat_rdv');
            
        $pieChartData = [
            'labels' => $statutsRdv->keys()->map(function($item) { 
                return ucfirst(str_replace('_', ' ', $item)); 
            }),
            'data' => $statutsRdv->values(),
        ];

        // 5. Activité des 7 derniers jours
        $activiteHebdomadaire = Programme::where('chauffeur_id', $chauffeur->id)
            ->whereBetween('date_programme', [
                Carbon::now()->subDays(6)->startOfDay(), 
                Carbon::now()->endOfDay()
            ])
            ->where('etat_rdv', 'effectué')
            ->select(DB::raw('DATE(date_programme) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date');

        // Assurer que tous les jours de la semaine sont présents
        $barChartLabels = [];
        $barChartValues = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $barChartLabels[] = Carbon::parse($date)->format('d/m');
            $barChartValues[] = $activiteHebdomadaire->get($date, 0);
        }
        
        $barChartData = [
            'labels' => $barChartLabels,
            'data' => $barChartValues,
        ];
        
        // Debug: Vérifions les données finales
        // dd(compact('missionsAujourdhui', 'missionsEffectuees', 'totalEncaisse', 'pieChartData', 'barChartData'));
        
        return view('chauffeur.dashboard', compact(
            'missionsAujourdhui',
            'missionsEffectuees',
            'totalEncaisse',
            'pieChartData',
            'barChartData'
        ));
    }

    public function index()
    {
        return view('chauffeur.auth.login');
    }
}