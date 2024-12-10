<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'admin', // Nazwa użytkownika
            'email' => 'admin@admin', // E-mail użytkownika
            'password' => Hash::make('zaq1@WSX'), // Hasło użytkownika
        ]);
        $admin->assignRole('admin');
    }
}
