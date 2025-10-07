<?php

namespace App\Http\Controllers\Devis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programme;

class DevisAutoController extends Controller
{
    /**
     * Recherche les programmes (devis) pour l'autocomplétion.
     */
    public function search(Request $request)
    {
        $query = $request->get('term', '');

        $programmes = Programme::where('reference_generee', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'reference_generee']);

        $formatted = $programmes->map(function($p) {
            return [
                'id' => $p->id,
                'label' => $p->reference_generee,
                'value' => $p->reference_generee,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * [CORRIGÉ]
     * Récupère les informations complètes d'un programme (devis) via son ID,
     * y compris les détails de l'expéditeur/destinataire et les items associés.
     *
     * @param Programme $programme Le modèle Programme injecté par Laravel.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItems(Programme $programme)
    {
        // Charge la relation 'items' pour l'inclure dans la réponse.
        $programme->load('items');

        // La clé est de retourner l'objet 'programme' complet.
        // Le JavaScript pourra alors accéder à response.programme.nom_destinataire, etc.
        return response()->json([
            'programme' => $programme,
            'items' => $programme->items // On garde les items pour un accès direct plus simple dans le JS
        ]);
    }
}