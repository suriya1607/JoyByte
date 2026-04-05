<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $pizzaPalace = Shop::where('slug', 'pizza-palace')->first();
        $burgerStation = Shop::where('slug', 'burger-station')->first();
        $sushiWorld = Shop::where('slug', 'sushi-world')->first();

        $fastFood = Category::where('slug', 'fast-food')->first();
        $beverages = Category::where('slug', 'beverages')->first();
        $desserts = Category::where('slug', 'desserts')->first();
        $vegetarian = Category::where('slug', 'vegetarian')->first();
        $snacks = Category::where('slug', 'snacks')->first();

        // Pizza Palace products
        if ($pizzaPalace && $fastFood && $beverages) {
            $pizzaProducts = [
                ['name' => 'Margherita Pizza', 'slug' => 'margherita-pizza', 'price' => 12.99, 'category_id' => $fastFood->id],
                ['name' => 'Pepperoni Pizza', 'slug' => 'pepperoni-pizza', 'price' => 14.99, 'category_id' => $fastFood->id],
                ['name' => 'Vegetarian Pizza', 'slug' => 'vegetarian-pizza', 'price' => 13.99, 'category_id' => $vegetarian->id],
                ['name' => 'Coca Cola', 'slug' => 'coca-cola', 'price' => 2.99, 'category_id' => $beverages->id],
                ['name' => 'Tiramisu', 'slug' => 'tiramisu', 'price' => 5.99, 'category_id' => $desserts->id],
            ];

            foreach ($pizzaProducts as $product) {
                Product::updateOrCreate(
                    ['shop_id' => $pizzaPalace->id, 'slug' => $product['slug']],
                    array_merge($product, [
                        'description' => 'Delicious ' . $product['name'],
                        'image_url' => 'https://via.placeholder.com/300x300?text=' . urlencode($product['name']),
                        'stock_quantity' => 100,
                    ])
                );
            }
        }

        // Burger Station products
        if ($burgerStation && $fastFood && $beverages) {
            $burgerProducts = [
                ['name' => 'Classic Burger', 'slug' => 'classic-burger', 'price' => 9.99, 'category_id' => $fastFood->id],
                ['name' => 'Cheese Burger', 'slug' => 'cheese-burger', 'price' => 10.99, 'category_id' => $fastFood->id],
                ['name' => 'Double Burger', 'slug' => 'double-burger', 'price' => 12.99, 'category_id' => $fastFood->id],
                ['name' => 'French Fries', 'slug' => 'french-fries', 'price' => 3.99, 'category_id' => $snacks->id],
                ['name' => 'Iced Tea', 'slug' => 'iced-tea', 'price' => 2.49, 'category_id' => $beverages->id],
            ];

            foreach ($burgerProducts as $product) {
                Product::updateOrCreate(
                    ['shop_id' => $burgerStation->id, 'slug' => $product['slug']],
                    array_merge($product, [
                        'description' => 'Delicious ' . $product['name'],
                        'image_url' => 'https://via.placeholder.com/300x300?text=' . urlencode($product['name']),
                        'stock_quantity' => 100,
                    ])
                );
            }
        }

        // Sushi World products
        if ($sushiWorld && $fastFood && $beverages) {
            $sushiProducts = [
                ['name' => 'California Roll', 'slug' => 'california-roll', 'price' => 10.99, 'category_id' => $fastFood->id],
                ['name' => 'Spicy Tuna Roll', 'slug' => 'spicy-tuna-roll', 'price' => 11.99, 'category_id' => $fastFood->id],
                ['name' => 'Vegetable Roll', 'slug' => 'vegetable-roll', 'price' => 8.99, 'category_id' => $vegetarian->id],
                ['name' => 'Salmon Nigiri', 'slug' => 'salmon-nigiri', 'price' => 12.99, 'category_id' => $fastFood->id],
                ['name' => 'Green Tea', 'slug' => 'green-tea', 'price' => 2.99, 'category_id' => $beverages->id],
            ];

            foreach ($sushiProducts as $product) {
                Product::updateOrCreate(
                    ['shop_id' => $sushiWorld->id, 'slug' => $product['slug']],
                    array_merge($product, [
                        'description' => 'Delicious ' . $product['name'],
                        'image_url' => 'https://via.placeholder.com/300x300?text=' . urlencode($product['name']),
                        'stock_quantity' => 100,
                    ])
                );
            }
        }
    }
}
