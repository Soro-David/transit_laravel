<?php

namespace App\Exports;

use App\Models\OperationComptable; // Assurez-vous que le chemin vers votre modèle est correct
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Pour définir les en-têtes de colonnes

class OperationComptableGexport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Récupérez les données des opérations comptables depuis votre base de données.
        // Vous pouvez utiliser Eloquent pour cela.
        return OperationComptable::all(); // Récupère toutes les opérations comptables
        // Vous pouvez ajouter des conditions de filtrage ici si nécessaire, par exemple :
        // return OperationComptable::where('agent_id', $agentId)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Définissez les en-têtes de colonnes pour votre fichier Excel.
        // Ces en-têtes correspondront aux colonnes de votre tableau d'opérations comptables.
        return [
            'Date',
            'Type',
            'Bénéficiaire / Fournisseur',
            'Objet',
            'Montant (€)',
            'Conteneur / Frais',
            // Ajoutez ici tous les en-têtes de colonnes que vous souhaitez exporter.
            // Assurez-vous que l'ordre correspond aux données retournées par la méthode collection().
        ];
    }
}