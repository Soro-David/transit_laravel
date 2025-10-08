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
        Schema::table('operation_comptables', function (Blueprint $table) {
            // Ajoute une colonne pour stocker la référence du colis, si l'opération est un paiement.
            // Elle est nullable car une opération peut ne pas concerner un colis (ex: frais de fonctionnement).
            $table->string('reference_colis')->nullable()->after('agent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_comptables', function (Blueprint $table) {
            //
        });
    }
};
