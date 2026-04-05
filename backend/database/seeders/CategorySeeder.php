<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fast Food', 'description' => 'Quick and tasty meals'],
            ['name' => 'Beverages', 'description' => 'Refreshing drinks'],
            ['name' => 'Desserts', 'description' => 'Sweet treats'],
            ['name' => 'Vegetarian', 'description' => 'Plant-based options'],
            ['name' => 'Snacks', 'description' => 'Bite-sized delights'],
            ['name' => 'Bakery', 'description' => 'Fresh baked goods'],
        ];

        foreach ($categories as $index => $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'display_order' => $index,
                ]
            );
        }
    }
}
