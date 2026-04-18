<?php

declare(strict_types=1);

namespace Tests\Unit\Gif;

use App\Gif\Domain\Exception\GifProviderException;
use App\Gif\Infrastructure\Giphy\Mapper\GiphyGifMapper;
use PHPUnit\Framework\TestCase;

final class GiphyGifMapperTest extends TestCase
{
    public function test_it_maps_a_valid_search_payload(): void
    {
        $mapper = new GiphyGifMapper();

        $result = $mapper->mapSearchResult([
            'data' => [
                [
                    'id' => 'gif-123',
                    'title' => 'Dancing Cat',
                    'url' => 'https://giphy.example/fallback.gif',
                    'username' => 'tester',
                    'source' => 'https://example.com',
                    'images' => [
                        'original' => [
                            'url' => 'https://giphy.example/original.gif',
                        ],
                        'fixed_width' => [
                            'url' => 'https://giphy.example/preview.gif',
                        ],
                    ],
                ],
            ],
            'pagination' => [
                'total_count' => 200,
                'count' => 1,
                'offset' => 0,
            ],
        ]);

        self::assertSame(200, $result->totalCount);
        self::assertCount(1, $result->items);
        self::assertSame('gif-123', $result->items[0]->id);
        self::assertSame('https://giphy.example/original.gif', $result->items[0]->url);
        self::assertSame('https://giphy.example/preview.gif', $result->items[0]->previewUrl);
    }

    public function test_it_throws_when_pagination_is_missing(): void
    {
        $mapper = new GiphyGifMapper();

        $this->expectException(GifProviderException::class);
        $this->expectExceptionMessage('missing or invalid "pagination"');

        $mapper->mapSearchResult([
            'data' => [],
        ]);
    }

    public function test_it_throws_when_a_gif_has_no_url(): void
    {
        $mapper = new GiphyGifMapper();

        $this->expectException(GifProviderException::class);
        $this->expectExceptionMessage('missing URL');

        $mapper->mapGif([
            'id' => 'gif-404',
            'title' => 'Broken Gif',
            'images' => [],
        ]);
    }

    public function test_it_maps_optional_fields_with_fallbacks(): void
    {
        $mapper = new GiphyGifMapper();

        $gif = $mapper->mapGif([
            'id' => 'gif-minimal',
            'url' => 'https://giphy.example/fallback.gif',
        ]);

        self::assertSame('gif-minimal', $gif->id);
        self::assertSame('', $gif->title);
        self::assertSame('https://giphy.example/fallback.gif', $gif->url);
        self::assertSame('https://giphy.example/fallback.gif', $gif->previewUrl);
        self::assertNull($gif->username);
        self::assertNull($gif->source);
    }
}
