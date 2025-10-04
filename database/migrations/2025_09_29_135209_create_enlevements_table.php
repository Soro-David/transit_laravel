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
        Schema::create('enlevements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            //Les informations concernant le client
            $table->string('type_enlevement');
            $table->string('nom_client')->nullable();
            $table->string('prenom_client')->nullable();
            $table->string('contact_client')->nullable();
            $table->string('email_client')->nullable();

            //Inforamtions concernant le colis 
            $table->string('nature')->nullable();
            $table->string('quantite')->nullable();
            $table->string('nbre_etiquette')->nullable();
            $table->string('path_qr_code')->nullable();
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
        Schema::dropIfExists('enlevements');
    }
};
