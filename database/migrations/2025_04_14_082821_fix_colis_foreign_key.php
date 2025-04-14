<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colis', function (Blueprint $table) {
            // Ajoute la nouvelle contrainte pointant vers 'agents'
            $table->foreign('agent_id')
                  ->references('id')
                  ->on('agents')
                  ->onDelete('set null');
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
            // Supprime la nouvelle contrainte vers 'agents'
            $table->dropForeign(['agent_id']);

            // Remet l'ancienne contrainte vers 'agentts'
            $table->foreign('agent_id')
                  ->references('id')
                  ->on('agentts')
                  ->onDelete('set null');
        });
    }
};
