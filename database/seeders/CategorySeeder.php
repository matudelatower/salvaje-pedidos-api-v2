<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Bebidas', 'image' => 'categories/bebidas.jpg', 'active' => true],
            ['name' => 'Comidas', 'image' => 'categories/comidas.jpg', 'active' => true],
            ['name' => 'Postres', 'image' => 'categories/postres.jpg', 'active' => true],
            ['name' => 'Snacks', 'image' => 'categories/snacks.jpg', 'active' => true],
            ['name' => 'Cafetería', 'image' => 'categories/cafeteria.jpg', 'active' => true],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
