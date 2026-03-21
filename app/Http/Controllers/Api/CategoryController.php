<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetCategoryRequest;
use App\Services\Api\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends BaseController
{
    public function __construct(private CategoryService $categoryService) {}

    public function getCategory(GetCategoryRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $response = $this->categoryService->getCategory($request->validated());

        return $this->resolveResponse($response);
    }
}
