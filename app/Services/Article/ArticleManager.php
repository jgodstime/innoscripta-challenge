<?php

namespace App\Services\Article;

use Illuminate\Support\Manager;

class ArticleManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('article.default');
    }

    protected function createDriver($driver)
    {
        $providers = config('article.providers', []);
        $class = $providers[$driver] ?? null;

        if (! $class) {
            throw new \InvalidArgumentException("Unknown driver [$driver].");
        }

        return app($class);
    }

    public function driver($driver = null): ArticleProviderInterface
    {
        /** @var ArticleProviderInterface $driverInstance */
        $driverInstance = parent::driver($driver);

        return $driverInstance;
    }
}
