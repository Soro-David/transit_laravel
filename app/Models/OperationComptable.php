<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationComptable extends Model
{
    use HasFactory;

    // Définir les attributs qui peuvent être remplis via la méthode create() ou update() (Mass Assignment)
    protected $fillable = [
        'date_operation',
        'type_operation',
        'beneficiaire_fournisseur',
        'objet',
        'montant',
        'conteneur_frais_fonction',
        'agent_id' // Ajout du champ agent_id
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_operation' => 'date', // Cast date_operation to a Carbon date object
    ];
   // Relation avec l'agent
   public function agent()
   {
       return $this->belongsTo(Agent::class);
   }
    // Définir les attributs qui NE PEUVENT PAS être remplis via la méthode create() ou update() (Mass Assignment)
    // Si vous utilisez $fillable, vous n'avez généralement pas besoin de $guarded
    // protected $guarded = ['id', 'created_at', 'updated_at']; // Exemple si vous voulez protéger ces champs

    // Indiquer si le modèle utilise des timestamps (created_at et updated_at)
    // Par défaut, c'est true car on a utilisé timestamps() dans la migration.
    // Si vous ne voulez pas de timestamps, décommentez et mettez à false:
    // public $timestamps = false;

    // Définir le nom de la table si ce n'est pas la convention Laravel (snake case pluriel du nom du modèle)
    // Dans ce cas, 'operation_comptables' est la convention, donc pas besoin de le définir ici.
    // protected $table = 'nom_de_votre_table';

    // Définir la clé primaire si ce n'est pas 'id' ou si ce n'est pas un entier auto-incrémenté
    // 'id' est la convention et c'est auto-incrémenté, donc pas besoin de le définir ici.
    // protected $primaryKey = 'ma_cle_primaire';
    // public $incrementing = false; // Si la clé primaire n'est pas auto-incrémentée
    // protected $keyType = 'string'; // Si la clé primaire n'est pas un entier

    // Définir la connexion à la base de données si vous utilisez une connexion différente de la connexion par défaut
    // protected $connection = 'nom_de_la_connexion';
}