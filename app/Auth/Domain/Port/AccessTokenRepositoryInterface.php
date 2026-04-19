<?php

declare(strict_types=1);

namespace App\Auth\Domain\Port;

use DateTimeInterface;

interface AccessTokenRepositoryInterface
{
    public function create(int $userId, string $tokenHash, DateTimeInterface $expiresAt): void;

    public function findValidUserIdByTokenHash(string $tokenHash): ?int;
}
