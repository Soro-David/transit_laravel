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
             $table->decimal('montant_service', 10, 2)->nullable()->after('service');
            $table->string('produit')->nullable()->after('montant_service'); 

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
             $table->dropColumn('produit');
             $table->dropColumn('montant_service');
        });
    }
};
