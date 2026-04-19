<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Persistence\Eloquent;

use App\Auth\Domain\Port\UserRepositoryInterface;
use App\Models\User;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?array
    {
        $user = User::query()
            ->where('email', $email)
            ->first(['id', 'password']);

        if ($user === null) {
            return null;
        }

        return [
            'id' => (int) $user->id,
            'password' => (string) $user->password,
        ];
    }
}
