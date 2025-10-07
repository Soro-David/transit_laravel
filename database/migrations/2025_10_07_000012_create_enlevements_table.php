<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnlevementsTable extends Migration
{
    public function up()
    {
        Schema::create('enlevements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type_enlevement')->nullable();
            $table->string('nom_client')->nullable();
            $table->string('prenom_client')->nullable();
            $table->string('contact_client')->nullable();
            $table->string('email_client')->nullable();
            $table->string('nature')->nullable();
            $table->integer('quantite')->nullable();
            $table->integer('nbre_etiquette')->nullable();
            $table->string('path_qr_code')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enlevements');
    }
}
