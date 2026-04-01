<?php

namespace App\Services\Shop;

use App\Repositories\Shop\ShopRepository;

class ShopService
{
    public function __construct(private readonly ShopRepository $shopRepository)
    {
    }

    public function getShops()
    {
        return $this->shopRepository->getAllShops();
    }

    public function getShopDetail(int $shopId)
    {
        return $this->shopRepository->getById($shopId);
    }

    public function getShopWithProducts(int $shopId, ?int $categoryId = null)
    {
        return $this->shopRepository->getShopWithProducts($shopId, $categoryId);
    }

    public function getByLocation(string $location)
    {
        return $this->shopRepository->getByLocation($location);
    }
}
