<?php

declare(strict_types=1);

use Kadee\FlareAdapter\KadeeSender;
use Kadee\FlareAdapter\KadeeServiceProvider;

it('can be instantiated with config array', function () {
    $sender = new KadeeSender([
        'projectId' => 'test-project-id',
        'secret' => 'test-secret',
    ]);

    expect($sender)->toBeInstanceOf(KadeeSender::class);
});

it('does not configure sender when config is missing', function () {
    // Simulate no KADEE_PROJECT or KADEE_KEY set
    config(['kadee.project' => null, 'kadee.key' => null]);

    $provider = new KadeeServiceProvider(app());
    $provider->register();

    // Should not have set flare.sender
    expect(config('flare.sender'))->toBeNull();
});

it('does not configure sender when only project is set', function () {
    config(['kadee.project' => 'test-uuid', 'kadee.key' => null]);

    $provider = new KadeeServiceProvider(app());
    $provider->register();

    expect(config('flare.sender'))->toBeNull();
});

it('does not configure sender when only key is set', function () {
    config(['kadee.project' => null, 'kadee.key' => 'test-secret']);

    $provider = new KadeeServiceProvider(app());
    $provider->register();

    expect(config('flare.sender'))->toBeNull();
});

it('configures sender when both project and key are set', function () {
    config(['kadee.project' => 'test-uuid', 'kadee.key' => 'test-secret']);

    $provider = new KadeeServiceProvider(app());
    $provider->register();

    expect(config('flare.sender'))->not->toBeNull();
    expect(config('flare.sender.class'))->toBe(KadeeSender::class);
});

it('uses default endpoint and timeout when not provided', function () {
    $sender = new KadeeSender([
        'projectId' => 'test-project-id',
        'secret' => 'test-secret',
    ]);

    $reflection = new ReflectionClass($sender);

    $endpointProperty = $reflection->getProperty('endpoint');
    expect($endpointProperty->getValue($sender))->toBe('https://usekadee.com/api/ingest');

    $timeoutProperty = $reflection->getProperty('timeout');
    expect($timeoutProperty->getValue($sender))->toBe(5);
});

it('uses custom endpoint and timeout when provided', function () {
    $sender = new KadeeSender([
        'projectId' => 'test-project-id',
        'secret' => 'test-secret',
        'endpoint' => 'https://custom.endpoint/api',
        'timeout' => 10,
    ]);

    $reflection = new ReflectionClass($sender);

    $endpointProperty = $reflection->getProperty('endpoint');
    expect($endpointProperty->getValue($sender))->toBe('https://custom.endpoint/api');

    $timeoutProperty = $reflection->getProperty('timeout');
    expect($timeoutProperty->getValue($sender))->toBe(10);
});
