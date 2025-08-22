<?php

namespace XuDev\Wework;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use XuDev\Wework\Facade\Wework;
use XuDev\Wework\Facade\WeworkCrypt;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Wework::class, function () {
            return new Wework;
        });

        $this->app->singleton(WeworkCrypt::class, function () {
            return new WeworkCrypt;
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
