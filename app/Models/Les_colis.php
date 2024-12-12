<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Les_colis extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'nom_expediteur', 'prenom_expediteur', 'email_expediteur', 'tel_expediteur',
        'agence_expedition', 'lieu_expedition', 'nom_destinataire', 'prenom_destinataire',
        'email_destinataire', 'tel_destinataire', 'agence_destination', 'lieu_destination',
        'quantite', 'type_emballage', 'dimension', 'description_colis', 'poids_colis', 
        'valeur_colis', 'mode_transit', 'reference_colis', 'mode_payement', 'status', 
        'qr_code_path', 'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
