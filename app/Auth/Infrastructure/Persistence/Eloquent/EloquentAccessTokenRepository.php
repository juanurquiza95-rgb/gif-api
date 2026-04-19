<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Persistence\Eloquent;

use App\Auth\Domain\Port\AccessTokenRepositoryInterface;
use DateTimeInterface;

final class EloquentAccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function create(int $userId, string $tokenHash, DateTimeInterface $expiresAt): void
    {
        AccessTokenModel::query()->create([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValidUserIdByTokenHash(string $tokenHash): ?int
    {
        $token = AccessTokenModel::query()
            ->where('token_hash', $tokenHash)
            ->where('expires_at', '>', now())
            ->first(['user_id']);

        if ($token === null) {
            return null;
        }

        return (int) $token->user_id;
    }
}
