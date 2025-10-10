<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DevisItems;
class Devis extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'mode_transit',
        'pays_expedition',
        'agence_expedition',
        'agence_destination',
        'nom_expediteur',
        'prenom_expediteur',
        'email_expediteur',
        'tel_expediteur',
        'adresse_expediteur',
        'montant',
        'devise',
        'user_id',
        'chauffeur_id',
        'etat',
        'mode_de_retrait', // <-- AJOUTER CETTE LIGNE
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chauffeur(): BelongsTo
    {
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id');
    }

    /**
     * Relation : un devis a plusieurs items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(DevisItems::class, 'devis_id');
    }
    public function devisItems(): HasMany
    {
        return $this->items();
    }
    public function calculerMontantTotal()
    {
        return $this->items()->sum('montant');
    }

    // Méthode pour mettre à jour le montant total
    public function mettreAJourMontantTotal()
    {
        $this->montant = $this->calculerMontantTotal();
        $this->save();
    }
}
