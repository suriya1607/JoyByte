<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getCategories();

        return ApiResponse::success($categories, 'Categories fetched successfully.');
    }
}
