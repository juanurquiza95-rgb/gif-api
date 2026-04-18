<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Giphy\Client;

use App\Gif\Domain\Exception\GifProviderException;
use Illuminate\Http\Client\Factory;
use Throwable;

final class GiphyHttpClient
{
    public function __construct(
        private Factory $http,
        private string $baseUrl,
        private string $apiKey,
        private int $timeoutSeconds,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        try {
            $response = $this->http
                ->baseUrl($this->baseUrl)
                ->acceptJson()
                ->timeout($this->timeoutSeconds)
                ->get($path, [
                    'api_key' => $this->apiKey,
                    ...$query,
                ])
                ->throw();
        } catch (Throwable $exception) {
            throw new GifProviderException('Failed to communicate with Giphy.', 0, $exception);
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw new GifProviderException('Invalid response received from Giphy.');
        }

        return $payload;
    }
}
