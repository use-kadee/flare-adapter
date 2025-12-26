<?php

declare(strict_types=1);

namespace Kadee\FlareAdapter;

use Closure;
use CurlHandle;
use Spatie\FlareClient\Enums\FlarePayloadType;
use Spatie\FlareClient\Senders\Sender;
use Spatie\FlareClient\Senders\Support\Response;

class KadeeSender implements Sender
{
    public function __construct(
        private readonly string $projectId,
        private readonly string $secret,
        private readonly string $endpoint = 'https://usekadee.com/api/ingest',
        private readonly int $timeout = 5,
    ) {}

    public function post(
        string $endpoint,
        string $apiToken,
        array $payload,
        FlarePayloadType $type,
        Closure $callback
    ): void {
        // Only send error reports, skip traces
        if ($type !== FlarePayloadType::Error && $type !== FlarePayloadType::TestError) {
            $callback(new Response(200, []));

            return;
        }

        $body = json_encode($payload);

        if ($body === false) {
            $callback(new Response(400, ['error' => 'Failed to encode payload']));

            return;
        }

        $signature = hash_hmac('sha256', $body, $this->secret);

        try {
            $ch = $this->createCurlHandle($body, $signature);
            $response = curl_exec($ch);
            $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response === false || $error !== '') {
                $callback(new Response(500, ['error' => $error ?: 'Request failed']));

                return;
            }

            $responseData = json_decode((string) $response, true);

            $callback(new Response($statusCode, is_array($responseData) ? $responseData : []));
        } catch (\Throwable) {
            // Silently fail - never break the app due to error reporting
            $callback(new Response(500, []));
        }
    }

    private function createCurlHandle(string $body, string $signature): CurlHandle
    {
        $url = rtrim($this->endpoint, '/') . '/' . $this->projectId;

        $ch = curl_init($url);

        if ($ch === false) {
            throw new \RuntimeException('Failed to initialize cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-Signature: ' . $signature,
            ],
        ]);

        return $ch;
    }
}
