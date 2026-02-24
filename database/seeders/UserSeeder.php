<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario admin
        \App\Models\User::create([
            'name' => 'Administrador',
            'email' => 'admin@salvajebar.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Crear usuario estándar
        \App\Models\User::create([
            'name' => 'Usuario',
            'email' => 'usuario@salvajebar.com',
            'password' => bcrypt('password'),
            'role' => 'usuario'
        ]);
    }
}
