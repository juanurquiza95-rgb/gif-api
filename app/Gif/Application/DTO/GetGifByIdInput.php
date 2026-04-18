<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

final class GetGifByIdInput
{
    public function __construct(
        public string $id,
    ) {
    }
}
