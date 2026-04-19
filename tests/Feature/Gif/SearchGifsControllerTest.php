<?php

declare(strict_types=1);

namespace Tests\Feature\Gif;

use App\Gif\Application\DTO\GifCollection;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Domain\ValueObject\GifSearchCriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Support\CreatesAccessTokens;

final class SearchGifsControllerTest extends TestCase
{
    use CreatesAccessTokens;
    use RefreshDatabase;

    public function test_it_returns_a_gif_collection(): void
    {
        $user = User::factory()->create();

        $this->app->bind(GifProviderInterface::class, static fn (): GifProviderInterface => new class implements GifProviderInterface
        {
            public function search(GifSearchCriteria $criteria): GifCollection
            {
                return new GifCollection(
                    items: [
                        new Gif(
                            id: 'abc123',
                            title: 'Funny cat',
                            url: 'https://media.example/original.gif',
                            previewUrl: 'https://media.example/preview.gif',
                            username: 'tester',
                            source: 'https://example.com',
                        ),
                    ],
                    totalCount: 100,
                    count: 1,
                    offset: 5,
                );
            }

            public function findById(string $id): Gif
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }
        });

        $response = $this->getJson(
            '/api/gifs/search?query=cats&limit=1&offset=5',
            $this->authHeaderFor($user),
        );

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => 'abc123',
                        'title' => 'Funny cat',
                        'url' => 'https://media.example/original.gif',
                        'preview_url' => 'https://media.example/preview.gif',
                        'username' => 'tester',
                        'source' => 'https://example.com',
                    ],
                ],
                'pagination' => [
                    'total_count' => 100,
                    'count' => 1,
                    'offset' => 5,
                ],
            ]);
    }

    public function test_it_validates_the_query_parameter(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/gifs/search', $this->authHeaderFor($user));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['query']);
    }

    public function test_it_requires_authentication(): void
    {
        $this->assertRequiresAuthentication(
            $this->getJson('/api/gifs/search?query=cats')
        );
    }
}
