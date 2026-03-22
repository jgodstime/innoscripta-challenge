<?php

namespace App\Services\Api;

use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Services\BaseService;

class AuthorService extends BaseService
{
    public function __construct(private Author $author) {}

    public function getAuthor(array $data): array
    {
        // Todo: We can add caching here to improve performance, since authors are not frequently updated. We can use Laravel's cache system to store the authors for a certain period of time.
        $authors = $this->author
            ->select('id', 'name', 'slug')
            ->when(isset($data['name']), fn ($query) => $query->where('name', 'like', '%'.$data['name'].'%'))
            ->orderBy('name')
            ->get();

        return $this->success('Success', AuthorResource::collection($authors));
    }
}
