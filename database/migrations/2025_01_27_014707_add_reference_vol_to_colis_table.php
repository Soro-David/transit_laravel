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
        Schema::table('colis', function (Blueprint $table) {
            // Ajout de la colonne reference_vol
            $table->string('reference_vol')->nullable()->after('id'); // 'id' est la colonne après laquelle vous voulez ajouter 'reference_vol'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colis', function (Blueprint $table) {
            // Suppression de la colonne reference_vol
            $table->dropColumn('reference_vol');
        });
    }
};