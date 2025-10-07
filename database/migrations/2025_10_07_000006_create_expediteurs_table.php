<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpediteursTable extends Migration
{
    public function up()
    {
        Schema::create('expediteurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->string('lieu_expedition')->nullable();
            $table->string('agence')->nullable();
            $table->string('adresse')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expediteurs');
    }
}
