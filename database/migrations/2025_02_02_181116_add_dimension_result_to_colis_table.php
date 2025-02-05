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
            $table->string('dimension_result')->nullable()->after('id'); // 'id' est la colonne après laquelle vous voulez ajouter 'reference_vol'
            $table->string('type_colis')->nullable(); // 'id' est la colonne après laquelle vous voulez ajouter 'reference_vol'
           
            //
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
            //
            $table->dropColumn('dimension_result');
            $table->dropColumn('type_colis');

        });
    }
};
