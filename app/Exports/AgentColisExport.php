<?php

namespace App\Exports;

use App\Models\Colis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AgentColisExport implements FromCollection, WithHeadings
{
    protected $agentId;

    public function __construct($agentId)
    {
        $this->agentId = $agentId;
    }

    public function collection()
    {
        return Colis::where('agent_id', $this->agentId)
            ->where('etat', 'Validé')
            ->with('paiement')
            ->get()
            ->map(function ($colis) {
                $montantPaye = optional($colis->paiement)->montant ?? 0;
                
                return [
                    'Référence' => $colis->reference_colis,
                    'Date création' => $colis->created_at->format('d/m/Y'),
                    'Prix total' => $colis->prix_transit_colis,
                    'Montant payé' => $montantPaye,
                    'Reste à payer' => max(0, $colis->prix_transit_colis - $montantPaye)
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Date création',
            'Prix total (€)',
            'Montant payé (€)',
            'Reste à payer (€)'
        ];
    }
}