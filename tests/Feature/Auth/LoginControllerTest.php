<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_token_for_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'secret-password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'expires_at',
            ]);

        $token = $response->json('token');

        self::assertIsString($token);
        self::assertSame(60, strlen($token));

        $this->assertDatabaseHas('access_tokens', [
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
        ]);

        self::assertNotNull(
            DB::table('access_tokens')->where('user_id', $user->id)->value('expires_at')
        );
    }

    public function test_it_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_it_validates_required_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
                'password',
            ]);
    }
}
