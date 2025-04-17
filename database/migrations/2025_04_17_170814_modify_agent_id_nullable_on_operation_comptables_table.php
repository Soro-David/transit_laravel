<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAgentIdNullableOnOperationComptablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_comptables', function (Blueprint $table) {
            // Rend la colonne agent_id nullable
            $table->unsignedBigInteger('agent_id')->nullable()->change();
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
            // Pour annuler la migration (remettre agent_id non-nullable - au cas où vous voudriez revenir en arrière)
            $table->unsignedBigInteger('agent_id')->nullable(false)->change();
        });
    }
}