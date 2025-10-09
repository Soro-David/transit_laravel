<?php

namespace App\Http\Controllers\Devis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\ProgrammeItems;

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
     * Récupère les informations d’un programme,
     * y compris les éléments (programme_items).
     */
    public function getItems(Programme $programme)
    {
        // On charge la relation programmeItems
        $programme->load('programmeItems');

        return response()->json([
            'programme' => $programme,
            'items' => $programme->programmeItems, // retour direct des items
        ]);
    }
}
