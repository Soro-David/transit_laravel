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
        Schema::table('paiements', function (Blueprint $table) {
            // Ajout de la colonne colis_id
            $table->unsignedBigInteger('colis_id')->nullable()->after('id'); // 'id' est la colonne après laquelle vous voulez ajouter 'colis_id'

            // Si vous souhaitez ajouter une contrainte de clé étrangère, décommentez la ligne suivante
            // $table->foreign('colis_id')->references('id')->on('colis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Suppression de la colonne colis_id
            $table->dropColumn('colis_id');
        });
    }
};