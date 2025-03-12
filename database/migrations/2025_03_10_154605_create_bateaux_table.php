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
        Schema::create('bateaux', function (Blueprint $table) {
            $table->id();
            $table->string('reference_conteneur')->unique();
            $table->string('reference_bateau')->unique();
            $table->string('type');
            $table->date('date_arriver');
            $table->string('compagnie');
            $table->string('nom_bateau')->nullable();
            $table->string('numero_bateau')->nullable()->unique();
            $table->string('agence_destination');
            $table->string('nom_ballon')->nullable();
            $table->string('numero_ballon')->nullable()->unique();
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
        Schema::dropIfExists('bateaux');
    }
};
