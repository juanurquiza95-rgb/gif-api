<?php

declare(strict_types=1);

namespace App\Gif\Domain\Entity;

final class FavoriteGif
{
    public function __construct(
        public ?int $id,
        public string $gifId,
        public string $alias,
        public int $userId,
    ) {
    }
}
