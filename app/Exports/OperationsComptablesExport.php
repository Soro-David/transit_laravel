<?php

namespace App\Exports;

use App\Models\OperationComptable; // Assurez-vous que le chemin vers votre modèle est correct
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Pour définir les en-têtes de colonnes

class OperationsComptablesExport implements FromCollection, WithHeadings
{
    protected $agentId; // Déclarer une propriété pour stocker l'agentId

    // Constructeur pour recevoir l'agentId
    public function __construct(int $agentId)
    {
        $this->agentId = $agentId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
         // Filtrer les opérations comptables par agent_id
         return OperationComptable::where('agent_id', $this->agentId)->get();
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