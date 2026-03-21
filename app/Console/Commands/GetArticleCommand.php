<?php

namespace App\Console\Commands;

use App\Jobs\CreateArticlesJob;
use App\Models\Source;
use App\Services\Article\ArticleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetArticleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-article-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get article from all sources';

    /**
     * Execute the console command.
     */
    public function handle(ArticleManager $articleManager)
    {
        $sources = Source::query()
            ->select('id', 'name', 'slug', 'class_key')
            ->get();

        foreach ($sources as $source) {
            try {
                $provider = $articleManager->driver($source->class_key);
                $fromDate = now()->toDateString();

                $getArticles = $provider->getArticle($fromDate);

                if (empty($getArticles->articles) || ! is_array($getArticles->articles)) {
                    continue;
                }

                CreateArticlesJob::dispatch($getArticles, $source);
            } catch (\Throwable $th) {
                Log::error(__METHOD__.' Error: '.$th->getMessage(), [
                    'source_id' => $source->id,
                    'source_slug' => $source->slug,
                    'source_class_key' => $source->class_key,
                ]);
                $this->error("Source failed: {$source->slug}");

                continue;
            }
        }
    }
}
