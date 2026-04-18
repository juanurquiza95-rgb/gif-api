<?php

declare(strict_types=1);

namespace App\Gif\Domain\Entity;

final class Gif
{
    public function __construct(
        public string $id,
        public string $title,
        public string $url,
        public string $previewUrl,
        public ?string $username,
        public ?string $source,
    ) {
    }
}
