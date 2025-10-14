<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammeItems extends Model
{
    use HasFactory;

    protected $table = 'programme_items';

    protected $fillable = [
        'programme_id',
        'quantite_colis',
        'service',
        'valeur_colis',
        'type_colis',
        'description_colis',
        'poids',
        'longueur',
        'largeur',
        'hauteur',
        'montant',
    ];

    protected $casts = [
        'quantite_colis' => 'integer',
        'valeur_colis'   => 'decimal:2',
        'poids'          => 'decimal:2',
        'longueur'       => 'decimal:2',
        'largeur'        => 'decimal:2',
        'hauteur'        => 'decimal:2',
        'montant'        => 'decimal:2',
    ];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Met à jour le montant total du programme quand un item est ajouté ou supprimé
        static::saved(function ($item) {
            if ($item->programme && method_exists($item->programme, 'mettreAJourMontantTotal')) {
                $item->programme->mettreAJourMontantTotal();
            }
        });

        static::deleted(function ($item) {
            if ($item->programme && method_exists($item->programme, 'mettreAJourMontantTotal')) {
                $item->programme->mettreAJourMontantTotal();
            }
        });
    }
}
