<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            // Ajout du champ "montant" s'il n'existe pas encore
            if (!Schema::hasColumn('programmes', 'montant')) {
                $table->string('montant')->nullable()->after('devise'); 
                // ⚠️ Si ta table n’a pas de colonne "devise", 
                // remplace "after('devise')" par une colonne qui existe, 
                // par exemple "after('nature_du_colis')"
            }
        });
    }

    public function down()
    {
        Schema::table('programmes', function (Blueprint $table) {
            if (Schema::hasColumn('programmes', 'montant')) {
                $table->dropColumn('montant');
            }
        });
    }
};
