<?php

namespace Wisdech\Wework;

use Illuminate\Support\ServiceProvider;
use Wisdech\Wework\WeworkSDK;

class WeworkProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WeworkSDK::class, function ($app) {
            return new WeworkSDK(
                config('wework.corp_id'),
                config('wework.agent_id'),
                config('wework.secret'),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/wework.php' => config_path('wework.php'),
        ], 'wework-config');
    }
}
