<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expediteur extends Model
{
    protected $fillable = ['nom', 'prenom', 'email','tel','lieu_expedition','agence','adresse'];

    // Relation avec Colis
    public function colis()
    {
        return $this->hasMany(Colis::class);
    }
}
