<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetSourceRequest;
use App\Services\Api\SourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SourceController extends BaseController
{
    public function __construct(private SourceService $sourceService) {}

    public function getSource(GetSourceRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $response = $this->sourceService->getSource($request->validated());

        return $this->resolveResponse($response);
    }
}
