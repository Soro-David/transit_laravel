<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expediteur; // Un seul modèle Expediteur
use App\Models\Destinataire; // Un seul modèle Destinataire

class InfoAUtoController extends Controller
{
    public function getExpediteur(Request $request)
    {
        $nom = $request->input('nom');
        $prenom = $request->input('prenom');
        $nom_societe = $request->input('nom_societe');

        if ($nom && $prenom) { // Recherche de particulier
            $expediteur = Expediteur::where('type_personne', 'particulier') // Assurez-vous d'avoir cette colonne
                                    ->where('nom', 'LIKE', '%' . $nom . '%')
                                    ->where('prenom', 'LIKE', '%' . $prenom . '%')
                                    ->first();
        } elseif ($nom_societe) { // Recherche de société
            $expediteur = Expediteur::where('type_personne', 'societe') // Assurez-vous d'avoir cette colonne
                                    ->where('nom_societe', 'LIKE', '%' . $nom_societe . '%') // Assurez-vous d'avoir cette colonne
                                    ->first();
        } else {
            return response()->json(['message' => 'Paramètres manquants'], 400);
        }

        if ($expediteur) {
            return response()->json($expediteur);
        }

        return response()->json(['message' => 'Expéditeur non trouvé'], 404);
    }

    public function getDestinataire(Request $request)
    {
        $nom = $request->input('nom');
        $prenom = $request->input('prenom');
        $nom_societe = $request->input('nom_societe');

        if ($nom && $prenom) { // Recherche de particulier
            $destinataire = Destinataire::where('type_personne', 'particulier')
                                        ->where('nom', 'LIKE', '%' . $nom . '%')
                                        ->where('prenom', 'LIKE', '%' . $prenom . '%')
                                        ->first();
        } elseif ($nom_societe) { // Recherche de société
            $destinataire = Destinataire::where('type_personne', 'societe')
                                        ->where('nom_societe', 'LIKE', '%' . $nom_societe . '%')
                                        ->first();
        } else {
            return response()->json(['message' => 'Paramètres manquants'], 400);
        }

        if ($destinataire) {
            return response()->json($destinataire);
        }

        return response()->json(['message' => 'Destinataire non trouvé'], 404);
    }
}