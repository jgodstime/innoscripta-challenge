<?php

namespace App\Services\Article\Providers;

use App\Services\Article\ArticleProviderInterface;
use App\Services\Article\BaseArticleService;
use App\Services\Article\DTOs\GetArticleResponseDTO;
use App\Services\Article\DTOs\ShowArticleResponseDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Guardian extends BaseArticleService implements ArticleProviderInterface
{
    protected $baseUrl;

    protected $http;

    public function __construct()
    {
        $this->baseUrl = config('article.guardian.base_url');
    }

    /**
     * Fetch articles from the Guardian API.
     */
    public function getArticle(?string $fromDate = null, ?string $toDate = null, int $perPage = 15): GetArticleResponseDTO
    {
        $data = [
            'api-key' => config('article.guardian.api_key'),
            'section' => 'technology',
            'from-date' => $fromDate,
            'to-date' => $toDate,
            'show-fields' => 'all',
            // 'page' => $page,
            'page-size' => $perPage,
            'format' => 'json',
        ];

        try {
            $params = array_filter($data, static fn ($value) => $value !== null && $value !== '');
            $response = Http::get("{$this->baseUrl}/search", $params);

            if ($response->failed()) {
                Log::error(__METHOD__.' Error: '.$response->status().' '.$response->body());
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $jsonResponse = $response->json();
            $apiResponse = $jsonResponse['response'] ?? null;

            if (! is_array($apiResponse) || ($apiResponse['status'] ?? null) !== 'ok') {
                Log::error(__METHOD__.' Error: '.json_encode($jsonResponse));
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $results = $apiResponse['results'] ?? [];
            if (! is_array($results) || count($results) === 0) {
                throw new HttpException(404, 'No articles found for the provided filters.');
            }

            $articles = [];
            foreach ($results as $article) {
                $fields = $article['fields'] ?? [];

                $id = (string) ($article['id'] ?? '');
                $title = (string) ($article['webTitle'] ?? ($fields['headline'] ?? ''));
                $content = (string) ($fields['body'] ?? ($fields['bodyText'] ?? ''));
                $imageUrl = (string) ($fields['thumbnail'] ?? '');
                $authorName = (string) ($fields['byline'] ?? 'Guardian');
                $category = (string) ($article['sectionName'] ?? ($article['sectionId'] ?? ''));
                $webUrl = (string) ($article['webUrl'] ?? null);

                if ($id === '' ||
                    $title === '' ||
                    $content === '' ||
                    $imageUrl === '' ||
                    $authorName === '' ||
                    $category === '') {
                    continue;
                }

                $articles[] = [
                    'id' => $id,
                    'title' => $title,
                    'summary' => $fields['trailText'] ?? ($fields['standfirst'] ?? null),
                    'content' => $content,
                    'image_url' => $imageUrl,
                    'web_url' => $webUrl,
                    'author_name' => $authorName,
                    'source' => config('article.guardian.driver'),
                    'published_at' => $article['webPublicationDate'],
                    'updated_at' => $fields['lastModified'] ?? $article['webPublicationDate'],
                    'category' => $category,
                ];
            }

            if ($articles === []) {
                Log::error(__METHOD__.' Error: No valid articles after normalization');
                throw new HttpException(500, 'Unable to get articles. Please try again later.');
            }

            $meta = [
                'total' => $apiResponse['total'] ?? null,
                'start_index' => $apiResponse['startIndex'] ?? null,
                'page_size' => $apiResponse['pageSize'] ?? null,
                'current_page' => $apiResponse['currentPage'] ?? null,
                'pages' => $apiResponse['pages'] ?? null, // total pages available
                'order_by' => $apiResponse['orderBy'] ?? null,
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
        try {
            $params = [
                'api-key' => config('article.guardian.api_key'),
                'show-fields' => 'all',
                'format' => 'json',
            ];

            $response = Http::get("{$this->baseUrl}/{$articleId}", $params);

            if ($response->failed()) {
                Log::error(__METHOD__.' Error: '.$response->status().' '.$response->body());
                throw new HttpException(500, 'Unable to fetch article. Please try again later.');
            }

            $jsonResponse = $response->json();
            $apiResponse = $jsonResponse['response'] ?? null;

            if (! is_array($apiResponse) || ($apiResponse['status'] ?? null) !== 'ok') {
                Log::error(__METHOD__.' Error: '.json_encode($jsonResponse));
                throw new HttpException(500, 'Unable to fetch article. Please try again later.');
            }

            $content = $apiResponse['content'] ?? null;
            if (! is_array($content)) {
                Log::error(__METHOD__.' Error: content missing', ['response' => $apiResponse]);
                throw new HttpException(500, 'Unable to fetch article. Please try again later.');
            }

            $fields = $content['fields'] ?? [];

            $id = (string) ($content['id'] ?? '');
            $title = (string) ($content['webTitle'] ?? ($fields['headline'] ?? ''));
            $summary = $fields['trailText'] ?? ($fields['standfirst'] ?? null);
            $body = (string) ($fields['bodyText'] ?? ($fields['body'] ?? ''));
            $imageUrl = (string) ($fields['thumbnail'] ?? '');
            $authorName = (string) ($fields['byline'] ?? 'Guardian');
            $webUrl = (string) ($content['webUrl'] ?? '');
            $publishedAt = (string) ($content['webPublicationDate'] ?? '');
            $updatedAt = (string) ($content['webPublicationDate'] ?? '');
            $category = (string) ($content['sectionName'] ?? ($content['sectionId'] ?? ''));

            if (
                $id === '' ||
                $title === '' ||
                $body === '' ||
                $imageUrl === '' ||
                $authorName === '' ||
                $publishedAt === '' ||
                $updatedAt === '' ||
                $category === ''
            ) {
                Log::error(__METHOD__.' Error: Missing required content fields', [
                    'id' => $id,
                    'title' => $title,
                    'content' => $body !== '' ? 'present' : '',
                    'image_url' => $imageUrl,
                    'author_name' => $authorName,
                    'published_at' => $publishedAt,
                    'updated_at' => $updatedAt,
                    'category' => $category,
                ]);
                throw new HttpException(500, 'Unable to fetch article. Please try again later.');
            }

            $normalized = $this->success('Article retrieved successfully', [
                'article' => [
                    'id' => $id,
                    'title' => $title,
                    'summary' => $summary,
                    'content' => $body,
                    'image_url' => $imageUrl,
                    'web_url' => $webUrl,
                    'author_name' => $authorName,
                    'source' => config('article.guardian.driver'),
                    'published_at' => $publishedAt,
                    'updated_at' => $updatedAt,
                    'category' => $category,
                ],
            ]);

            return ShowArticleResponseDTO::fromArray($normalized);
        } catch (\Throwable $th) {
            Log::error(__METHOD__.' Error: '.$th->getMessage());
            throw new HttpException(500, 'Unable to fetch article. Please try again later.');
        }
    }
}
