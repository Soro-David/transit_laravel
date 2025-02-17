<?php

namespace App\Http\Controllers;
use App\Models\Colis;
use Illuminate\Http\Request;
use App\Models\Chauffeur;
use App\Models\Programme;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChauffeurColisController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:chauffeur');
    }
    public function index()
    {
        $userId = Auth::id();

        // Récupérer l'ID du chauffeur à partir de la table users
        $chauffeur = Chauffeur::where('email', Auth::user()->email)->first();
        if($chauffeur) {
            $chauffeurId = $chauffeur->id;
             $programmes = Programme::where('chauffeur_id', $chauffeurId)->get();
               $data = $programmes->map(function ($programme) {
                    return [
                        'id' => $programme->id, // IMPORTANT : Inclure l'ID du programme
                        'date_programme_formatted' => Carbon::parse($programme->date_programme)->format('d/m/Y'),
                        'reference_colis' => $programme->reference_colis,
                        'actions_a_faire' => $programme->actions_a_faire,
                        'nom_expediteur' => $programme->nom_expediteur,
                        'nom_destinataire' => $programme->nom_destinataire,
                       'lieu_destinataire' => $programme->lieu_destinataire,
                       'etat_rdv'=> $programme->etat_rdv,
                    ];
                 });
            return view('chauffeur.programme', compact('data'));
        }
    }

    public function updateEtatRdv(Request $request, Programme $programme) // Injection de dépendance pour Programme
    {
        $request->validate([
            'etat_rdv' => 'required|in:en cours,effectué,à replanifié', // Validation de l'état du RDV
        ]);

        $programme->etat_rdv = $request->etat_rdv;
        $programme->save();

        // Vous pouvez ajouter une redirection ou un message de succès ici
        return redirect()->route('chauffeur.programme.index')->with('success', 'État du RDV mis à jour avec succès.');
    }
  

}