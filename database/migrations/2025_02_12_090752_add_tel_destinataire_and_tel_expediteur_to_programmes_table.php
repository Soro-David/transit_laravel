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
            $table->string('tel_expediteur')->nullable()->after('lieu_destinataire'); // Ajustez la position si nécessaire
            $table->string('tel_destinataire')->nullable()->after('tel_expediteur');   // Ajustez la position si nécessaire
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn('tel_expediteur');
            $table->dropColumn('tel_destinataire');
        });
    }
};