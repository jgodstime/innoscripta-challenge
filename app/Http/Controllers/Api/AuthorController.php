<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetAuthorRequest;
use App\Services\Api\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends BaseController
{
    public function __construct(private AuthorService $authorService) {}

    public function getAuthor(GetAuthorRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $response = $this->authorService->getAuthor($request->validated());

        return $this->resolveResponse($response);
    }
}
