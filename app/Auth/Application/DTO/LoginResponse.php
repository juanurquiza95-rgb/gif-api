<?php

declare(strict_types=1);

namespace App\Auth\Application\DTO;

final class LoginResponse
{
    public function __construct(
        public string $token,
        public string $expiresAt,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'expires_at' => $this->expiresAt,
        ];
    }
}
