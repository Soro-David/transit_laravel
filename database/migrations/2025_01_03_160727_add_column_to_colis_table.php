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
        Schema::table('colis', function (Blueprint $table) {
            //
            $table->string('qr_code_path')->nullable();   // Chemin du QR code
            $table->enum('etat', ['En attente','Validé','En entrepot','Chargé','','En transit','Dechargé','Livré','Annuler' ]);
            $table->string('status')->default('non payé'); // Statut par défaut : non payé



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colis', function (Blueprint $table) {
            //
        Schema::dropIfExists('status');
        Schema::dropIfExists('etat');
        Schema::dropIfExists('qr_code_path');

        });
    }
};
