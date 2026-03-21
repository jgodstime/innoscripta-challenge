<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\Article\DTOs\GetArticleResponseDTO;
use Illuminate\Support\Carbon;

class ArticleIngestionService
{
    public function ingest(GetArticleResponseDTO $articles, Source $source): void
    {
        foreach ($articles->articles as $article) {
            $reference = $article['id'];
            $title = $article['title'];

            $articleExists = Article::query()
                ->where('reference', $reference)
                ->where('source_id', $source->id)
                ->exists();

            if ($articleExists) {
                continue;
            }

            $categoryName = $article['category'] ?: 'general';
            $authorName = $article['author_name'] ?: 'unknown';

            $category = Category::query()->firstOrCreate(
                ['name' => $categoryName]
            );

            $author = Author::query()->firstOrCreate(
                ['name' => $authorName]
            );

            $publishedAt = null;
            if (! empty($article['published_at'])) {
                $publishedAt = Carbon::parse($article['published_at'])->toDateTimeString();
            }

            Article::create([
                'title' => $title,
                'summary' => $article['summary'] ?? null,
                'reference' => $reference,
                'body' => $article['content'] ?? null,
                'category_id' => $category->id,
                'source_id' => $source->id,
                'author_id' => $author->id,
                'image_url' => $article['image_url'],
                'web_url' => $article['web_url'],
                'published_at' => $publishedAt,
            ]);
        }
    }
}
