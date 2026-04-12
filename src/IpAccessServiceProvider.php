<?php

namespace Rboschin\LaravelIpAccess;

use Illuminate\Support\ServiceProvider;
use Rboschin\LaravelIpAccess\Middleware\CheckIpAccess;
use Rboschin\LaravelIpAccess\Controllers\IpAccessWhiteController;
use Rboschin\LaravelIpAccess\Controllers\IpAccessBlackController;

class IpAccessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/ip-access.php', 'ip-access'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/config/ip-access.php' => config_path('ip-access.php'),
        ], 'ip-access-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'ip-access-migrations');

        // Register middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('ip.access', CheckIpAccess::class);

        // Register API routes
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    }
}
