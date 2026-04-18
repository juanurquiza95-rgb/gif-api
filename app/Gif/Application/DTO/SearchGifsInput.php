<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

final class SearchGifsInput
{
    public function __construct(
        public string $query,
        public int $limit = 25,
        public int $offset = 0,
    ) {
    }
}
