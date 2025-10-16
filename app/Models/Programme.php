<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantite',
        'date_programme',
        'user_id',
        'agent_id',
        'reference_colis',
        'reference_generee',
        'type_reference',
        'nature_du_colis',
        'mode_transit',
        'agence_expedition',
        'agence_destination',
        'actions_a_faire',
        'nom_expediteur',
        'prenom_expediteur',
        'email_expediteur',
        'nom_destinataire',
        'lieu_destinataire',
        'tel_expediteur',
        'tel_destinataire',
        'lieu_expedition',
        'lieu_destination',
        'montant',
        'devise',
        'etat_rdv',
        'qr_code',
    ];

    /**
     * IMPORTANT :
     * - 'date_programme' doit être casté en "datetime" (pas 'date') pour conserver l'heure.
     * - Ici je demande un format précis pour la sérialisation JSON : 'Y-m-d H:i:s'
     */
    protected $casts = [
        'date_programme' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * (Optionnel) Ajoute un champ sérialisé en plus pour l'affichage frontal
     * Exemple : '16/10/2025 09:25' -> utile si tu veux l'afficher tel quel côté JS
     */
    protected $appends = [
        'date_programme_formatted',
    ];

    // Relation avec l'utilisateur qui a créé le programme
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec l'agent
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Relation avec le colis (via référence)
    public function colis(): BelongsTo
    {
        return $this->belongsTo(Colis::class, 'reference_colis', 'reference_colis');
    }

    // Relation avec le devis associé
    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class, 'reference_colis', 'reference');
    }

    // Relation avec les éléments du programme (colis détaillés)
    public function items(): HasMany
    {
        return $this->hasMany(ProgrammeItems::class, 'programme_id');
    }

    // Relation avec le programme de dépôt correspondant
    public function programmeDepot(): BelongsTo
    {
        return $this->belongsTo(Programme::class, 'reference_colis', 'reference_generee')
                    ->where('actions_a_faire', 'depot')
                    ->where('etat_rdv', 'effectué');
    }

    // Exemple : recalcul automatique du montant total
    public function mettreAJourMontantTotal(): void
    {
        $this->montant = $this->items()->sum('montant');
        $this->saveQuietly();
    }

    /**
     * Accessor optionnel : renvoie une chaîne lisible pour l'affichage (d/m/Y H:i)
     * Ce champ sera présent dans la JSON grâce à $appends.
     */
    public function getDateProgrammeFormattedAttribute(): ?string
    {
        if (!$this->date_programme) {
            return null;
        }

        // $this->date_programme est un Carbon instance grâce au cast 'datetime'
        return $this->date_programme->format('d/m/Y H:i');
    }
}
