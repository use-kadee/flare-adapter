<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter\Commands;

use Exception;
use Illuminate\Console\Command;
use Kadee\FlareAdapter\KadeeSender;
use Spatie\FlareClient\Enums\FlarePayloadType;
use Spatie\FlareClient\Senders\Support\Response;

class TestCommand extends Command
{
    protected $signature = 'kadee:test';

    protected $description = 'Send a test error to Kadee';

    public function handle(): int
    {
        $project = config('kadee.project');
        $key = config('kadee.key');
        $endpoint = config('kadee.endpoint');

        if (! $project || ! $key) {
            $this->error('Kadee is not configured. Please set KADEE_PROJECT and KADEE_KEY in your .env file.');
            return self::FAILURE;
        }

        $this->info('Kadee Configuration:');
        $this->line("  Project: {$project}");
        $this->line("  Endpoint: {$endpoint}");
        $this->newLine();

        $this->info('Sending test error to Kadee...');

        $sender = new KadeeSender([
            'projectId' => $project,
            'secret' => $key,
            'endpoint' => $endpoint,
            'timeout' => config('kadee.timeout', 5),
        ]);

        $testException = new Exception('This is a test error from Kadee adapter - ' . now()->toDateTimeString());

        $payload = $this->buildTestPayload($testException);

        $success = false;
        $responseCode = 0;
        $responseBody = [];

        $sender->post(
            'https://api.flareapp.io/api/reports', // ignored by KadeeSender
            'dummy-token', // ignored by KadeeSender
            $payload,
            FlarePayloadType::TestError,
            function (Response $response) use (&$success, &$responseCode, &$responseBody) {
                $responseCode = $response->code;
                $responseBody = $response->body;
                $success = $response->code >= 200 && $response->code < 300;
            }
        );

        if ($success) {
            $this->newLine();
            $this->info('✓ Test error sent successfully!');
            $this->line('  Check your Kadee dashboard to see the error.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->error("✗ Failed to send test error (HTTP {$responseCode})");
        if (! empty($responseBody)) {
            $this->line('  Response: ' . json_encode($responseBody));
        }

        return self::FAILURE;
    }

    private function buildTestPayload(Exception $exception): array
    {
        return [
            'exceptionClass' => get_class($exception),
            'message' => $exception->getMessage(),
            'seenAtUnixNano' => (int) (microtime(true) * 1_000_000_000),
            'stacktrace' => [
                [
                    'file' => $exception->getFile(),
                    'lineNumber' => $exception->getLine(),
                    'method' => 'handle',
                    'class' => self::class,
                    'codeSnippet' => [],
                    'arguments' => [],
                    'isApplicationFrame' => true,
                ],
            ],
            'attributes' => [
                'laravel.version' => app()->version(),
                'php.version' => PHP_VERSION,
            ],
            'events' => [],
            'trackingUuid' => null,
            'applicationPath' => base_path(),
        ];
    }
}
