<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // Identifiant unique de la notification
            $table->string('type'); // Type de la notification (par exemple, 'message', 'alert', etc.)
            $table->morphs('notifiable'); // Polymorphisme, pour lier à d'autres modèles (utilisateurs, objets, etc.)
            $table->string('data'); // Les données de la notification, généralement sous forme JSON
            $table->timestamp('read_at')->nullable(); // Date de lecture de la notification (null si non lue)
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
