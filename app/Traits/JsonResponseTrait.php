<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait JsonResponseTrait
{
    public function resolveResponse($data): JsonResponse|AnonymousResourceCollection
    {
        $statusCode = $data['status_code'];

        unset($data['status_code']);

        if ($data['data'] instanceof AnonymousResourceCollection) {
            return $data['data'];
        }

        return response()->json($data, $statusCode);
    }
}
