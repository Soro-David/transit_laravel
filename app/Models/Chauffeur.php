<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Chauffeur extends Authenticatable implements AuthenticatableContract
{
    use HasFactory, Notifiable;

    protected $fillable = [ 'nom', 'prenom', 'email','tel','agence_id','agence', 'password', ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
        protected $casts = [
            'email_verified_at' => 'datetime',
        ];

    // Relation avec Colis
    public function colis()
    {
    return $this->hasMany(Colis::class);
    }
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
    public function getFullname()
    {
        return "{$this->prenom} {$this->nom}";
    }
}