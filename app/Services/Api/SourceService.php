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
        // Todo: We can add caching here to improve performance, since sources are not frequently updated. We can use Laravel's cache system to store the sources for a certain period of time.
        $sources = $this->source
            ->select('id', 'name', 'slug')
            ->when(isset($data['name']), fn ($query) => $query->where('name', 'like', '%'.$data['name'].'%'))
            ->orderBy('name')
            ->get();

        return $this->success('Success', SourceResource::collection($sources));
    }
}
