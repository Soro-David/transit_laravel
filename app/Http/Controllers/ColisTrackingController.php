<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis; // Assurez-vous d'avoir un modèle Colis

class ColisTrackingController extends Controller
{
    public function track(Request $request)
    {
        // Validez la référence
        $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        $reference = $request->input('reference');
        // dd($reference);
        // Recherche du colis dans la base de données
        // Assurez-vous que le nom du champ dans la base de données est bien 'reference'
        $colis = Colis::where('reference_colis', $reference)->first();

        if ($colis) {
            // Colis trouvé, renvoie les informations en JSON
            return response()->json([
                'success' => true,
                'colis' => [
                    'reference' => $colis->reference_colis,
                    'status' => $colis->status, // Assurez-vous que ces champs existent dans votre modèle Colis
                    'etat' => $colis->etat,
                    'transit' => $colis->mode_transit,
                    // Ajoutez d'autres champs que vous souhaitez afficher
                ]
            ]);
        } else {
            // Colis non trouvé
            return response()->json([
                'success' => false,
                'message' => 'Aucun colis trouvé avec cette référence.'
            ], 404); // Renvoie un statut 404 pour "non trouvé"
        }
    }
}