<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['description', 'quantite', 'poids','dimension','colis_id'];

    // Relation avec Colis
    public function colis()
    {
        return $this->belongsToMany(Colis::class);
    }
}