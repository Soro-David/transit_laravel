<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencesTable extends Migration
{
    public function up()
    {
        Schema::create('agences', function (Blueprint $table) {
            $table->id();
            $table->string('nom_agence')->nullable();
            $table->string('adresse_agence')->nullable();
            $table->string('pays_agence')->nullable();
            $table->string('devise_agence')->nullable();
            $table->decimal('prix_au_kg', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agences');
    }
}
