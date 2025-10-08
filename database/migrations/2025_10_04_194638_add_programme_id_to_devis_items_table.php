<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis_items', function (Blueprint $table) {
            $table->foreignId('programme_id')->nullable()->constrained('programmes')->onDelete('cascade')->after('devis_id');
        });
    }

    public function down(): void
    {
        Schema::table('devis_items', function (Blueprint $table) {
            $table->dropForeign(['programme_id']);
            $table->dropColumn('programme_id');
        });
    }
};