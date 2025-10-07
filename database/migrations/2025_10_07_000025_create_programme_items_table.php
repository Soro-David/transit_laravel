<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programme_items', function (Blueprint $table) {
            $table->id();

            // référence vers programmes
            $table->foreignId('programme_id')->nullable()->constrained('programmes')->onDelete('cascade');

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programme_items');
    }
};
