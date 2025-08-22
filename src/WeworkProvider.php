<?php

namespace XuDev\Wework;

use Illuminate\Support\ServiceProvider;
use XuDev\Wework\Facade\Wework;

class WeworkProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Wework::class, function ($app) {
            return new Wework;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/wework.php' => config_path('wework.php'),
        ], 'wework-config');
    }
}
