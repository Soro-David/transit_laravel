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
        Schema::create('devis_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained()->onDelete('cascade');
            // --- Informations spécifiques à chaque colis ---
            $table->integer('quantite_colis');
            $table->string('service')->comment('Nom du produit ou service');
            $table->decimal('valeur_colis', 10, 2)->nullable(); // 10 chiffres au total, 2 après la virgule
            $table->string('type_colis'); // 'standard' ou 'fragile'
            $table->text('description_colis')->nullable();
            
            // --- Champs conditionnels (selon le mode de transit) ---
            $table->decimal('poids', 8, 2)->nullable()->comment('En kg, pour le mode aérien');
            $table->decimal('longueur', 8, 2)->nullable()->comment('En cm, pour le mode maritime');
            $table->decimal('largeur', 8, 2)->nullable()->comment('En cm, pour le mode maritime');
            $table->decimal('hauteur', 8, 2)->nullable()->comment('En cm, pour le mode maritime');
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
        Schema::dropIfExists('devis_items');
    }
};
