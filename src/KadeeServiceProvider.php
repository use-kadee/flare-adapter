<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter;

use Illuminate\Support\ServiceProvider;

class KadeeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kadee.php', 'kadee');

        // Only configure if both KADEE_PROJECT and KADEE_KEY are set
        $project = config('kadee.project');
        $key = config('kadee.key');

        if ($project && $key) {
            config([
                // Enable Flare if no key is set (Flare disables itself without a key)
                'flare.key' => config('flare.key') ?: $project,
                'flare.sender' => [
                    'class' => KadeeSender::class,
                    'config' => [
                        'projectId' => $project,
                        'secret' => $key,
                        'endpoint' => config('kadee.endpoint'),
                        'timeout' => config('kadee.timeout'),
                    ],
                ],
            ]);
        }
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
