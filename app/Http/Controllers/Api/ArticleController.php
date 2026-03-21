<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetArticleRequest;
use App\Services\Api\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends BaseController
{
    public function __construct(private ArticleService $articleService) {}

    public function getArticle(GetArticleRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $response = $this->articleService->getArticle($request->validated());

        return $this->resolveResponse($response);
    }

    public function showArticle(int|string $articleId): JsonResponse|AnonymousResourceCollection
    {
        $response = $this->articleService->showArticle($articleId);

        return $this->resolveResponse($response);
    }
}
