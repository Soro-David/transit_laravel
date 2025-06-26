<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColisIdToVersementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('versements', function (Blueprint $table) {
            // Ajoute la colonne colis_id après la colonne paiement_id
            // Elle est nullable et liée par une contrainte de clé étrangère.
            // Si le colis est supprimé, la valeur devient NULL.
            $table->foreignId('colis_id')->nullable()->after('paiement_id')->constrained('colis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('versements', function (Blueprint $table) {
            // Supprime d'abord la contrainte, puis la colonne
            $table->dropForeign(['colis_id']);
            $table->dropColumn('colis_id');
        });
    }
}