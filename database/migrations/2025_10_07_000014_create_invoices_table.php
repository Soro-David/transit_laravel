<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediteur_id')->nullable()->constrained('expediteurs')->onDelete('set null');
            $table->foreignId('destinataire_id')->nullable()->constrained('destinataires')->onDelete('set null');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->decimal('montant', 12, 2)->nullable();
            $table->string('numero_facture')->nullable();
            $table->string('nom_agent')->nullable();
            $table->string('nom_expediteur')->nullable();
            $table->string('nom_destinataire')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
