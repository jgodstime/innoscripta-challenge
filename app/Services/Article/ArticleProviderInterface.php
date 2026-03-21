<?php

namespace App\Services\Article;

use App\Services\Article\DTOs\GetArticleResponseDTO;
use App\Services\Article\DTOs\ShowArticleResponseDTO;

interface ArticleProviderInterface
{
    public function getArticle(?string $fromDate = null, ?string $toDate = null, int $perPage = 15): GetArticleResponseDTO;

    public function showArticle(string|int $articleId): ShowArticleResponseDTO;
}
