<?php

declare(strict_types=1);

namespace App\Gif\Application\UseCase\SearchGifs;

use App\Gif\Application\DTO\SearchGifsInput;
use App\Gif\Application\DTO\SearchGifsResponse;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Domain\ValueObject\GifSearchCriteria;

final class SearchGifsUseCase
{
    public function __construct(
        private GifProviderInterface $gifProvider,
    ) {
    }

    public function __invoke(SearchGifsInput $input): SearchGifsResponse
    {
        $criteria = new GifSearchCriteria(
            query: $input->query,
            limit: $input->limit,
            offset: $input->offset,
        );

        $result = $this->gifProvider->search($criteria);

        return SearchGifsResponse::fromDomain($result);
    }
}
