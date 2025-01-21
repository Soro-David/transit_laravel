<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colis extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_colis',
        'reference_contenaire',
        'quantite_colis',
        'type_embalage',
        'valeur_colis',
        'poids_colis',
        'dimension',
        'expediteur_id',
        'destinataire_id',
        'paiement_id',
        'chauffeur_id',
        'mode_transit',
        'qr_code_path',
        'prix_transit_colis',
        'etat',
        'status',
        'client_id',
        
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function user()
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

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }
}
