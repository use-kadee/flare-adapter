<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelFlare\FlareConfig;

class KadeeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kadee.php', 'kadee');

        // Modify FlareConfig after it's resolved to use Kadee sender
        // We use resolving() because FlareServiceProvider creates the config
        // directly and stores it, then registers a singleton that returns it.
        // Using extend() wouldn't work because the singleton is never resolved
        // by FlareServiceProvider itself.
        $this->app->resolving(FlareConfig::class, function (FlareConfig $config) {
            $project = config('kadee.project');
            $key = config('kadee.key');

            if (! $project || ! $key) {
                return;
            }

            $config->sender = KadeeSender::class;
            $config->senderConfig = [
                'projectId' => $project,
                'secret' => $key,
                'endpoint' => config('kadee.endpoint'),
                'timeout' => config('kadee.timeout'),
            ];
        });

        // Also modify the config directly if it's already been created
        // This handles the case where FlareServiceProvider ran first
        $this->app->booted(function () {
            if (! $this->app->bound(FlareConfig::class)) {
                return;
            }

            $project = config('kadee.project');
            $key = config('kadee.key');

            if (! $project || ! $key) {
                return;
            }

            $config = $this->app->make(FlareConfig::class);
            $config->sender = KadeeSender::class;
            $config->senderConfig = [
                'projectId' => $project,
                'secret' => $key,
                'endpoint' => config('kadee.endpoint'),
                'timeout' => config('kadee.timeout'),
            ];
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\TestCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/kadee.php' => config_path('kadee.php'),
            ], 'kadee-config');
        }
    }
}
