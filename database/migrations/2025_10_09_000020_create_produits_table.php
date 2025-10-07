<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduitsTable extends Migration
{
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->decimal('prix', 12, 2)->nullable();
            $table->string('categorie')->nullable();
            $table->text('description')->nullable();
            $table->string('agence')->nullable();
            $table->foreignId('colis_id')->nullable()->constrained('colis')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produits');
    }
}
