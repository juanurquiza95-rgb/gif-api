<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Giphy\Mapper;

use App\Gif\Application\DTO\GifSearchResult;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\Exception\GifProviderException;
use App\Gif\Infrastructure\Giphy\DTO\GiphyGifDTO;

final class GiphyGifMapper
{
    /**
     * @param array<string, mixed> $payload
     */
    public function mapSearchResult(array $payload): GifSearchResult
    {
        $data = $payload['data'] ?? null;
        $pagination = $payload['pagination'] ?? null;

        if (!is_array($data)) {
            throw new GifProviderException('Invalid Giphy response: missing or invalid "data" field.');
        }

        if (!is_array($pagination)) {
            throw new GifProviderException('Invalid Giphy response: missing or invalid "pagination" field.');
        }

        return new GifSearchResult(
            items: array_map(fn (mixed $gif): Gif => $this->mapGif($gif), $data),
            totalCount: (int) ($pagination['total_count'] ?? 0),
            count: (int) ($pagination['count'] ?? 0),
            offset: (int) ($pagination['offset'] ?? 0),
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function mapSingleGifResult(array $payload): Gif
    {
        $data = $payload['data'] ?? null;

        if (!is_array($data)) {
            throw new GifProviderException('Invalid Giphy response: missing or invalid "data" field.');
        }

        return $this->mapGif($data);
    }

    /**
     * @param mixed $payload
     */
    public function mapGif(mixed $payload): Gif
    {
        if (!is_array($payload)) {
            throw new GifProviderException('Invalid Giphy GIF payload: expected an array.');
        }

        $dto = GiphyGifDTO::fromArray($payload);

        return new Gif(
            id: $dto->id,
            title: $dto->title ?? '',
            url: $dto->url,
            previewUrl: $dto->previewUrl ?? $dto->url,
            username: $dto->username,
            source: $dto->source,
        );
    }
}
