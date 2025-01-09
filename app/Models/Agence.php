<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agence extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_agence',
        'adresse_agence',
        'pays_agence',
        'devise_agence',
        'prix_au_kg',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function chauffeur()
    {
        return $this->hasMany(Chauffeur::class);
    }
}
