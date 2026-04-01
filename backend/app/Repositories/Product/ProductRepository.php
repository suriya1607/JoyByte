<?php

namespace App\Repositories\Product;

use App\Models\Product;

class ProductRepository
{
    public function getByShop(int $shopId)
    {
        return Product::query()
            ->where('shop_id', $shopId)
            ->where('is_active', true)
            ->get();
    }

    public function getByCategory(int $categoryId)
    {
        return Product::query()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();
    }

    public function getByShopAndCategory(int $shopId, int $categoryId)
    {
        return Product::query()
            ->where('shop_id', $shopId)
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();
    }

    public function getById(int $id): ?Product
    {
        return Product::query()->find($id);
    }
}
