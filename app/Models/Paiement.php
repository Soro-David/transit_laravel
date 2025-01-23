<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = ['mode_de_paiement', 
                            'montant_reçu', '
                            operateur_mobile',
                            'numero_tel',
                            'nom_banque',
                            'numero_compte',
                            'id_transaction',
                            'colis_id',
                            'numero_cheque'];

    // Relation avec Colis
    public function colis()
    {
        return $this->hasMany(Colis::class);
    }
}
