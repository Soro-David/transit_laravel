<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versements', function (Blueprint $table) {
            $table->id();
            
            // La clé étrangère qui lie ce versement à la "facture" principale dans la table 'paiements'.
            $table->foreignId('paiement_id')->constrained('paiements')->onDelete('cascade');

            // Le montant de CE versement spécifique, stocké en EUR.
            $table->decimal('montant_versement', 10, 2); 

            // L'agent qui a enregistré ce versement.
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            
            // Vous pouvez ajouter d'autres champs si nécessaire (ex: methode_paiement pour ce versement)
            // $table->string('methode_paiement_specifique')->default('cash');

            $table->timestamps(); // created_at et updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versements');
    }
};