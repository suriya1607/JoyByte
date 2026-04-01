<?php

namespace App\Repositories\Cart;

use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    public function getCartItems(int $userId)
    {
        return CartItem::query()
            ->where('user_id', $userId)
            ->with('product')
            ->get();
    }

    public function addOrUpdateItem(int $userId, int $productId, int $quantity): CartItem
    {
        return CartItem::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['quantity' => $quantity]
        );
    }

    public function removeItem(int $userId, int $productId): bool
    {
        return CartItem::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    public function clearCart(int $userId): bool
    {
        return CartItem::query()
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    public function getCartWithProducts(int $userId)
    {
        return CartItem::query()
            ->where('user_id', $userId)
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'price', 'image_url', 'stock_quantity');
            }])
            ->get();
    }
}
