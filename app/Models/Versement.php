<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Versement extends Model
{
    use HasFactory;

    protected $fillable = [
       
        'montant_versement',
        'agent_id',
        'colis_id', // Ajout de la nouvelle colonne
    ];

    // Un versement appartient à un dossier de paiement principal.
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    // Un versement est lié à un colis (celui de référence du groupe).
    public function colis()
    {
        return $this->belongsTo(Colis::class);
    }

    // Un versement est enregistré par un agent.
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}