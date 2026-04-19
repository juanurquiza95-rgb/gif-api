<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

use App\Gif\Domain\Entity\FavoriteGif;

final class FavoriteGifData
{
    public function __construct(
        public int $id,
        public string $gifId,
        public string $alias,
        public int $userId,
    ) {
    }

    public static function fromDomain(FavoriteGif $favoriteGif): self
    {
        return new self(
            id: (int) $favoriteGif->id,
            gifId: $favoriteGif->gifId,
            alias: $favoriteGif->alias,
            userId: $favoriteGif->userId,
        );
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'gif_id' => $this->gifId,
            'alias' => $this->alias,
            'user_id' => $this->userId,
        ];
    }
}
