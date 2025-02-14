<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = ['adresse', 'telephone', 'role', 'mot_de_passe', 'user_id', 'agence_id'];

    // Relation : Un agent appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation : Un agent appartient à une agence
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
