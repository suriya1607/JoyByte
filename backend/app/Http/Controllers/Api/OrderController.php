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

    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->getUserOrders($request->user()->id);

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
}
