<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Giphy\Client;

use App\Gif\Domain\Exception\GifNotFoundException;
use App\Gif\Domain\Exception\GifProviderException;
use App\Gif\Domain\ValueObject\GifSearchCriteria;
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
    public function searchGifs(GifSearchCriteria $criteria): array
    {
        return $this->get('/v1/gifs/search', [
            'q' => $criteria->query,
            'limit' => $criteria->limit,
            'offset' => $criteria->offset,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getGifById(string $id): array
    {
        return $this->get(sprintf('/v1/gifs/%s', $id));
    }

    /**
     * @return array<string, mixed>
     */
    private function get(string $path, array $query = []): array
    {
        $this->ensureConfigured();

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
            if ($this->isGifNotFoundResponse($path, $exception)) {
                throw new GifNotFoundException(basename($path), $exception);
            }

            throw new GifProviderException(
                message: $this->providerErrorMessage($exception),
                previous: $exception,
            );
        } catch (Throwable $exception) {
            throw new GifProviderException(previous: $exception);
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw new GifProviderException('Invalid response received from Giphy.');
        }

        return $payload;
    }

    private function isGifNotFoundResponse(string $path, RequestException $exception): bool
    {
        $status = $exception->response->status();

        if ($status === 404) {
            return true;
        }

        if ($status !== 400 || !str_starts_with($path, '/v1/gifs/')) {
            return false;
        }

        $payload = $exception->response->json();

        if (!is_array($payload)) {
            return false;
        }

        return ($payload['meta']['msg'] ?? null) === 'Validation error';
    }

    private function providerErrorMessage(RequestException $exception): string
    {
        return match ($exception->response->status()) {
            401 => 'GIF provider rejected the configured API key.',
            429 => 'GIF provider rate limit exceeded.',
            default => $this->providerMessage($exception)
                ?? 'Failed to communicate with GIF provider.',
        };
    }

    private function providerMessage(RequestException $exception): ?string
    {
        $payload = $exception->response->json();

        if (!is_array($payload)) {
            return null;
        }

        $message = $payload['meta']['msg'] ?? null;

        if (!is_string($message) || trim($message) === '') {
            return null;
        }

        return sprintf('GIF provider request failed: %s.', trim($message));
    }

    private function ensureConfigured(): void
    {
        if (trim($this->baseUrl) === '') {
            throw new GifProviderException('GIF provider base URL is not configured.');
        }

        if (trim($this->apiKey) === '') {
            throw new GifProviderException('GIF provider API key is not configured.');
        }
    }
}
