<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter;

use Illuminate\Support\ServiceProvider;

class KadeeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kadee.php', 'kadee');

        // Only configure if KADEE_KEY is set
        if ($key = config('kadee.key')) {
            config([
                'flare.sender' => [
                    'class' => KadeeSender::class,
                    'config' => [
                        'projectId' => $key,
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
