<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddForeignKeyToUserIdInProgrammes extends Migration
{
    public function up()
    {
        // Désactiver temporairement les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Étape 1: Vérifier que user_id existe et l'ajouter si nécessaire
        if (!Schema::hasColumn('programmes', 'user_id')) {
            Schema::table('programmes', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
            \Log::info("Colonne user_id ajoutée à la table programmes");
        }

        // Étape 2: Vérifier et ajouter la contrainte foreign key
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'programmes' 
            AND COLUMN_NAME = 'user_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (empty($constraints)) {
            Schema::table('programmes', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            });
            \Log::info("Contrainte foreign key ajoutée à user_id");
        } else {
            \Log::info("La contrainte foreign key existe déjà pour user_id");
        }

        // Étape 3: Nettoyer les données problématiques
        $this->cleanInvalidData();

        // Étape 4: Rendre user_id non nullable si toutes les données sont valides
        $invalidCount = DB::table('programmes')
            ->leftJoin('users', 'programmes.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->whereNotNull('programmes.user_id')
            ->count();

        if ($invalidCount === 0) {
            Schema::table('programmes', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
            \Log::info("Colonne user_id rendue non nullable");
        }

        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::table('programmes', function (Blueprint $table) {
            $table->dropForeignIfExists(['user_id']);
            // Ne pas supprimer la colonne user_id pour conserver les données
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function cleanInvalidData()
    {
        // Identifier les user_id qui ne correspondent à aucun user
        $invalidRecords = DB::table('programmes')
            ->leftJoin('users', 'programmes.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->whereNotNull('programmes.user_id')
            ->select('programmes.id', 'programmes.user_id')
            ->get();

        if ($invalidRecords->isNotEmpty()) {
            \Log::warning("Nettoyage des user_id invalides:", [
                'count' => $invalidRecords->count()
            ]);

            // Option 1: Mettre user_id à NULL pour les enregistrements invalides
            DB::table('programmes')
                ->whereIn('id', $invalidRecords->pluck('id'))
                ->update(['user_id' => null]);

            \Log::info("User_id mis à NULL pour " . $invalidRecords->count() . " enregistrements");
        }
    }
}