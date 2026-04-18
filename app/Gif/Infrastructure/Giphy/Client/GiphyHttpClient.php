<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Giphy\Client;

use App\Gif\Domain\Exception\GifNotFoundException;
use App\Gif\Domain\Exception\GifProviderException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\RequestException;
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
        } catch (RequestException $exception) {
            if ($exception->response->status() === 404) {
                throw new GifNotFoundException(basename($path), $exception);
            }

            throw new GifProviderException(previous: $exception);
        } catch (Throwable $exception) {
            throw new GifProviderException(previous: $exception);
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw new GifProviderException('Invalid response received from Giphy.');
        }

        return $payload;
    }
}
