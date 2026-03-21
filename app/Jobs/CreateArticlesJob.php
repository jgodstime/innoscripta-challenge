<?php

namespace App\Jobs;

use App\Models\Source;
use App\Services\Article\ArticleIngestionService;
use App\Services\Article\DTOs\GetArticleResponseDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateArticlesJob
{
    // Todo: Add [implements ShouldQueue] back after your testing is done to ensure the job is queued properly.

    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private GetArticleResponseDTO $articles,
        private Source $source
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ArticleIngestionService $ingestion): void
    {
        $ingestion->ingest($this->articles, $this->source);
    }
}
