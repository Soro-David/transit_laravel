<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_reference_to_programmes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceToProgrammesTable extends Migration
{
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->string('reference_generee')->nullable()->after('reference_colis');
            $table->string('type_reference')->default('externe')->after('reference_generee'); // 'externe', 'generee'
        });
    }

    public function down()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn(['reference_generee', 'type_reference']);
        });
    }
}