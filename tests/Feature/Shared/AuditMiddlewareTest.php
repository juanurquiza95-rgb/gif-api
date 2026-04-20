<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use App\Gif\Application\DTO\GifCollection;
use App\Gif\Domain\Entity\Gif;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Domain\ValueObject\GifSearchCriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesAccessTokens;
use Tests\TestCase;

final class AuditMiddlewareTest extends TestCase
{
    use CreatesAccessTokens;
    use RefreshDatabase;

    public function test_it_logs_login_interactions(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret-password'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'secret-password',
        ])->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'service' => 'api/login',
            'method' => 'POST',
            'status_code' => 200,
            'user_id' => null,
        ]);

        $this->assertDatabaseMissing('audit_logs', [
            'request_body->password' => 'secret-password',
        ]);
    }

    public function test_it_logs_authenticated_gif_interactions(): void
    {
        $user = User::factory()->create();

        $this->app->bind(GifProviderInterface::class, static fn (): GifProviderInterface => new class implements GifProviderInterface
        {
            public function search(GifSearchCriteria $criteria): GifCollection
            {
                return new GifCollection(
                    items: [
                        new Gif(
                            id: 'gif-123',
                            title: 'Funny cat',
                            url: 'https://media.example/original.gif',
                            previewUrl: 'https://media.example/preview.gif',
                            username: null,
                            source: null,
                        ),
                    ],
                    totalCount: 1,
                    count: 1,
                    offset: 0,
                );
            }

            public function findById(string $id): Gif
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }
        });

        $this->getJson('/api/gifs/search?query=cat', $this->authHeaderFor($user))
            ->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'service' => 'api/gifs/search',
            'method' => 'GET',
            'status_code' => 200,
            'user_id' => $user->id,
        ]);
    }
}
