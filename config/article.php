<?php

use App\Services\Article\Providers\Guardian;
use App\Services\Article\Providers\NewYorkTimes;
use App\Services\Article\Providers\TheNews;

return [
    'default' => env('DEFAULT_ARTICLE_SOURCE', 'guardian'), 

      /**
     * The key value of the providers should be the same as the class name of the article provider
     * e.g app/Services/Article/Providers/NewYorkTimes.php = NewYorkTimes => new_york_times
     * e.g app/Services/Article/Providers/Guardian.php = Guardian => guardian
     * So if you have a new article provider, you can easily add it here and the seeder will take care of the rest and the manager will be able to use it without any additional configuration.
     * With this we can never go wrong when adding new sources as long as we follow the naming convention for the key value of the providers
     */
    'providers' => [
        'guardian' => Guardian::class,
        'new_york_times' => NewYorkTimes::class,
        'the_news' => TheNews::class,
    ],

    'guardian' => [
        'base_url' => env('GUARDIAN_BASE_URL'),
        'api_key' => env('GUARDIAN_API_KEY'),
        'driver' => 'guardian',
    ],

    'new_york_times' => [
        'base_url' => env('NYT_BASE_URL'),
        'api_key' => env('NYT_API_KEY'),
        'driver' => 'new_york_times',
    ],

    'the_news' => [
        'base_url' => env('THE_NEWS_BASE_URL'),
        'api_key' => env('THE_NEWS_API_KEY'),
        'driver' => 'the_news',
    ],

];
