<?php

namespace App\Services\Api;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\BaseService;

class ArticleService extends BaseService
{
    public function __construct(
        private Article $article
    ) {}

    public function getArticle(array $data): array
    {
        $articles = $this->article
            ->with(['source:id,name', 'category:id,name', 'author:id,name'])
            ->select('id', 'title', 'slug', 'summary', 'image_url', 'web_url', 'author_id', 'source_id', 'published_at', 'updated_at', 'category_id')
            ->when(isset($data['title']), fn ($query) => $query->where('title', 'like', '%'.$data['title'].'%'))
            ->when(isset($data['category_id']), fn ($query) => $query->where('category_id', $data['category_id']))
            ->when(isset($data['source_id']), fn ($query) => $query->where('source_id', $data['source_id']))
            ->when(isset($data['published_from']), fn ($query) => $query->whereDate('published_at', '>=', $data['published_from']))
            ->when(isset($data['published_to']), fn ($query) => $query->whereDate('published_at', '<=', $data['published_to']))
            ->when(isset($data['preference_by_author']) && $data['preference_by_author'] == 1, function ($query) {
                $query->whereIn('author_id', auth()->user()?->preferredAuthors()->pluck('id') ?? []);
            })
            ->when(isset($data['preference_by_source']) && $data['preference_by_source'] == 1, function ($query) {
                $query->whereIn('source_id', auth()->user()?->preferredSources()->pluck('id') ?? []);
            })
            ->when(isset($data['preference_by_category']) && $data['preference_by_category'] == 1, function ($query) {
                $query->whereIn('category_id', auth()->user()?->preferredCategories()->pluck('id') ?? []);
            })
            ->latest()
            ->simplePaginate($data['per_page'] ?? 15);

        return $this->success('Success', ArticleResource::collection($articles));
    }

    public function showArticle(string|int $articleId): array
    {
        $article = $this->article
            ->select('id', 'title', 'slug', 'summary', 'content', 'image_url', 'web_url', 'author_id', 'source_id', 'published_at', 'updated_at', 'category_id')
            ->with(['source:id,name', 'category:id,name', 'author:id,name'])
            ->where(function ($query) use ($articleId) {
                $query->where('id', $articleId)
                    ->orWhere('slug', $articleId);
            })
            ->first();

        if (! $article) {
            return $this->error('Article not found', [], 404);
        }

        return $this->success('Success', new ArticleResource($article));
    }
}
