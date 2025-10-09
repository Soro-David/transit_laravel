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
        Schema::table('programmes', function (Blueprint $table) {
            $table->text('qr_code')->nullable()->after('etat_rdv'); // Ajoute la colonne qr_code de type TEXT, qui peut être nulle, après la colonne etat_rdv
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn('qr_code'); // Supprime la colonne qr_code
        });
    }
};