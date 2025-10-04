<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_quantite_to_programmes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantiteToProgrammesTable extends Migration
{
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->integer('quantite')->default(1)->after('id');
        });
    }

    public function down()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn('quantite');
        });
    }
}