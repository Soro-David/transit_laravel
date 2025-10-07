<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationComptablesTable extends Migration
{
    public function up()
    {
        Schema::create('operation_comptables', function (Blueprint $table) {
            $table->id();
            $table->date('date_operation')->nullable();
            $table->string('type_operation')->nullable();
            $table->string('beneficiaire_fournisseur')->nullable();
            $table->text('objet')->nullable();
            $table->decimal('montant', 14, 2)->nullable();
            $table->decimal('conteneur_frais_fonction', 12, 2)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->string('reference_colis')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('operation_comptables');
    }
}
