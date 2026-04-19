<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Giphy;

use App\Gif\Application\DTO\GifCollection;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Domain\ValueObject\GifSearchCriteria;
use App\Gif\Infrastructure\Giphy\Client\GiphyHttpClient;
use App\Gif\Infrastructure\Giphy\Mapper\GiphyGifMapper;

final class GiphyGifProvider implements GifProviderInterface
{
    public function __construct(
        private GiphyHttpClient $client,
        private GiphyGifMapper $mapper,
    ) {
    }

    public function search(GifSearchCriteria $criteria): GifCollection
    {
        $payload = $this->client->searchGifs($criteria);

        return $this->mapper->mapSearchResult($payload);
    }

    public function findById(string $id): Gif
    {
        $payload = $this->client->getGifById($id);

        return $this->mapper->mapSingleGifResult($payload);
    }
}
