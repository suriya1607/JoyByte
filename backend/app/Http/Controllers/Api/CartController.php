<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->cartService->getCart($request->user()->id);

        return ApiResponse::success($items, 'Cart items fetched successfully.');
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        $this->cartService->addToCart(
            $request->user()->id,
            $request->validated('product_id'),
            $request->validated('quantity')
        );

        $items = $this->cartService->getCart($request->user()->id);

        return ApiResponse::success($items, 'Item added to cart successfully.', 201);
    }

    public function update(int $productId, UpdateCartItemRequest $request): JsonResponse
    {
        $this->cartService->updateCartItem(
            $request->user()->id,
            $productId,
            $request->validated('quantity')
        );

        $items = $this->cartService->getCart($request->user()->id);

        return ApiResponse::success($items, 'Cart item updated successfully.');
    }

    public function destroy(int $productId, Request $request): JsonResponse
    {
        $this->cartService->removeFromCart($request->user()->id, $productId);

        $items = $this->cartService->getCart($request->user()->id);

        return ApiResponse::success($items, 'Item removed from cart successfully.');
    }
}
