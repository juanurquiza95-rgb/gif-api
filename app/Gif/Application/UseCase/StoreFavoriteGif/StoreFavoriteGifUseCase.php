<?php

declare(strict_types=1);

namespace App\Gif\Application\UseCase\StoreFavoriteGif;

use App\Gif\Application\DTO\StoreFavoriteGifInput;
use App\Gif\Application\DTO\StoreFavoriteGifResponse;
use App\Gif\Domain\Entity\FavoriteGif;
use App\Gif\Domain\Port\FavoriteGifRepositoryInterface;
use App\Gif\Domain\Port\GifProviderInterface;

final class StoreFavoriteGifUseCase
{
    public function __construct(
        private FavoriteGifRepositoryInterface $favoriteGifRepository,
        private GifProviderInterface $gifProvider,
    ) {
    }

    public function __invoke(StoreFavoriteGifInput $input): StoreFavoriteGifResponse
    {
        $this->ensureGifExists($input->gifId);

        $favoriteGif = new FavoriteGif(
            id: null,
            gifId: $input->gifId,
            alias: $input->alias,
            userId: $input->userId,
        );

        return StoreFavoriteGifResponse::fromDomain(
            $this->favoriteGifRepository->save($favoriteGif)
        );
    }

    private function ensureGifExists(string $gifId): void
    {
        $this->gifProvider->findById($gifId);
    }
}
