<?php

declare(strict_types=1);

namespace App\Gif\Domain\ValueObject;

use InvalidArgumentException;

final class GifSearchCriteria
{
    public function __construct(
        public string $query,
        public int $limit = 25,
        public int $offset = 0,
    ) {
        $normalizedQuery = trim($this->query);

        if ($normalizedQuery === '') {
            throw new InvalidArgumentException('The search query cannot be empty.');
        }

        if ($this->limit < 1) {
            throw new InvalidArgumentException('The limit must be greater than zero.');
        }

        if ($this->offset < 0) {
            throw new InvalidArgumentException('The offset cannot be negative.');
        }
    }
}
