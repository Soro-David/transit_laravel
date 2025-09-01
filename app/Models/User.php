<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','first_name',
        'last_name', 
        'email', 
        'password', 
        'role',
        'agence_id',
        'tel',
        'adresse',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->belongsToMany(Product::class, 'user_cart')->withPivot('quantity');
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function getFullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getMail()
    {
        return $this->email;
    }
    public function getIdUser()
    {
        return $this->id;
    }

    public function getAvatar()
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->email);
    }
    
//     public function agent()
// {
//     return $this->belongsTo(Agent::class, 'agent_id');
// }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function agent() 
    {
        return $this->hasOne(Agent::class, 'email', 'email'); // En supposant que 'email' est la colonne de liaison
    }

    public function expediteur()
    {
        return $this->hasOne(Expediteur::class);
    }

    public function destinataire()
    {
        return $this->hasOne(Destinataire::class);
    }
    
}
