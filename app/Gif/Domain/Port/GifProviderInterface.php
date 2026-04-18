<?php

declare(strict_types=1);

namespace App\Gif\Domain\Port;

use App\Gif\Application\DTO\GifCollection;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\ValueObject\GifSearchCriteria;

interface GifProviderInterface
{
    public function search(GifSearchCriteria $criteria): GifCollection;

    public function findById(string $id): Gif;
}
