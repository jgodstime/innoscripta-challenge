<?php

namespace App\Services\Article\Providers;

use App\Services\Article\ArticleProviderInterface;
use App\Services\Article\BaseArticleService;
use App\Services\Article\DTOs\GetArticleResponseDTO;
use App\Services\Article\DTOs\ShowArticleResponseDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TheNews extends BaseArticleService implements ArticleProviderInterface
{
    protected $baseUrl;

    protected $http;

    public function __construct()
    {
        $this->baseUrl = config('article.the_news.base_url');
    }

    /**
     * Fetch articles from TheNews API.
     */
    public function getArticle(?string $fromDate = null, ?string $toDate = null, int $perPage = 15): GetArticleResponseDTO
    {
        $data = [
            'api_token' => config('article.the_news.api_key'),
            'published_on' => $fromDate ?? null,
            'published_before' => $toDate ?? null,
            'limit' => $perPage,
            'language' => 'en',
        ];

        try {
            $params = array_filter($data, static fn ($value) => $value !== null && $value !== '');
            $response = Http::get("{$this->baseUrl}/v1/news/all", $params);

            if ($response->failed()) {
                Log::error(__METHOD__.' Error: '.$response->status().' '.$response->body());
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $jsonResponse = $response->json();
            $dataResponse = $jsonResponse['data'] ?? [];
            if (! is_array($dataResponse) || count($dataResponse) === 0) {
                throw new HttpException(404, 'No articles found for the provided filters.');
            }

            $articles = [];
            foreach ($dataResponse as $item) {
                $id = (string) ($item['uuid'] ?? '');
                $title = (string) ($item['title'] ?? '');
                $summary = $item['description'] ?? ($item['snippet'] ?? null);
                $content = (string) ($item['snippet'] ?? ($item['description'] ?? ''));
                $imageUrl = (string) ($item['image_url'] ?? '');
                $webUrl = (string) ($item['url'] ?? null);
                $publishedAt = (string) ($item['published_at'] ?? null);
                $sourceName = (string) ($item['source'] ?? '');
                $category = 'General';
                if (! empty($item['categories']) && is_array($item['categories'])) {
                    $category = (string) ($item['categories'][0] ?? 'General');
                }

                if (
                    $id === '' ||
                    $title === '' ||
                    $content === '' ||
                    $imageUrl === '' ||
                    $category === ''
                ) {
                    continue;
                }

                $articles[] = [
                    'id' => $id,
                    'title' => $title,
                    'summary' => $summary,
                    'content' => $content,
                    'image_url' => $imageUrl,
                    'web_url' => $webUrl,
                    'author_name' => $sourceName !== '' ? $sourceName : 'The News',
                    'source' => config('article.the_news.driver'),
                    'published_at' => $publishedAt,
                    'updated_at' => $publishedAt,
                    'category' => $category,
                ];
            }

            if ($articles === []) {
                Log::error(__METHOD__.' Error: No valid articles after normalization');
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $metaResponse = $jsonResponse['meta'] ?? [];
            $meta = [
                'total' => $metaResponse['found'] ?? null,
                'page_size' => $metaResponse['returned'] ?? null,
                'current_page' => $metaResponse['page'] ?? null,
            ];

            $normalized = $this->success('Articles retrieved successfully', [
                'articles' => $articles,
                'meta' => $meta,
            ]);

            return GetArticleResponseDTO::fromArray($normalized);
        } catch (\Throwable $th) {
            Log::error(__METHOD__.' Error: '.$th->getMessage());
            throw new HttpException(500, 'Unable to get articles. Please try again later.');
        }
    }

    public function showArticle(string|int $articleId): ShowArticleResponseDTO
    {
        throw new HttpException(501, 'Not implemented for TheNews provider.');
    }
}
