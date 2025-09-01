<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinataire extends Model
{
    protected $fillable = ['nom', 'prenom', 'email','tel','lieu_destination','agence','adresse','user_id'];

// Relation avec User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function colis()
    {
        return $this->hasMany(Colis::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}