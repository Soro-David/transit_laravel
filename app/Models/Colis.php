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
        'description_colis',
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
        'dimension_result',
        'hauteur',
        'largeur',
        'longueur',
        'type_colis',
        'recup',
        'service',
        'devise',
        'agent_id',
        'categorie_client',
        'id_reference'
        
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
    public function validateur() // Nom de la relation, vous pouvez choisir un autre nom
    {
        return $this->belongsTo(Agent::class, 'agent_id'); // 'agent_id' est la clé étrangère dans la table 'colis'
    }
    public function agentValidateur()
{
    return $this->belongsTo(Agent::class, 'agent_id')->withDefault([
        'nom' => 'N/A',
        'prenom' => ''
    ]);
}

public function agent()
{
    // 'agent_id' est la clé étrangère dans la table 'colis'
    // 'id' est la clé primaire dans la table 'agents'
    return $this->belongsTo(Agent::class, 'agent_id');
}
public function paiements() // Notez le "s" à la fin
{
    return $this->hasMany(\App\Models\Paiement::class);
}
}
