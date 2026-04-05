<?php

namespace App\Services\Cart;

use App\Repositories\Cart\CartRepository;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(private readonly CartRepository $cartRepository)
    {
    }

    public function getCart(int $userId)
    {
        return $this->cartRepository->getCartWithProducts($userId);
    }

    public function addToCart(int $userId, int $productId, int $quantity)
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => ['Quantity must be greater than 0.'],
            ]);
        }

        return $this->cartRepository->addOrUpdateItem($userId, $productId, $quantity);
    }

    public function updateCartItem(int $userId, int $productId, int $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }

        return $this->cartRepository->addOrUpdateItem($userId, $productId, $quantity);
    }

    public function removeFromCart(int $userId, int $productId)
    {
        return $this->cartRepository->removeItem($userId, $productId);
    }

    public function clearCart(int $userId)
    {
        return $this->cartRepository->clearCart($userId);
    }

    public function getCartTotal(int $userId)
    {
        $items = $this->getCart($userId);
        $total = 0;

        foreach ($items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        return $total;
    }
}
