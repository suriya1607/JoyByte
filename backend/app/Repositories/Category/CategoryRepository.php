<?php

namespace App\Repositories\Category;

use App\Models\Category;

class CategoryRepository
{
    public function getAllCategories()
    {
        return Category::query()
            ->orderBy('display_order')
            ->get();
    }

    public function getById(int $id): ?Category
    {
        return Category::query()->find($id);
    }
}
