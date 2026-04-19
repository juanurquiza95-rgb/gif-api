<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

use App\Gif\Domain\Entity\FavoriteGif;

final class StoreFavoriteGifResponse
{
    public function __construct(
        public FavoriteGifData $favoriteGif,
    ) {
    }

    public static function fromDomain(FavoriteGif $favoriteGif): self
    {
        return new self(
            favoriteGif: FavoriteGifData::fromDomain($favoriteGif),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'message' => 'Favorite GIF saved successfully.',
            'data' => $this->favoriteGif->toArray(),
        ];
    }
}
