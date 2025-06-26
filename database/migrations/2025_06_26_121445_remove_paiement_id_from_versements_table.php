<?php

// dans database/migrations/xxxx_xx_xx_xxxxxx_remove_paiement_id_from_versements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePaiementIdFromVersementsTable extends Migration
{
    public function up()
    {
        Schema::table('versements', function (Blueprint $table) {
            // Important : La clé étrangère DOIT être supprimée avant la colonne
            $table->dropForeign(['paiement_id']);
            $table->dropColumn('paiement_id');
        });
    }

    public function down()
    {
        Schema::table('versements', function (Blueprint $table) {
            // Pour pouvoir annuler la migration, on recrée la colonne
            $table->foreignId('paiement_id')->nullable()->after('id')->constrained('paiements')->onDelete('cascade');
        });
    }
}