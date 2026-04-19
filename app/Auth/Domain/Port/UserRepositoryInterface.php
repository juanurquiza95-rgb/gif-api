<?php

declare(strict_types=1);

namespace App\Auth\Domain\Port;

interface UserRepositoryInterface
{
    /**
     * @return array{id: int, password: string}|null
     */
    public function findByEmail(string $email): ?array;
}
