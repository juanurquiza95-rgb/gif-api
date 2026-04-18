<?php

declare(strict_types=1);

namespace Tests\Unit\Gif;

use App\Gif\Application\DTO\GetGifByIdInput;
use App\Gif\Application\DTO\GifSearchResult;
use App\Gif\Application\UseCase\GetGifById\GetGifByIdUseCase;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Domain\ValueObject\GifSearchCriteria;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GetGifByIdUseCaseTest extends TestCase
{
    public function test_it_returns_a_gif_by_id(): void
    {
        $provider = new class implements GifProviderInterface
        {
            public function search(GifSearchCriteria $criteria): GifSearchResult
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
                    username: 'tester',
                    source: null,
                );
            }
        };

        $useCase = new GetGifByIdUseCase($provider);

        $response = $useCase(new GetGifByIdInput('gif-123'));

        self::assertSame([
            'id' => 'gif-123',
            'title' => 'Funny cat',
            'url' => 'https://media.example/original.gif',
            'preview_url' => 'https://media.example/preview.gif',
            'username' => 'tester',
            'source' => null,
        ], $response->toArray());
    }

    public function test_it_rejects_an_empty_id(): void
    {
        $provider = new class implements GifProviderInterface
        {
            public function search(GifSearchCriteria $criteria): GifSearchResult
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }

            public function findById(string $id): Gif
            {
                throw new \BadMethodCallException('Should not be called.');
            }
        };

        $this->expectException(InvalidArgumentException::class);

        (new GetGifByIdUseCase($provider))(new GetGifByIdInput(' '));
    }
}
