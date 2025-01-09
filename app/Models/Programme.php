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
        'nom_expediteur',
        'nom_destinataire',
        'lieu_destinataire',
    ];

    public function chauffeur(): BelongsTo
    {
        return $this->belongsTo(Chauffeur::class);
    }

     //Pas de relation direct pour la table les_colis avec la table programmes.
}