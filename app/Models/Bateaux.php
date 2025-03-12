<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bateaux extends Model
{
    use HasFactory;

    protected $table = 'bateaux'; 

    protected $fillable = [
        'reference_bateau',
        'reference_conteneur',
        'type',
        'date_arriver',
        'compagnie',
        'nom_bateau',
        'numero_bateau',
        'agence_destination',
        'nom_ballon',
        'numero_ballon',
    ];
}
