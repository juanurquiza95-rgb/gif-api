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

final class StoreFavoriteGifControllerTest extends TestCase
{
    use CreatesAccessTokens;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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
                    username: null,
                    source: null,
                );
            }
        });
    }

    public function test_it_stores_a_favorite_gif(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/gifs/favorites', [
            'gif_id' => 'gif-123',
            'alias' => 'Funny cat',
            'user_id' => $user->id,
        ], $this->authHeaderFor($user));

        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Favorite GIF saved successfully.',
                'data' => [
                    'gif_id' => 'gif-123',
                    'alias' => 'Funny cat',
                    'user_id' => $user->id,
                ],
            ]);

        $this->assertDatabaseHas('favorite_gifs', [
            'gif_id' => 'gif-123',
            'alias' => 'Funny cat',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/gifs/favorites', [], $this->authHeaderFor($user));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'gif_id',
                'alias',
            ]);
    }

    public function test_it_updates_alias_when_same_user_saves_same_gif(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/gifs/favorites', [
            'gif_id' => 'gif-123',
            'alias' => 'Old alias',
            'user_id' => $user->id,
        ], $this->authHeaderFor($user))->assertCreated();

        $this->postJson('/api/gifs/favorites', [
            'gif_id' => 'gif-123',
            'alias' => 'New alias',
            'user_id' => $user->id,
        ], $this->authHeaderFor($user))->assertCreated();

        $this->assertDatabaseCount('favorite_gifs', 1);
        $this->assertDatabaseHas('favorite_gifs', [
            'gif_id' => 'gif-123',
            'alias' => 'New alias',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_rejects_mismatched_user_id(): void
    {
        $authenticatedUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->postJson('/api/gifs/favorites', [
            'gif_id' => 'gif-123',
            'alias' => 'Funny cat',
            'user_id' => $otherUser->id,
        ], $this->authHeaderFor($authenticatedUser));

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_it_requires_authentication(): void
    {
        $this->assertRequiresAuthentication(
            $this->postJson('/api/gifs/favorites', [
                'gif_id' => 'gif-123',
                'alias' => 'Funny cat',
            ])
        );
    }
}
