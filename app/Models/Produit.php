<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'prix', 'categorie', 'description', 'agence'
    ];

    public function colis()
    {
        return $this->belongsTo(Colis::class);
    }
}
