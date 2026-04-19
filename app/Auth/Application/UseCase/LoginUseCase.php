<?php

declare(strict_types=1);

namespace App\Auth\Application\UseCase;

use App\Auth\Application\DTO\LoginInput;
use App\Auth\Application\DTO\LoginResponse;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Port\AccessTokenRepositoryInterface;
use App\Auth\Domain\Port\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private AccessTokenRepositoryInterface $tokens,
    ) {
    }

    public function execute(LoginInput $input): LoginResponse
    {
        $user = $this->users->findByEmail($input->email);

        if ($user === null || !Hash::check($input->password, $user['password'])) {
            throw new InvalidCredentialsException();
        }

        $plainToken = Str::random(60);
        $hashedToken = hash('sha256', $plainToken);
        $expiresAt = now()->addMinutes(30);

        $this->tokens->create(
            userId: $user['id'],
            tokenHash: $hashedToken,
            expiresAt: $expiresAt,
        );

        return new LoginResponse(
            token: $plainToken,
            expiresAt: $expiresAt->toISOString(),
        );
    }
}
