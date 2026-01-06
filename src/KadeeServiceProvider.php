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

        // Extend FlareConfig to use Kadee sender
        $this->app->extend(FlareConfig::class, function (FlareConfig $config) {
            $project = config('kadee.project');
            $key = config('kadee.key');

            if (! $project || ! $key) {
                return $config;
            }

            $config->sender = KadeeSender::class;
            $config->senderConfig = [
                'projectId' => $project,
                'secret' => $key,
                'endpoint' => config('kadee.endpoint'),
                'timeout' => config('kadee.timeout'),
            ];

            return $config;
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
