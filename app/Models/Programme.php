<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_programme',
        'chauffeur_id',
        'reference_colis',
        'actions_a_faire',
        'nom_expediteur',
        'nom_destinataire',
        'lieu_destinataire',
        'tel_expediteur',
        'tel_destinataire',
        'lieu_expedition',
        'lieu_destination',
        'etat_rdv', // Ajout du nouveau champ
    ];

    public function chauffeur(): BelongsTo
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function colis(): BelongsTo
    {
        return $this->belongsTo(Colis::class, 'reference_colis', 'reference_colis');
    }
}