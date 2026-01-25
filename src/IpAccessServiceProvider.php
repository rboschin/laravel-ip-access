<?php

namespace Rboschin\LaravelIpAccess;

use Illuminate\Support\ServiceProvider;
use Rboschin\LaravelIpAccess\Middleware\CheckIpAccess;

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

        // Register middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('ip.access', CheckIpAccess::class);
    }
}
