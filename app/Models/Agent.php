<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prenom', 'email', 'password','agence_id','user_id']; // Champs fillable mis à jour

    // ... (vos autres relations existantes, si vous les conservez) ...


    /**
     * Relation : Agent a validé plusieurs colis.
     */
    public function colisValides()
    {
        return $this->hasMany(Colis::class, 'agent_id')->where('etat', 'Validé');
    }
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
     // Relation avec les opérations comptables
     
     public function operationsComptables()
     {
         return $this->hasMany(OperationComptable::class);
     }

   
     
}