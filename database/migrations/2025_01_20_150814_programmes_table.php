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
            $table->unsignedBigInteger('chauffeur_id'); // Référence au chauffeur
            $table->unsignedBigInteger('colis_id'); // Référence au colis
            $table->timestamp('date_programme')->nullable(); // Date de la programmation
            $table->enum('status', ['En attente', 'En cours', 'Recuperé', 'Livré'])->default('En attente'); // Statut du programme
            $table->timestamps();

            // Relations
            $table->foreign('chauffeur_id')->references('id')->on('chauffeurs')->onDelete('cascade');
            $table->foreign('colis_id')->references('id')->on('colis')->onDelete('cascade');
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
