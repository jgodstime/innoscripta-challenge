<?php

namespace App\Services\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\BaseService;

class CategoryService extends BaseService
{
    public function __construct(private Category $category) {}

    public function getCategory(array $data): array
    {
        // Todo: We can add caching here to improve performance, since categories are not frequently updated. We can use Laravel's cache system to store the categories for a certain period of time.
        $categories = $this->category
            ->select('id', 'name', 'slug')
            ->when(isset($data['name']), fn ($query) => $query->where('name', 'like', '%'.$data['name'].'%'))
            ->orderBy('name')
            ->get();

        return $this->success('Success', CategoryResource::collection($categories));
    }
}
