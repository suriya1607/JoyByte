<?php

namespace App\Repositories\Shop;

use App\Models\Product;
use App\Models\Shop;

class ShopRepository
{
    public function getAllShops()
    {
        return Shop::query()
            ->where('is_active', true)
            ->get();
    }

    public function getById(int $id): ?Shop
    {
        return Shop::query()->find($id);
    }

    public function getShopWithProducts(int $shopId, ?int $categoryId = null)
    {
        $query = Product::query()->where('shop_id', $shopId);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->get();
    }

    public function getByLocation(string $location)
    {
        return Shop::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->get();
    }
}
