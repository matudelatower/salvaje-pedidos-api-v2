<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Unidad', 'abbreviation' => 'un', 'active' => true],
            ['name' => 'Litro', 'abbreviation' => 'l', 'active' => true],
            ['name' => 'Mililitro', 'abbreviation' => 'ml', 'active' => true],
            ['name' => 'Kilogramo', 'abbreviation' => 'kg', 'active' => true],
            ['name' => 'Gramo', 'abbreviation' => 'g', 'active' => true],
            ['name' => 'Porción', 'abbreviation' => 'porc', 'active' => true],
        ];

        foreach ($units as $unit) {
            \App\Models\Unit::create($unit);
        }
    }
}
