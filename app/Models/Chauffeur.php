<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    protected $fillable = ['nom', 'prenom', 'email','tel','agence_id'];

    // Relation avec Colis
    public function colis()
    {
        return $this->hasMany(Colis::class);
    }
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
}