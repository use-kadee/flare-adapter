<?php

declare(strict_types=1);

use Kadee\FlareAdapter\KadeeServiceProvider;

uses(Orchestra\Testbench\TestCase::class)
    ->beforeEach(function () {
        // Reset flare.sender config before each test
        config(['flare.sender' => null]);
    })
    ->in(__DIR__);

function getPackageProviders($app): array
{
    return [
        KadeeServiceProvider::class,
    ];
}
