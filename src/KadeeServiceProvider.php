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
            $this->publishes([
                __DIR__ . '/../config/kadee.php' => config_path('kadee.php'),
            ], 'kadee-config');
        }
    }
}
