<?php

declare(strict_types=1);

namespace Tests\Unit\Gif;

use App\Gif\Application\DTO\StoreFavoriteGifInput;
use App\Gif\Application\DTO\GifCollection;
use App\Gif\Application\UseCase\StoreFavoriteGif\StoreFavoriteGifUseCase;
use App\Gif\Domain\Entity\FavoriteGif;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\Exception\GifNotFoundException;
use App\Gif\Domain\Port\FavoriteGifRepositoryInterface;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Domain\ValueObject\GifSearchCriteria;
use PHPUnit\Framework\TestCase;

final class StoreFavoriteGifUseCaseTest extends TestCase
{
    public function test_it_stores_a_favorite_gif(): void
    {
        $repository = new class implements FavoriteGifRepositoryInterface
        {
            public function save(FavoriteGif $favoriteGif): FavoriteGif
            {
                return new FavoriteGif(
                    id: 10,
                    gifId: $favoriteGif->gifId,
                    alias: $favoriteGif->alias,
                    userId: $favoriteGif->userId,
                );
            }
        };

        $provider = new class implements GifProviderInterface
        {
            public function search(GifSearchCriteria $criteria): GifCollection
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }

            public function findById(string $id): Gif
            {
                return new Gif(
                    id: $id,
                    title: 'Funny cat',
                    url: 'https://media.example/original.gif',
                    previewUrl: 'https://media.example/preview.gif',
                    username: null,
                    source: null,
                );
            }
        };

        $useCase = new StoreFavoriteGifUseCase($repository, $provider);

        $response = $useCase(new StoreFavoriteGifInput(
            gifId: 'gif-123',
            alias: 'Funny cat',
            userId: 1,
        ));

        self::assertSame([
            'message' => 'Favorite GIF saved successfully.',
            'data' => [
                'id' => 10,
                'gif_id' => 'gif-123',
                'alias' => 'Funny cat',
                'user_id' => 1,
            ],
        ], $response->toArray());
    }

    public function test_it_does_not_store_when_gif_does_not_exist(): void
    {
        $repository = new class implements FavoriteGifRepositoryInterface
        {
            public function save(FavoriteGif $favoriteGif): FavoriteGif
            {
                throw new \BadMethodCallException('Should not store an invalid GIF.');
            }
        };

        $provider = new class implements GifProviderInterface
        {
            public function search(GifSearchCriteria $criteria): GifCollection
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }

            public function findById(string $id): Gif
            {
                throw new GifNotFoundException($id);
            }
        };

        $this->expectException(GifNotFoundException::class);

        (new StoreFavoriteGifUseCase($repository, $provider))(new StoreFavoriteGifInput(
            gifId: 'missing-gif',
            alias: 'Missing',
            userId: 1,
        ));
    }
}
