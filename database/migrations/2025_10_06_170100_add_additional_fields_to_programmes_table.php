<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_additional_fields_to_programmes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToProgrammesTable extends Migration
{
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            // Nouveaux champs pour les informations du devis
            $table->string('mode_transit')->nullable()->after('nature_du_colis');
            $table->unsignedBigInteger('agent_id')->nullable()->after('user_id');

            // Ajout des champs en ordre logique
            $table->string('agence_expedition')->nullable()->after('mode_transit');
            $table->string('prenom_expediteur')->nullable()->after('nom_expediteur');
            $table->string('email_expediteur')->nullable()->after('prenom_expediteur');

            $table->string('devise')->default('EUR')->after('agence_expedition');

            // placer 'montant' après 'devise' (ou autre colonne existante)
            $table->string('montant')->nullable()->after('devise');

            $table->string('agence_destination')->nullable()->after('agence_expedition');

            // Clé étrangère pour l'agent (doit être défini après agent_id)
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('programmes', function (Blueprint $table) {
            // supprimer la clé étrangère avant la colonne
            $table->dropForeign(['agent_id']);

            $table->dropColumn([
                'mode_transit',
                'agent_id',
                'agence_expedition',
                'prenom_expediteur',
                'email_expediteur',
                'devise',
                'montant',
                'agence_destination'
            ]);
        });
    }
}
