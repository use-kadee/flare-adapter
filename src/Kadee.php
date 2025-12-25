<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter;

use Spatie\FlareClient\Flare;
use Spatie\FlareClient\FlareConfig;

class Kadee
{
    /**
     * Create a Flare instance configured to send errors to Kadee.
     *
     * Usage:
     *   Kadee::make('your-project-id')->registerFlareHandlers();
     */
    public static function make(
        string $projectId,
        string $endpoint = 'https://kadee.io/api/ingest'
    ): Flare {
        $config = FlareConfig::make('kadee')
            ->sendUsing(KadeeSender::class, [
                'projectId' => $projectId,
                'endpoint' => $endpoint,
            ])
            ->useDefaults();

        return Flare::make($config);
    }
}
