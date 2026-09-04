<?php

namespace Rapidez\Postcode;

use Illuminate\Support\ServiceProvider;

class PostcodeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rapidez/postcode.php', 'rapidez.postcode');

        $this->app->singleton(PostcodeManager::class);
    }

    public function boot(): void
    {
        $this->bootRoutes()->bootPublishables();
    }

    public function bootRoutes(): self
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        return $this;
    }

    public function bootPublishables(): self
    {
        $this->publishes([
            __DIR__ . '/../config/rapidez/postcode.php' => config_path('rapidez/postcode.php'),
        ], 'rapidez-postcode-config');

        return $this;
    }
}
