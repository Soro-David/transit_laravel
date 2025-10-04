<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEtatToDevisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Les états acceptés :
     * Devis, Fermé, En attente, Validé, En entrepot, Chargé, En transit, Déchargé, Livré, Annulé
     */
    public function up()
    {
        Schema::table('devis', function (Blueprint $table) {
            // Utilisation d'enum (MySQL) — si tu préfères string, remplace par ->string('etat')->default('Devis')
            $table->enum('etat', [
                'Devis',
                'Fermé',
                'En attente',
                'Validé',
                'En entrepot',
                'Chargé',
                'En transit',
                'Déchargé',
                'Livré',
                'Annulé'
            ])->default('Devis')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn('etat');
        });
    }
}
