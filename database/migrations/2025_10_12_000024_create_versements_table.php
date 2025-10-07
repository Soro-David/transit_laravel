<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersementsTable extends Migration
{
    public function up()
    {
        Schema::create('versements', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant_versement', 14, 2)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->foreignId('colis_id')->nullable()->constrained('colis')->onDelete('set null');
            $table->foreignId('paiement_id')->nullable()->constrained('paiements')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('versements');
    }
}
