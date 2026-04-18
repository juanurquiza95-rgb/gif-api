<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

use App\Gif\Domain\Entity\Gif;

final class GifCollection
{
    /**
     * @param array<int, Gif> $items
     */
    public function __construct(
        public array $items,
        public int $totalCount,
        public int $count,
        public int $offset,
    ) {
    }
}
