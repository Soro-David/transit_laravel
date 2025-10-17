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
            $table->integer('quantite')->nullable();
            $table->dateTime('date_programme')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reference_colis')->nullable();
            $table->string('reference_generee')->nullable();
            $table->string('type_reference')->nullable();
            $table->string('nature_du_colis')->nullable();
            $table->string('mode_transit')->nullable();
            $table->string('agence_expedition')->nullable();
            $table->string('agence_destination')->nullable();
            $table->text('actions_a_faire')->nullable();
            $table->string('nom_expediteur')->nullable();
            $table->string('prenom_expediteur')->nullable();
            $table->string('email_expediteur')->nullable();
            $table->string('nom_destinataire')->nullable();
            $table->string('lieu_destinataire')->nullable();
            $table->string('tel_expediteur')->nullable();
            $table->string('tel_destinataire')->nullable();
            $table->string('lieu_expedition')->nullable();
            $table->string('lieu_destination')->nullable();
            $table->decimal('montant', 12, 2)->nullable();
            $table->string('devise')->nullable();
            $table->string('etat_rdv')->nullable();
            $table->string('qr_code')->nullable();
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
