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
            $table->string('lieu_expedition')->nullable()->after('tel_destinataire'); // Ajustez la position si nécessaire
            $table->string('lieu_destination')->nullable()->after('lieu_expedition');   // Ajustez la position si nécessaire
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn('lieu_expedition');
            $table->dropColumn('lieu_destination');
        });
    }
};