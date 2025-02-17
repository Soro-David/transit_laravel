<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use Illuminate\Http\Request;

class Rdvlbcontroller extends Controller
{
    public function index()
    {
        $depotRdvs = Programme::with(['chauffeur' => function ($query) {
                $query->where('agence_id', 5);
            }])
            ->where('actions_a_faire', 'depot')
            ->get();

        $recuperationRdvs = Programme::with(['chauffeur' => function ($query) {
                $query->where('agence_id', 5);
            }])
            ->where('actions_a_faire', 'recuperation')
            ->get();

        // Filter out programmes where chauffeur is null after filtering by agence_id
        $depotRdvs = $depotRdvs->filter(function ($programme) {
            return $programme->chauffeur !== null;
        });

        $recuperationRdvs = $recuperationRdvs->filter(function ($programme) {
            return $programme->chauffeur !== null;
        });

        return view('AFT_LOUIS_BLERIOT.rdv.rdv', compact('depotRdvs', 'recuperationRdvs'));
    }
}

