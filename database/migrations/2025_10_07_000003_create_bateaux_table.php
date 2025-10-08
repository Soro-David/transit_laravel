<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBateauxTable extends Migration
{
    public function up()
    {
        Schema::create('bateaux', function (Blueprint $table) {
            $table->id();
            $table->string('reference_bateau')->nullable();
            $table->string('reference_conteneur')->nullable();
            $table->string('type')->nullable();
            $table->dateTime('date_arriver')->nullable();
            $table->string('compagnie')->nullable();
            $table->string('nom_bateau')->nullable();
            $table->string('numero_bateau')->nullable();
            $table->string('agence_destination')->nullable();
            $table->string('agence_expedition')->nullable();
            $table->string('nom_ballon')->nullable();
            $table->string('numero_ballon')->nullable();
            $table->boolean('recuperer')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bateaux');
    }
}
