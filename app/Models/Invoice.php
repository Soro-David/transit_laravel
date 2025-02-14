<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'expediteur_id',
        'destinataire_id',
        'agent_id',
        'montant',
        'numero_facture',
        // 'description',
        
    ];

    public function expediteur()
    {
        return $this->belongsTo(Expediteur::class);
    }

    public function destinataire()
    {
        return $this->belongsTo(Destinataire::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
