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
        Schema::table('paiements', function (Blueprint $table) {
            // Renommer les colonnes existantes
            $table->renameColumn('mode_de_paiement', 'methode_paiement');
            $table->renameColumn('montant_reçu', 'montant');
            $table->renameColumn('operateur_mobile', 'operateur');
            $table->renameColumn('nom_banque', 'banque');
            
            // Fusionner numero_compte et numero_tel en compte_utilisateur
            $table->string('compte_utilisateur')->nullable()->after('banque');
            
            // Supprimer les colonnes inutiles
            $table->dropColumn('numero_cheque');
            $table->dropColumn('numero_tel');
            $table->dropColumn('numero_compte');
    
            // Ajouter les colonnes manquantes
            $table->enum('statut_paiement', ['En attente', 'Payé', 'Annulé'])->default('En attente');
            $table->dateTime('date_validation')->nullable();
            $table->foreignId('utilisateur_id')->constrained('users');
            $table->foreignId('agent_id')->nullable()->constrained('agents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
