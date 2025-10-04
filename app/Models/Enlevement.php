<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enlevement extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type_enlevement',
        'nom_client',
        'prenom_client',
        'contact_client',
        'email_client',
        'nature',
        'quantite',
        'nbre_etiquette',
        'path_qr_code',
    ];

    public function chauffeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
