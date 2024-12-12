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
       
    Schema::create('les_colis', function (Blueprint $table) {
        $table->id(); // Clé primaire

        // Informations sur l'expéditeur
        $table->string('nom_expediteur');
        $table->string('prenom_expediteur');
        $table->string('email_expediteur')->nullable(); // Un expéditeur peut ne pas avoir d'email
        $table->string('tel_expediteur');
        $table->string('agence_expedition');
        $table->string('lieu_expedition');

        // Informations sur le destinataire
        $table->string('nom_destinataire');
        $table->string('prenom_destinataire');
        $table->string('email_destinataire')->nullable(); // Un destinataire peut ne pas avoir d'email
        $table->string('tel_destinataire');
        $table->string('agence_destination');
        $table->string('lieu_destination');

        // Détails du colis
        $table->integer('quantite');
        $table->string('type_emballage');
        $table->string('dimension');
        $table->text('description_colis');
        $table->float('poids_colis');
        $table->decimal('valeur_colis', 10, 2);

        // Informations de transit
        $table->string('mode_transit')->nullable();
        $table->string('reference_colis')->unique(); // Chaque colis doit avoir une référence unique

        // Informations de paiement
        $table->string('mode_payement')->nullable();
        $table->string('numero_compte')->nullable();
        $table->string('nom_banque')->nullable();
        $table->string('transaction_id')->nullable();
        $table->string('tel')->nullable();
        $table->string('operateur')->nullable();
        $table->string('numero_cheque')->nullable();
        $table->decimal('montant_reçu', 10, 2)->nullable();

        // Statut et QR code
        $table->string('status')->default('non payé'); // Statut par défaut : non payé
        $table->string('qr_code_path')->nullable();   // Chemin du QR code
        $table->enum('etat', ['En transit', 'Livré', 'En attente', 'En entrepot']);

        // Relation avec le client
        $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

        // Timestamps
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
    Schema::dropIfExists('les_colis');
}
};
