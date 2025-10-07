<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChauffeursTable extends Migration
{
    public function up()
    {
        Schema::create('chauffeurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('email')->nullable()->unique(false);
            $table->string('tel')->nullable();
            $table->foreignId('agence_id')->nullable()->constrained('agences')->onDelete('cascade');
            $table->string('agence')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chauffeurs');
    }
}
