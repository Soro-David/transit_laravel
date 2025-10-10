<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
<<<<<<< HEAD
use App\Models\ProgrammeItems;
=======
>>>>>>> 3d2bf99a86ea0e6f6f26527573ed1e52b9117280

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
        'qr_code'
    ];
    
    protected $casts = [
        'date_programme' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation avec User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function colis(): BelongsTo
    {
        return $this->belongsTo(Colis::class, 'reference_colis', 'reference_colis');
    }

    // Relation avec Devis
    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class, 'reference_colis', 'reference');
    }
<<<<<<< HEAD
     // Relation avec l'agent qui a créé le programme
     public function agent()
     {
         return $this->belongsTo(User::class, 'agent_id');
     }
 
 // Relation avec ProgrammeItem
 public function programmeItems()
 {
     return $this->hasMany(ProgrammeItems::class, 'programme_id');
 }
 
=======

    // Relation avec les programmes de dépôt pour la récupération
    public function programmeDepot()
    {
        return $this->belongsTo(Programme::class, 'reference_colis', 'reference_generee')
            ->where('actions_a_faire', 'depot')
            ->where('etat_rdv', 'effectué');
    }

    // CORRECTION : Relation avec ProgrammeItems
    public function items(): HasMany
    {
        return $this->hasMany(ProgrammeItems::class, 'programme_id');
    }

    // Relation avec l'agent qui a créé le programme
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // CORRECTION : Supprimer la relation programmeItems() en double
    // public function programmeItems() // ❌ À supprimer
    // {
    //     return $this->hasMany(ProgrammeItem::class, 'programme_id');
    // }
>>>>>>> 3d2bf99a86ea0e6f6f26527573ed1e52b9117280
}