<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $shops = [
            [
                'user_id' => 1,
                'name' => 'Pizza Palace',
                'location' => 'Downtown',
                'phone' => '+1-800-7499',
                'rating' => 4.5,
                'total_orders' => 3200,
                'image_url' => 'https://via.placeholder.com/300x200?text=Pizza+Palace',
            ],
            [
                'user_id' => 1,
                'name' => 'Burger Station',
                'location' => 'Midtown',
                'phone' => '+1-800-2873',
                'rating' => 4.3,
                'total_orders' => 2800,
                'image_url' => 'https://via.placeholder.com/300x200?text=Burger+Station',
            ],
            [
                'user_id' => 1,
                'name' => 'Sushi World',
                'location' => 'Harbor',
                'phone' => '+1-800-7874',
                'rating' => 4.7,
                'total_orders' => 4100,
                'image_url' => 'https://via.placeholder.com/300x200?text=Sushi+World',
            ],
        ];

        foreach ($shops as $shop) {
            Shop::updateOrCreate(
                ['slug' => Str::slug($shop['name'])],
                $shop
            );
        }
    }
}
