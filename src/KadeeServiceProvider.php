<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter;

use Illuminate\Support\ServiceProvider;
use Spatie\FlareClient\Disabled\DisabledFlare;
use Spatie\FlareClient\Flare;
use Spatie\FlareClient\Recorders\ApplicationRecorder\ApplicationRecorder;
use Spatie\FlareClient\Tracer;
use Spatie\LaravelFlare\FlareConfig;
use Spatie\LaravelFlare\FlareServiceProvider;

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
        $project = config('kadee.project');
        $key = config('kadee.key');

        // If Flare was disabled (loaded before us), re-enable it
        if ($project && $key) {
            $flare = $this->app->make(Flare::class);
            if ($flare instanceof DisabledFlare) {
                // Force unbind the disabled classes
                $this->app->offsetUnset(Flare::class);
                $this->app->offsetUnset(FlareConfig::class);
                $this->app->offsetUnset(Tracer::class);
                $this->app->offsetUnset(ApplicationRecorder::class);

                // Re-register FlareServiceProvider to pick up our config
                $provider = new FlareServiceProvider($this->app);
                $provider->register();
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
