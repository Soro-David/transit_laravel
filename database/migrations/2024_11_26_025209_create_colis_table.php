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
        Schema::create('colis', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date_entree');
            $table->date('date_sortie')->nullable();
            $table->string('destinataire');
            $table->string('expediteur');
            $table->enum('etat', ['En transit', 'Livré', 'En attente','En entrepot']);
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
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
        Schema::dropIfExists('colis');
    }
};
