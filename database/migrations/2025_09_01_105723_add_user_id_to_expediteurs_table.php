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
            if (!Schema::hasColumn('expediteurs', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained('users')
                      ->onDelete('cascade')
                      ->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('expediteurs', function (Blueprint $table) {
            if (Schema::hasColumn('expediteurs', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
