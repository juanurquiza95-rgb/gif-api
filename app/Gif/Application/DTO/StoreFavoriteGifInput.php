<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

final class StoreFavoriteGifInput
{
    public function __construct(
        public string $gifId,
        public string $alias,
        public int $userId,
    ) {
    }
}
