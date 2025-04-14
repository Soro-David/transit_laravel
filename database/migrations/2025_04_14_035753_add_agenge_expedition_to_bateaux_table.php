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
        Schema::table('bateaux', function (Blueprint $table) {
            $table->string('agence_expedition')->nullable()->after('agence_destination');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bateaux', function (Blueprint $table) {
            $table->dropColumn('agence_expedition');
        });
    }
};
