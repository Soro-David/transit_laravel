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
        Schema::table('programmes', function (Blueprint $table) {
            $table->string('nature_du_colis')->nullable()->after('reference_colis');
        });
    }
    
    public function down()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn('nature_du_colis');
        });
    }
};
