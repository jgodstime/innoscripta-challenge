
## Project Setup

### Requirements

- PHP 8.2+
- Composer
- MySQL

### Installation

1. Install PHP dependencies:

	```bash
	composer install
	```

2. Create your environment file:

	```bash
	cp .env.example .env
	```

3. Generate the application key:

	```bash
	php artisan key:generate
	```

4. Update `.env` with your database credentials and Sources API keys:

	```ini
	DB_DATABASE=innoscripta_test
	DB_USERNAME=your_user
	DB_PASSWORD=your_password

	GUARDIAN_BASE_URL=https://content.guardianapis.com
    GUARDIAN_API_KEY=your_key

    NYT_BASE_URL=https://api.nytimes.com
    NYT_API_KEY=your_key

    THE_NEWS_BASE_URL=https://api.thenewsapi.com
    THE_NEWS_API_KEY=your_key
	```

5. Run migrations:

	```bash
	php artisan migrate
	```

6. Seed the database (to add exsisting sources (Guardian, NYT and The News), user and preferred category/author, and populte articles from providers) see DatabaseSeeder.php

	```bash
	php artisan db:seed
	```

### Run the App

Start the API server:

```bash
php artisan serve
```

### Fetch Articles

Run the command to pull articles from sources and store new entries:

```bash
php artisan app:get-article-command
```

### Scheduler

The scheduler is defined in [routes/console.php](routes/console.php). To run it automatically, add this cron entry on your server:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker (if using queued jobs, but the current job is not queued just for this testing. see comment in CreateArticlesJob.php)

```bash
php artisan queue:work
```

## Add a New Source Provider

To add a new article source (provider), follow these steps:

1. **Create the provider class** in [app/Services/Article/Providers](app/Services/Article/Providers) and implement `ArticleProviderInterface`.
	- The class name should follow `StudlyCase` (e.g. `MyNewSource`).
	- Implement `getArticle()` (and `showArticle()` if needed).

2. **Register the provider in config** in [config/article.php](config/article.php).
	- Add a new entry in `providers` with the driver key (snake_case) and class.
	- Add the provider config entry with `base_url`, `api_key`, and `driver`.
	- Example provider key: `my_new_source`.

3. **Add env variables** in `.env`.
	- Add `MY_NEW_SOURCE_BASE_URL` and `MY_NEW_SOURCE_API_KEY` (or whatever keys you define in config).

4. **Seed the source** 
	- Run the source seeder so it appears in the `sources` table.

5. **Run the fetch command** to verify:

	```bash
	php artisan app:get-article-command
	```
