<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'methode_paiement',
        'montant',
        'operateur',
        'banque',
        'NumeroPaiement',
        'id_transaction',
        'statut_paiement',
        'date_validation',
        'colis_id',
        'expediteur_id',
        'agent_id',
        'montant_paye'
    ];

    protected $casts = [
        'date_validation' => 'datetime',
    ];

    // Relation avec Colis (un paiement appartient à un colis)
    public function colis()
    {
        return $this->hasMany(Colis::class); // Si un paiement peut concerner plusieurs colis
    }

    // Relation avec l'expéditeur (un paiement appartient à un expéditeur)
    public function expediteur()
    {
        return $this->belongsTo(Expediteur::class); // Changement ici
    }

    // Relation avec l'agent (un paiement peut être validé par un agent)
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}