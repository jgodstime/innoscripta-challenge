<?php

namespace App\Services\Article\Providers;

use App\Services\Article\ArticleProviderInterface;
use App\Services\Article\BaseArticleService;
use App\Services\Article\DTOs\GetArticleResponseDTO;
use App\Services\Article\DTOs\ShowArticleResponseDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NewYorkTimes extends BaseArticleService implements ArticleProviderInterface
{
    protected $baseUrl;

    protected $http;

    public function __construct()
    {
        $this->baseUrl = config('article.new_york_times.base_url');
    }

    /**
     * Fetch articles from the New York Times API.
     */
    public function getArticle(?string $fromDate = null, ?string $toDate = null, int $perPage = 15): GetArticleResponseDTO
    {
        $data = [
            'api-key' => config('article.new_york_times.api_key'),
            'begin_date' => $fromDate ? now()->parse($fromDate)->format('Ymd') : null,
            'end_date' => $toDate ? now()->parse($toDate)->format('Ymd') : null,
            'page' => 0,
        ];

        try {
            $params = array_filter($data, static fn ($value) => $value !== null && $value !== '');
            $response = Http::get("{$this->baseUrl}/svc/search/v2/articlesearch.json", $params);

            if ($response->failed()) {
                Log::error(__METHOD__.' Error: '.$response->status().' '.$response->body());
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $jsonResponse = $response->json();
            if (($jsonResponse['status'] ?? null) !== 'OK') {
                Log::error(__METHOD__.' Error: '.json_encode($jsonResponse));
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $apiResponse = $jsonResponse['response'] ?? null;
            $docs = $apiResponse['docs'] ?? [];
            if (! is_array($docs) || count($docs) === 0) {
                throw new HttpException(404, 'No articles found for the provided filters.');
            }

            $articles = [];
            foreach ($docs as $doc) {
                $headline = $doc['headline'] ?? [];
                $byline = $doc['byline'] ?? [];
                $multimedia = $doc['multimedia'] ?? [];
                $defaultMedia = $multimedia['default'] ?? null;

                $id = (string) ($doc['_id'] ?? ($doc['uri'] ?? ''));
                $title = (string) ($headline['main'] ?? '');
                $summary = $doc['abstract'] ?? ($doc['snippet'] ?? null);
                $content = (string) ($doc['abstract'] ?? ($doc['snippet'] ?? ''));
                $imageUrl = (string) ($defaultMedia['url'] ?? '');
                $authorName = (string) ($byline['original'] ?? 'The New York Times');
                $category = (string) ($doc['section_name'] ?? ($doc['news_desk'] ?? ''));
                $webUrl = (string) ($doc['web_url'] ?? null);
                $publishedAt = (string) ($doc['pub_date'] ?? null);

                if (
                    $id === '' ||
                    $title === '' ||
                    $content === '' ||
                    $imageUrl === '' ||
                    $authorName === '' ||
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
                    'author_name' => $authorName,
                    'source' => config('article.new_york_times.driver'),
                    'published_at' => $publishedAt,
                    'updated_at' => $publishedAt,
                    'category' => $category,
                ];
            }

            if ($articles === []) {
                Log::error(__METHOD__.' Error: No valid articles after normalization');
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $meta = [
                'total' => $apiResponse['meta']['hits'] ?? null,
                'page_size' => $apiResponse['meta']['docs'] ?? null,
                'current_page' => $apiResponse['meta']['offset'] ?? null,
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
        throw new HttpException(501, 'Not implemented for New York Times provider.');
    }
}
