<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->placeOrder(
            userId: $request->user()->id,
            addressId: $request->validated('address_id'),
            paymentMethod: $request->validated('payment_method')
        );

        return ApiResponse::success($order, 'Order placed successfully.', 201);
    }

    /**
     * Get user's orders with filtering, sorting, and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $sortBy = $request->query('sort_by', 'created_at'); // created_at, updated_at, total
        $sortOrder = $request->query('sort_order', 'desc'); // asc, desc
        $perPage = $request->query('per_page', 10);

        $orders = $this->orderService->getUserOrdersWithFilters(
            userId: $request->user()->id,
            status: $status,
            sortBy: $sortBy,
            sortOrder: $sortOrder,
            perPage: $perPage
        );

        return ApiResponse::success($orders, 'Orders fetched successfully.');
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $order = $this->orderService->getOrderDetail($id, $request->user()->id);

        if (! $order) {
            return ApiResponse::error('Order not found.', null, 404);
        }

        return ApiResponse::success($order, 'Order retrieved successfully.');
    }

    /**
     * Update order status (admin only - for demo/testing)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'integer', 'in:' . implode(',', array_keys(config('order.statuses', [])))],
        ]);

        $order = $this->orderService->getOrderDetail($id, $request->user()->id);

        if (! $order) {
            return ApiResponse::error('Order not found.', null, 404);
        }

        $order->update(['status' => $request->status]);
        $order->refresh();

        return ApiResponse::success($order, 'Order status updated successfully.');
    }

    /**
     * Get order tracking info (location simulation for demo)
     */
    public function tracking(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getOrderDetail($id, $request->user()->id);

        if (! $order) {
            return ApiResponse::error('Order not found.', null, 404);
        }

        // Mock tracking data - in production, this would come from delivery partner's location
        $trackingData = [
            'order_id' => $order->id,
            'status_id' => $order->status,
            'status_label' => $order->status_label,
            'delivery_agent' => [
                'name' => 'Ramesh Kumar',
                'phone' => '9876543210',
                'vehicle' => 'Two Wheeler',
                'rating' => 4.8,
            ],
            'current_location' => [
                'latitude' => 13.0827 + (rand(-100, 100) / 10000), // Mock location near Chennai
                'longitude' => 80.2707 + (rand(-100, 100) / 10000),
                'address' => 'Near Anna Nagar, Chennai',
                'updated_at' => now()->toIso8601String(),
            ],
            'estimated_delivery' => $order->created_at->addMinutes(45)->toIso8601String(),
            'distance_remaining_km' => rand(2, 10),
            'milestones' => [
                [
                    'id' => 1,
                    'status_id' => 1,
                    'label' => 'Order Placed',
                    'completed' => true,
                    'timestamp' => $order->created_at->toIso8601String(),
                ],
                [
                    'id' => 2,
                    'status_id' => 2,
                    'label' => 'Confirmed',
                    'completed' => $order->status >= 2,
                    'timestamp' => $order->status >= 2 ? $order->created_at->addMinutes(5)->toIso8601String() : null,
                ],
                [
                    'id' => 3,
                    'status_id' => 3,
                    'label' => 'Being Prepared',
                    'completed' => $order->status >= 3,
                    'timestamp' => $order->status >= 3 ? $order->created_at->addMinutes(15)->toIso8601String() : null,
                ],
                [
                    'id' => 4,
                    'status_id' => 4,
                    'label' => 'Out for Delivery',
                    'completed' => $order->status >= 4,
                    'timestamp' => $order->status >= 4 ? $order->created_at->addMinutes(25)->toIso8601String() : null,
                ],
                [
                    'id' => 5,
                    'status_id' => 5,
                    'label' => 'Delivered',
                    'completed' => $order->status >= 5,
                    'timestamp' => $order->status >= 5 ? $order->created_at->addMinutes(45)->toIso8601String() : null,
                ],
            ]
        ];

        return ApiResponse::success($trackingData, 'Order tracking information retrieved.');
    }
}
