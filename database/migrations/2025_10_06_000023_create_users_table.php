<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agence_id')->nullable();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('role')->default('user');
            $table->string('tel')->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->unique();

            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');
            $table->rememberToken();
            $table->string('last_session_id')->nullable();

            $table->timestamps();

     
             $table->foreign('agence_id')->references('id')->on('agences')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
