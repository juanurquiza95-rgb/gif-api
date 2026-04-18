<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Giphy\DTO;

use App\Gif\Domain\Exception\GifProviderException;

final class GiphyGifDTO
{
    public function __construct(
        public string $id,
        public ?string $title,
        public string $url,
        public ?string $previewUrl,
        public ?string $username,
        public ?string $source,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = self::stringOrNull($data['id'] ?? null)
            ?? throw new GifProviderException('Invalid Giphy GIF payload: missing id.');

        $url = self::stringOrNull($data['images']['original']['url'] ?? null)
            ?? self::stringOrNull($data['url'] ?? null)
            ?? throw new GifProviderException(sprintf('Invalid Giphy GIF payload: missing URL for gif "%s".', $id));

        return new self(
            id: $id,
            title: self::stringOrNull($data['title'] ?? null),
            url: $url,
            previewUrl: self::stringOrNull($data['images']['fixed_width']['url'] ?? null),
            username: self::stringOrNull($data['username'] ?? null),
            source: self::stringOrNull($data['source'] ?? null),
        );
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        return $value;
    }
}
