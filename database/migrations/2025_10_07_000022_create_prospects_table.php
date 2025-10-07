<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectsTable extends Migration
{
    public function up()
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->string('adresse')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prospects');
    }
}
