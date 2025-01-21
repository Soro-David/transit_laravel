<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToChauffeursTable extends Migration
{
    public function up()
    {
        Schema::table('chauffeurs', function (Blueprint $table) {
            $table->string('agence')->nullable(); // Ajoute la colonne
        });
    }

    public function down()
    {
        Schema::table('chauffeurs', function (Blueprint $table) {
            $table->dropColumn('agence'); // Supprime la colonne
        });
    }
}
