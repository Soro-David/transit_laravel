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
            $table->string('reference_colis');
            $table->string('reference_contenaire');
            $table->integer('quantite');
            $table->string('type_embalage');
            $table->float('valeur');
            $table->float('poids');
            $table->string('dimension');
            $table->foreignId('expediteur_id')->constrained()->onDelete('cascade'); // Clé étrangère vers expediteur
            $table->foreignId('destinataire_id')->constrained()->onDelete('cascade'); // Clé étrangère vers destinataire
            $table->foreignId('paiement_id')->constrained()->onDelete('cascade'); // Clé étrangère vers paiement
            $table->foreignId('chauffeur_id')->nullable()->constrained()->onDelete('set null'); // Clé étrangère vers chauffeur (facultatif)
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
            $table->dropForeign(['expediteur_id']);
            $table->dropForeign(['destinataire_id']);
            $table->dropForeign(['paiement_id']);
            $table->dropForeign(['chauffeur_id']);
        });
    }
};
