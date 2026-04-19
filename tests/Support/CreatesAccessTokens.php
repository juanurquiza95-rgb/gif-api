<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait CreatesAccessTokens
{
    protected function authHeaderFor(User $user): array
    {
        $plainToken = 'test-token-'.$user->id.'-'.Str::random(32);

        DB::table('access_tokens')->insert([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addMinutes(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'Authorization' => 'Bearer '.$plainToken,
        ];
    }

    protected function assertRequiresAuthentication(TestResponse $response): void
    {
        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
