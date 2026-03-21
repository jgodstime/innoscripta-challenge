<?php

namespace App\Services\Article\DTOs;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GetArticleResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $articles,
        public readonly array $meta,
        public readonly int $status_code,
    ) {}

    /**
     * @throws ValidationException
     */
    public static function fromArray(array $data): self
    {
        $validator = Validator::make($data, [
            'success' => 'required|boolean',
            'message' => 'required|string',
            'data' => 'required|array',
            'data.articles' => 'required|array',
            'data.articles.*.id' => 'required|string',
            'data.articles.*.title' => 'required|string',
            'data.articles.*.summary' => 'nullable|string',
            'data.articles.*.content' => 'nullable|string',
            'data.articles.*.image_url' => 'required|string',
            'data.articles.*.web_url' => 'required|string',
            'data.articles.*.author_name' => 'required|string',
            'data.articles.*.source' => ['required', Rule::in(array_keys(config('article.providers', [])))],
            'data.articles.*.published_at' => 'required|date',
            'data.articles.*.updated_at' => 'required|date',
            'data.articles.*.category' => 'required|string',
            'data.meta' => 'nullable|array',
            'status_code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return new self(
            success: $data['success'],
            message: $data['message'],
            articles: $data['data']['articles'],
            meta: $data['data']['meta'] ?? [],
            status_code: $data['status_code'],
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'status_code' => $this->status_code,
            'data' => [
                'articles' => $this->articles,
                'meta' => $this->meta,
            ],
        ];
    }
}
