<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('methode_paiement')->nullable();
            $table->decimal('montant', 14, 2)->nullable();
            $table->string('operateur')->nullable();
            $table->string('banque')->nullable();
            $table->string('NumeroPaiement')->nullable();
            $table->string('id_transaction')->nullable();
            $table->string('statut_paiement')->nullable();
            $table->dateTime('date_validation')->nullable();
            $table->foreignId('expediteur_id')->nullable()->constrained('expediteurs')->onDelete('set null');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->decimal('montant_paye', 14, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}
