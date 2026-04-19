<?php

declare(strict_types=1);

namespace App\Gif\Domain\Port;

use App\Gif\Domain\Entity\FavoriteGif;

interface FavoriteGifRepositoryInterface
{
    public function save(FavoriteGif $favoriteGif): FavoriteGif;
}
