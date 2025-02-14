<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediteur_id');
            $table->unsignedBigInteger('destinataire_id');
            $table->unsignedBigInteger('agent_id');
            $table->decimal('montant', 8, 2);
            $table->string('numero_facture')->nullable();
            $table->timestamps();

            // Clés étrangères
            $table->foreign('expediteur_id')->references('id')->on('expediteurs')->onDelete('cascade');
            $table->foreign('destinataire_id')->references('id')->on('destinataires')->onDelete('cascade');
           
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
