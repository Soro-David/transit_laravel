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

            // Récupérer l'utilisateur après l'authentification réussie
            $user = Auth::user();
            if ($user->role === 'chauffeur') {
                // Redirection vers le dashboard chauffeur
                return redirect()->route('chauffeur.dashboard');
            }
             Auth::logout();
             return redirect()->back()->with('error', 'Vous n\'avez pas le rôle chauffeur.');
        }

        return redirect('/login')->with('error', 'Email ou mot de passe incorrect.');
    }
    
    public function dashboard()
    {
        // 1. Récupérer le chauffeur authentifié
        $chauffeur = Auth::user();

      // 2. Calculer les missions du jour
      $missionsAujourdhui = Programme::where('chauffeur_id', $chauffeur->id)
      ->whereDate('date_programme', Carbon::today())
      ->count();

  // 3. Calculer le total des missions effectuées
  $missionsEffectuees = Programme::where('chauffeur_id', $chauffeur->id)
      ->where('etat_rdv', 'effectué')
      ->count();
  
  // 4. Calculer le total encaissé
  $referencesColisEffectues = Programme::where('chauffeur_id', $chauffeur->id)
      ->where('etat_rdv', 'effectué')
      ->pluck('reference_colis');
  
  $totalEncaisse = 0;
  if ($referencesColisEffectues->isNotEmpty()) {
      $colisIds = Colis::whereIn('reference_colis', $referencesColisEffectues)->pluck('id');
      if ($colisIds->isNotEmpty()) {
          $totalEncaisse = Paiement::whereIn('colis_id', $colisIds)->sum('montant_paye');
      }
  }
  
  // 5. Préparer les données pour le graphique de répartition des statuts
  $statutsRdv = Programme::where('chauffeur_id', $chauffeur->id)
      ->select('etat_rdv', DB::raw('count(*) as total'))
      ->groupBy('etat_rdv')
      ->pluck('total', 'etat_rdv');
      
  $pieChartData = [
      'labels' => $statutsRdv->keys()->map(function($item) { return ucfirst(str_replace('_', ' ', $item)); }),
      'data' => $statutsRdv->values(),
  ];

  // 6. Préparer les données pour le graphique d'activité des 7 derniers jours
  $activiteHebdomadaire = Programme::where('chauffeur_id', $chauffeur->id)
      ->whereBetween('date_programme', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
      ->where('etat_rdv', 'effectué')
      ->select(DB::raw('DATE(date_programme) as date'), DB::raw('count(*) as total'))
      ->groupBy('date')
      ->orderBy('date', 'asc')
      ->get()->pluck('total', 'date');

  // Construire le graphique en s'assurant que tous les jours sont présents (même avec 0 mission)
  $barChartLabels = [];
  $barChartValues = [];
  for ($i = 6; $i >= 0; $i--) {
      $date = Carbon::now()->subDays($i)->format('Y-m-d');
      $barChartLabels[] = Carbon::parse($date)->format('d/m');
      $barChartValues[] = $activiteHebdomadaire->get($date, 0); // Utilise 0 si aucune donnée n'existe pour ce jour
  }
  
  $barChartData = [ 'labels' => $barChartLabels, 'data' => $barChartValues ];
  
  // 7. Retourner la vue du dashboard avec toutes les données calculées
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