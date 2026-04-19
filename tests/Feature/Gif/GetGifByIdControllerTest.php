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

final class GetGifByIdControllerTest extends TestCase
{
    use CreatesAccessTokens;
    use RefreshDatabase;

    public function test_it_returns_a_gif_by_id(): void
    {
        $user = User::factory()->create();

        $this->app->bind(GifProviderInterface::class, static fn (): GifProviderInterface => new class implements GifProviderInterface
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
                    username: 'tester',
                    source: null,
                );
            }
        });

        $response = $this->getJson('/api/gifs/gif-123', $this->authHeaderFor($user));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'gif-123',
                    'title' => 'Funny cat',
                    'url' => 'https://media.example/original.gif',
                    'preview_url' => 'https://media.example/preview.gif',
                    'username' => 'tester',
                    'source' => null,
                ],
            ]);
    }

    public function test_it_requires_authentication(): void
    {
        $this->assertRequiresAuthentication(
            $this->getJson('/api/gifs/gif-123')
        );
    }
}
