<?php

declare(strict_types=1);

namespace App\Gif\Domain\Port;

use App\Gif\Application\DTO\GifSearchResult;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\ValueObject\GifSearchCriteria;

interface GifProviderInterface
{
    public function search(GifSearchCriteria $criteria): GifSearchResult;

    public function findById(string $id): Gif;
}
