<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->date('date_programme');
            $table->foreignId('chauffeur_id')->constrained('chauffeurs')->onDelete('cascade');
            $table->string('reference_colis');
            $table->text('actions_a_faire')->nullable();
            $table->string('nom_expediteur');
            $table->string('nom_destinataire');
            $table->string('lieu_destinataire');
            $table->string('tel_expediteur');
            $table->string('tel_destinataire');
            $table->string('lieu_expedition');
            $table->string('lieu_destination');
            $table->string('etat_rdv')->nullable(); // Nouveau champ
            $table->timestamps();

            // Clé étrangère pour `colis`
            $table->foreign('reference_colis')->references('reference')->on('colis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};
