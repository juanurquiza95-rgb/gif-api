<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Persistence\Eloquent;

use App\Gif\Domain\Entity\FavoriteGif;
use App\Gif\Domain\Port\FavoriteGifRepositoryInterface;

final class EloquentFavoriteGifRepository implements FavoriteGifRepositoryInterface
{
    public function save(FavoriteGif $favoriteGif): FavoriteGif
    {
        $model = FavoriteGifModel::query()->updateOrCreate(
            [
                'user_id' => $favoriteGif->userId,
                'gif_id' => $favoriteGif->gifId,
            ],
            [
                'alias' => $favoriteGif->alias,
            ],
        );

        return new FavoriteGif(
            id: (int) $model->id,
            gifId: (string) $model->gif_id,
            alias: (string) $model->alias,
            userId: (int) $model->user_id,
        );
    }
}
