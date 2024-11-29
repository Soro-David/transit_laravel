<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 'adresse', 
        'type_facture', 'agence', 'date_inscription'
    ];

    public function colis()
    {
        return $this->hasMany(Colis::class);
    }

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }
}

