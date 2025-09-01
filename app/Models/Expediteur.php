<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expediteur extends Model
{
    protected $fillable = ['nom', 'prenom', 'email','tel','lieu_expedition','agence','adresse','user_id'];

    // Relation avec Colis
    public function colis()
    {
        return $this->hasMany(Colis::class);
    }
     // Relation avec les paiements
     public function paiements()
     {
         return $this->hasMany(Paiement::class);
     }

         // Relation avec User
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
