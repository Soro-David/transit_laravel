<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colis extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_entree', 
        'date_sortie', 
        'etat', 
        'client_id',
        'reference_colis',
        'quantite_colis',
        'valeur_colis',
        'poids_colis',
        'dimension_colis',
        'mode_transit',
        'description_colis',
        'status',
        'type_embalage',
        'reference_contenaire',
        'expediteur_id',
        'destinataire_id',
        'paiement_id',
        'qr_code_path',
        'chauffeur_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
    // Relation avec Expediteur
    public function expediteur()
    {
        return $this->belongsTo(Expediteur::class);
    }

    // Relation avec Destinataire
    public function destinataire()
    {
        return $this->belongsTo(Destinataire::class);
    }

    // Relation avec Paiement
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    // Relation avec Chauffeur
    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    // Relation avec Article (Relation plusieurs-à-plusieurs)
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}
