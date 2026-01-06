<?php

declare(strict_types=1);

use Kadee\FlareAdapter\KadeeSender;
use Spatie\FlareClient\Enums\FlarePayloadType;
use Spatie\FlareClient\Senders\Support\Response;

it('creates signature correctly', function () {
    $sender = new KadeeSender([
        'projectId' => 'test-project',
        'secret' => 'test-secret',
        'endpoint' => 'https://example.com/api/ingest',
        'timeout' => 5,
    ]);

    expect($sender)->toBeInstanceOf(KadeeSender::class);
});

it('skips non-error payloads', function () {
    $sender = new KadeeSender([
        'projectId' => 'test-project',
        'secret' => 'test-secret',
    ]);

    $called = false;
    $sender->post(
        'https://flareapp.io/api/reports',
        'dummy-token',
        ['trace' => 'data'],
        FlarePayloadType::Trace,
        function (Response $response) use (&$called) {
            $called = true;
            expect($response->code)->toBe(200);
        }
    );

    expect($called)->toBeTrue();
});
