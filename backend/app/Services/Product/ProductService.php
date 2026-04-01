<?php

namespace App\Services\Product;

use App\Repositories\Product\ProductRepository;

class ProductService
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function getShopProducts(int $shopId, ?int $categoryId = null)
    {
        if ($categoryId) {
            return $this->productRepository->getByShopAndCategory($shopId, $categoryId);
        }

        return $this->productRepository->getByShop($shopId);
    }

    public function getProductById(int $id)
    {
        return $this->productRepository->getById($id);
    }
}
