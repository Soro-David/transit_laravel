<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Principal',
                'role' => 'admin',
                'tel' => '0101010101',
                'adresse' => 'Abidjan',
                'is_active' => true,
                'password' => Hash::make('12345678'), // 🔑 Mot de passe par défaut
                'remember_token' => Str::random(60),
            ]
        );
    }
}
