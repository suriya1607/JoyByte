<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Order\OrderRepository;
use Illuminate\Validation\ValidationException;

class OrderService
{
    private OrderRepository $orderRepository;
    private CartRepository $cartRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CartRepository $cartRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository  = $cartRepository;
    }

    public function placeOrder(int $userId, int $addressId, string $paymentMethod = null): Order
    {
        // Get payment method ID from config
        $paymentMethod = $paymentMethod ?? config('order.default_payment_method', 'cod');
        $paymentMethodId = Order::getPaymentMethodId($paymentMethod) ?? config('order.default_payment_method_id', 1);
        
        // Get initial status ID from config (confirmed)
        $initialStatusId = config('order.default_status_id', 2);
        
        $deliveryFee = config('order.default_delivery_fee', 40.00);

        $cartItems = $this->cartRepository->getCartWithProducts($userId);

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Your cart is empty. Add items before placing an order.'],
            ]);
        }

        // Verify address belongs to user
        $address = \App\Models\Address::where('id', $addressId)->where('user_id', $userId)->first();
        if (!$address) {
            throw ValidationException::withMessages([
                'address_id' => ['The selected address is invalid or does not belong to you.'],
            ]);
        }

        // Derive shop_id from first cart item
        $shopId = $cartItems->first()->product->shop_id;

        // Calculate totals
        $subtotal = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);
        $total = $subtotal + $deliveryFee;

        // Create delivery address snapshot for backward compatibility
        $deliveryAddressSnapshot = [
            'name'    => $address->name,
            'phone'   => $address->phone,
            'line1'   => $address->line1,
            'city'    => $address->city,
            'pincode' => $address->pincode,
        ];

        // Create the order
        $order = $this->orderRepository->createOrder([
            'user_id'          => $userId,
            'shop_id'          => $shopId,
            'address_id'       => $addressId,
            'status'           => $initialStatusId,        // Store ID instead of string
            'payment_method'   => $paymentMethodId,        // Store ID instead of string
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'total'            => $total,
            'delivery_address' => $deliveryAddressSnapshot,
        ]);

        // Snapshot order items
        $itemsData = $cartItems->map(fn ($item) => [
            'product_id'        => $item->product->id,
            'product_name'      => $item->product->name,
            'product_price'     => $item->product->price,
            'product_image_url' => $item->product->image_url ?? null,
            'quantity'          => $item->quantity,
            'subtotal'          => $item->product->price * $item->quantity,
        ])->toArray();

        $this->orderRepository->createOrderItems($order, $itemsData);

        // Clear the cart
        $this->cartRepository->clearCart($userId);

        return $order->load(['items', 'shop', 'address']);
    }

    public function getUserOrders(int $userId)
    {
        return $this->orderRepository->getUserOrders($userId);
    }

    /**
     * Get user orders with filtering, sorting, and pagination
     */
    public function getUserOrdersWithFilters(
        int $userId,
        ?int $status = null,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 10
    ) {
        return $this->orderRepository->getUserOrdersWithFilters(
            userId: $userId,
            status: $status,
            sortBy: $sortBy,
            sortOrder: $sortOrder,
            perPage: $perPage
        );
    }

    public function getOrderDetail(int $orderId, int $userId): ?Order
    {
        return $this->orderRepository->getOrderById($orderId, $userId);
    }
}
