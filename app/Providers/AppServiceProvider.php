<?php

namespace App\Providers;

use App\Jobs\Helper\ErrorCache;
use App\Jobs\Helper\ErrorCacheInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ErrorCacheInterface::class, ErrorCache::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
