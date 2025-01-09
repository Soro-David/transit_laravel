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
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->date('date_programme');
            $table->foreignId('chauffeur_id')->constrained('chauffeurs')->onDelete('cascade');

            // Ajout des colonnes pour les informations de la tournée
            $table->string('reference_colis')->nullable();
            $table->string('nom_expediteur')->nullable();
            $table->string('nom_destinataire')->nullable();
            $table->string('lieu_destinataire')->nullable();
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
        Schema::dropIfExists('programmes');
    }
};