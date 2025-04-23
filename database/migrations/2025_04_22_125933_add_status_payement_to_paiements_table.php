<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->enum('statut_paiement', ['payé', 'non payé', 'partiellement payé', 'annulé'])
                  ->default('non payé')
                  ->after('id_transaction'); // ou place-le à l’endroit que tu veux
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn('statut_paiement');
        });
    }
};
