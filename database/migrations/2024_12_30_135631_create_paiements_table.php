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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('mode_de_paiement');
            $table->decimal('montant_reçu', 10, 2)->nullable();
            $table->string('operateur_mobile');
            $table->string('numero_compte');
            $table->string('nom_banque');
            $table->string('id_transaction');
            $table->string('numero_tel');
            $table->string('numero_cheque');
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
        Schema::dropIfExists('paiements');
    }
};
