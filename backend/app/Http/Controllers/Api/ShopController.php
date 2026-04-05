<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Shop\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(private readonly ShopService $shopService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $location = $request->query('location');

        $shops = $location
            ? $this->shopService->getByLocation($location)
            : $this->shopService->getShops();

        return ApiResponse::success($shops, 'Shops fetched successfully.');
    }

    public function show(int $id): JsonResponse
    {
        $shop = $this->shopService->getShopDetail($id);

        if (!$shop) {
            return ApiResponse::error('Shop not found.', null, 404);
        }

        return ApiResponse::success($shop, 'Shop retrieved successfully.');
    }
}
