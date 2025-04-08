<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('password');

            // Ajouter la colonne agence_id (clé étrangère vers la table agences)
            $table->unsignedBigInteger('agence_id')->nullable(); // nullable car un agent peut ne pas être associé à une agence immédiatement
            $table->foreign('agence_id')->references('id')->on('agences')->onDelete('SET NULL'); // Clé étrangère vers la table 'agences'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agents');
    }
}