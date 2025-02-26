<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use Illuminate\Http\Request;

class RdvchineController extends Controller
{
    public function index()
    {
        $depotRdvs = Programme::with(['chauffeur' => function ($query) {
                $query->where('agence_id', 7);
            }])
            ->where('actions_a_faire', 'depot')
            ->get();
    
        $recuperationRdvs = Programme::with(['chauffeur' => function ($query) {
                $query->where('agence_id', 7);
            }])
            ->where('actions_a_faire', 'recuperation')
            ->get();
    
        // Ajout de la section Voler Livraison
        $livraisonRdvs = Programme::with(['chauffeur' => function ($query) {
                $query->where('agence_id', 7);
            }])
            ->where('actions_a_faire', 'livraison')
            ->get();
    
        // Filtrage des résultats
        $depotRdvs = $depotRdvs->filter(fn($p) => $p->chauffeur);
        $recuperationRdvs = $recuperationRdvs->filter(fn($p) => $p->chauffeur);
        $livraisonRdvs = $livraisonRdvs->filter(fn($p) => $p->chauffeur);
    
        return view('AGENCE_CHINE.rdv.rdv', compact('depotRdvs', 'recuperationRdvs', 'livraisonRdvs'));
    }
}