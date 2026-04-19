<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Auth\Application\DTO\LoginInput;
use App\Auth\Application\UseCase\LoginUseCase;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Port\AccessTokenRepositoryInterface;
use App\Auth\Domain\Port\UserRepositoryInterface;
use DateTimeInterface;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoginUseCaseTest extends TestCase
{
    public function test_it_creates_a_token_for_valid_credentials(): void
    {
        $users = new class implements UserRepositoryInterface
        {
            public function findByEmail(string $email): ?array
            {
                return [
                    'id' => 1,
                    'password' => Hash::make('secret-password'),
                ];
            }
        };

        $tokens = new class implements AccessTokenRepositoryInterface
        {
            public ?string $tokenHash = null;

            public function create(int $userId, string $tokenHash, DateTimeInterface $expiresAt): void
            {
                $this->tokenHash = $tokenHash;
            }

            public function findValidUserIdByTokenHash(string $tokenHash): ?int
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }
        };

        $response = (new LoginUseCase($users, $tokens))->execute(
            new LoginInput('john@example.com', 'secret-password')
        );

        self::assertSame(60, strlen($response->token));
        self::assertSame(hash('sha256', $response->token), $tokens->tokenHash);
        self::assertNotEmpty($response->expiresAt);
    }

    public function test_it_rejects_invalid_credentials(): void
    {
        $users = new class implements UserRepositoryInterface
        {
            public function findByEmail(string $email): ?array
            {
                return [
                    'id' => 1,
                    'password' => Hash::make('secret-password'),
                ];
            }
        };

        $tokens = new class implements AccessTokenRepositoryInterface
        {
            public function create(int $userId, string $tokenHash, DateTimeInterface $expiresAt): void
            {
                throw new \BadMethodCallException('Token should not be created.');
            }

            public function findValidUserIdByTokenHash(string $tokenHash): ?int
            {
                throw new \BadMethodCallException('Not needed for this test.');
            }
        };

        $this->expectException(InvalidCredentialsException::class);

        (new LoginUseCase($users, $tokens))->execute(
            new LoginInput('john@example.com', 'wrong-password')
        );
    }
}
