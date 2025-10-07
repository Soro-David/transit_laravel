<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColisTable extends Migration
{
    public function up()
    {
        Schema::create('colis', function (Blueprint $table) {
            $table->id();
            $table->string('reference_colis')->nullable();
            $table->string('reference_contenaire')->nullable();
            $table->integer('quantite_colis')->nullable();
            $table->text('description_colis')->nullable();
            $table->decimal('valeur_colis', 12, 2)->nullable();
            $table->decimal('poids_colis', 10, 2)->nullable();
            $table->string('dimension')->nullable();
            $table->foreignId('expediteur_id')->nullable()->constrained('expediteurs')->onDelete('set null');
            $table->foreignId('destinataire_id')->nullable()->constrained('destinataires')->onDelete('set null');
            $table->foreignId('paiement_id')->nullable()->constrained('paiements')->onDelete('set null');
            $table->foreignId('chauffeur_id')->nullable()->constrained('chauffeurs')->onDelete('set null');
            $table->string('mode_transit')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->decimal('prix_transit_colis', 12, 2)->nullable();
            $table->string('etat')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->string('dimension_result')->nullable();
            $table->decimal('hauteur', 8, 2)->nullable();
            $table->decimal('largeur', 8, 2)->nullable();
            $table->decimal('longueur', 8, 2)->nullable();
            $table->string('type_colis')->nullable();
            $table->boolean('recup')->default(false);
            $table->string('service')->nullable();
            $table->string('devise')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->string('categorie_client')->nullable();
            $table->string('id_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('colis');
    }
}
