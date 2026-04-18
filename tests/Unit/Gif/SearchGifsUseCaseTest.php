<?php

declare(strict_types=1);

namespace Tests\Unit\Gif;

use App\Gif\Application\DTO\GifCollection;
use App\Gif\Application\DTO\SearchGifsInput;
use App\Gif\Application\UseCase\SearchGifs\SearchGifsUseCase;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Domain\ValueObject\GifSearchCriteria;
use PHPUnit\Framework\TestCase;

final class SearchGifsUseCaseTest extends TestCase
{
    public function test_it_maps_the_domain_result_into_an_application_response(): void
    {
        $provider = new class implements GifProviderInterface
        {
            public function search(GifSearchCriteria $criteria): GifCollection
            {
                return new GifCollection(
                    items: [
                        new Gif(
                            id: 'gif-1',
                            title: 'Party parrot',
                            url: 'https://media.example/gif-1.gif',
                            previewUrl: 'https://media.example/gif-1-preview.gif',
                            username: null,
                            source: null,
                        ),
                    ],
                    totalCount: 42,
                    count: 1,
                    offset: 0,
                );
            }

            public function findById(string $id): Gif
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }
        };

        $useCase = new SearchGifsUseCase($provider);

        $response = $useCase(new SearchGifsInput('party'));

        self::assertSame([
            'data' => [
                [
                    'id' => 'gif-1',
                    'title' => 'Party parrot',
                    'url' => 'https://media.example/gif-1.gif',
                    'preview_url' => 'https://media.example/gif-1-preview.gif',
                    'username' => null,
                    'source' => null,
                ],
            ],
            'pagination' => [
                'total_count' => 42,
                'count' => 1,
                'offset' => 0,
            ],
        ], $response->toArray());
    }
}
