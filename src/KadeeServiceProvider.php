<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter;

use Illuminate\Support\ServiceProvider;
use Spatie\FlareClient\Senders\Sender;
use Spatie\LaravelFlare\FlareConfig;

class KadeeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kadee.php', 'kadee');
    }

    public function boot(): void
    {
        // Replace the Sender singleton with KadeeSender
        // This runs after all service providers have registered,
        // so FlareServiceProvider's Sender binding will be overridden
        $project = config('kadee.project');
        $key = config('kadee.key');

        if ($project && $key) {
            // Override the Sender singleton directly
            $this->app->singleton(Sender::class, fn () => new KadeeSender([
                'projectId' => $project,
                'secret' => $key,
                'endpoint' => config('kadee.endpoint'),
                'timeout' => config('kadee.timeout'),
            ]));

            // Also update FlareConfig for consistency (used by test command)
            if ($this->app->bound(FlareConfig::class)) {
                $config = $this->app->make(FlareConfig::class);
                $config->sender = KadeeSender::class;
                $config->senderConfig = [
                    'projectId' => $project,
                    'secret' => $key,
                    'endpoint' => config('kadee.endpoint'),
                    'timeout' => config('kadee.timeout'),
                ];
            }
        }

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
