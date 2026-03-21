<?php

namespace App\Services\Api;

use App\Http\Resources\SourceResource;
use App\Models\Source;
use App\Services\BaseService;

class SourceService extends BaseService
{
    public function __construct(private Source $source) {}

    public function getSource(array $data): array
    {
        $sources = $this->source
            ->select('id', 'name', 'slug')
            ->when(isset($data['name']), fn ($query) => $query->where('name', 'like', '%'.$data['name'].'%'))
            ->orderBy('name')
            ->get();

        return $this->success('Success', SourceResource::collection($sources));
    }
}
