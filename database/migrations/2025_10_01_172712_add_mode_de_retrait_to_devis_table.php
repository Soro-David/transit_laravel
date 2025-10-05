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
        Schema::table('devis', function (Blueprint $table) {
            // Ajoute le champ pour le mode de retrait après la colonne 'etat'
            // Il est nullable car il ne sera rempli qu'après la confirmation
            $table->string('mode_de_retrait')->nullable()->after('etat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn('mode_de_retrait');
        });
    }
};