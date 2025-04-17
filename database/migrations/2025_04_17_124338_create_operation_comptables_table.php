<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationComptablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_comptables', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (bigIncrements par défaut)
            $table->date('date_operation');
            $table->string('type_operation');
            $table->string('beneficiaire_fournisseur')->nullable(); // Peut être nul
            $table->text('objet')->nullable(); // Peut être nul, type text pour plus de longueur
            $table->decimal('montant', 10, 2); // Montant avec 10 chiffres au total, dont 2 après la virgule
            $table->string('conteneur_frais_fonction')->nullable(); // Peut être nul
            $table->unsignedBigInteger('agent_id'); // Clé étrangère vers la table agents

            // Définition de la clé étrangère
            $table->foreign('agent_id')
                  ->references('id')
                  ->on('agents') // Nom de la table agents
                  ->onDelete('cascade'); // Action en cas de suppression de l'agent (cascade pour supprimer les opérations liées)

            $table->timestamps(); // created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_comptables');
    }
}