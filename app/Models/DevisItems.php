<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevisItems extends Model
{
    use HasFactory;

    protected $table = 'devis_items';

    protected $fillable = [
        'devis_id',
        'programme_id', // <-- AJOUTER programme_id
        'quantite_colis',
        'service',
        'valeur_colis',
        'type_colis',
        'nom_produit',
        'description_colis',
        'poids',
        'longueur',
        'largeur',
        'hauteur',
    ];

    protected $casts = [
        'quantite_colis' => 'integer',
        'valeur_colis'   => 'decimal:2',
        'poids'          => 'decimal:2',
        'longueur'       => 'decimal:2',
        'largeur'        => 'decimal:2',
        'hauteur'        => 'decimal:2',
    ];

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class, 'devis_id');
    }
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }
}
