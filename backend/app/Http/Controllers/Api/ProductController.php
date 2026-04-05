<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function byShop(int $shopId, Request $request): JsonResponse
    {
        $categoryId = $request->query('category_id');

        $products = $this->productService->getShopProducts($shopId, $categoryId ? (int) $categoryId : null);

        return ApiResponse::success($products, 'Products fetched successfully.');
    }
}
