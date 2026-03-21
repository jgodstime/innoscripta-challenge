<?php

namespace App\Services\Article\DTOs;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShowArticleResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $article,
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
            'data.article' => 'required|array',
            'data.article.id' => 'required|string',
            'data.article.title' => 'required|string',
            'data.article.summary' => 'nullable|string',
            'data.article.content' => 'nullable|string',
            'data.article.image_url' => 'required|string',
            'data.article.web_url' => 'required|string',
            'data.article.author_name' => 'required|string',
            'data.article.source' => ['required', Rule::in(array_keys(config('article.providers', [])))],
            'data.article.published_at' => 'required|date',
            'data.article.updated_at' => 'required|date',
            'data.article.category' => 'required|string',
            'status_code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return new self(
            success: $data['success'],
            message: $data['message'],
            article: $data['data']['article'],
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
                'article' => $this->article,
            ],
        ];
    }
}
