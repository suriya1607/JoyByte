<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Models\OrderItem;

class OrderRepository
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function createOrderItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            OrderItem::create([
                'order_id'          => $order->id,
                'product_id'        => $item['product_id'],
                'product_name'      => $item['product_name'],
                'product_price'     => $item['product_price'],
                'product_image_url' => $item['product_image_url'] ?? null,
                'quantity'          => $item['quantity'],
                'subtotal'          => $item['subtotal'],
            ]);
        }
    }

    public function getUserOrders(int $userId)
    {
        return Order::with(['items', 'shop'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function getOrderById(int $orderId, int $userId): ?Order
    {
        return Order::with(['items', 'shop'])
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }
}
