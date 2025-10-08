<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevisItemsTable extends Migration
{
    public function up()
    {
        Schema::create('devis_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->nullable()->constrained('devis')->onDelete('cascade');
            $table->foreignId('programme_id')->nullable()->constrained('programmes')->onDelete('set null');
            $table->integer('quantite_colis')->nullable();
            $table->string('service')->nullable();
            $table->decimal('valeur_colis', 12, 2)->nullable();
            $table->string('type_colis')->nullable();
            $table->text('description_colis')->nullable();
            $table->decimal('poids', 10, 2)->nullable();
            $table->decimal('longueur', 8, 2)->nullable();
            $table->decimal('largeur', 8, 2)->nullable();
            $table->decimal('hauteur', 8, 2)->nullable();
            $table->decimal('montant', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devis_items');
    }
}
