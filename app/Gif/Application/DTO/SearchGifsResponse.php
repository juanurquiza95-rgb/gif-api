<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

final class SearchGifsResponse
{
    /**
     * @param array<int, GifData> $items
     */
    public function __construct(
        public array $items,
        public int $totalCount,
        public int $count,
        public int $offset,
    ) {
    }

    public static function fromDomain(GifCollection $result): self
    {
        return new self(
            items: array_map(
                static fn ($gif): GifData => GifData::fromDomain($gif),
                $result->items,
            ),
            totalCount: $result->totalCount,
            count: $result->count,
            offset: $result->offset,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                static fn (GifData $gif): array => $gif->toArray(),
                $this->items,
            ),
            'pagination' => [
                'total_count' => $this->totalCount,
                'count' => $this->count,
                'offset' => $this->offset,
            ],
        ];
    }
}
