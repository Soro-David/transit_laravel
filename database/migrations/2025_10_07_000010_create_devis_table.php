<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevisTable extends Migration
{
    public function up()
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('mode_transit')->nullable();
            $table->string('pays_expedition')->nullable();
            $table->string('agence_expedition')->nullable();
            $table->string('agence_destination')->nullable();
            $table->string('nom_expediteur')->nullable();
            $table->string('prenom_expediteur')->nullable();
            $table->string('email_expediteur')->nullable();
            $table->string('tel_expediteur')->nullable();
            $table->string('adresse_expediteur')->nullable();
            $table->decimal('montant', 12, 2)->default(0);
            $table->string('devise')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('chauffeur_id')->nullable()->constrained('chauffeurs')->onDelete('set null');
            $table->string('etat')->nullable();
            $table->string('mode_de_retrait')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devis');
    }
}
