<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgentIdToColisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id')->nullable()->after('service'); // Ajoute agent_id après la colonne 'service', nullable
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('SET NULL'); // Clé étrangère vers la table 'agents'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->dropForeign(['colis_agent_id_foreign']); // Supprimer la clé étrangère (Laravel nomme automatiquement la contrainte 'table_colonne_foreign')
            $table->dropColumn('agent_id');     // Supprimer la colonne
        });
    }
}