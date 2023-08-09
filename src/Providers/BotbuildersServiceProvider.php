<?php

namespace Hrgweb\Botbuilders\Providers;

use Illuminate\Support\ServiceProvider;

class BotbuildersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Configs
        $this->publishes([
            __DIR__ . '/../../config/botbuilders.php' => config_path('botbuilders.php')
        ]);

        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
