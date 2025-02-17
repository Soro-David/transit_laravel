<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use Illuminate\Http\Request;

class RdvController extends Controller
{
    public function index()
    {
        $depotRdvs = Programme::with('chauffeur')
            ->where('actions_a_faire', 'depot')
            ->get();

        $recuperationRdvs = Programme::with('chauffeur')
            ->where('actions_a_faire', 'recuperation')
            ->get();

        return view('admin.rdv.rdv', compact('depotRdvs', 'recuperationRdvs'));
    }
}