<?php

namespace App\Services\Category;

use App\Repositories\Category\CategoryRepository;

class CategoryService
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function getCategories()
    {
        return $this->categoryRepository->getAllCategories();
    }

    public function getCategoryById(int $id)
    {
        return $this->categoryRepository->getById($id);
    }
}
