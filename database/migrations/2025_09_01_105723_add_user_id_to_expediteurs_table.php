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
        Schema::table('expediteurs', function (Blueprint $table) {
            // Vérifie si la colonne existe déjà
            if (!Schema::hasColumn('expediteurs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id');
            }

            // Dans tous les cas, on s'assure que la clé étrangère est bien posée
            $table->foreign('user_id')
                ->nullable()
                ->constrained('users') 
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('expediteurs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id'); 
        });
    }

};
