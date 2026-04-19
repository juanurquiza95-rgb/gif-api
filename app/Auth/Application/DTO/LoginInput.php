<?php

declare(strict_types=1);

namespace App\Auth\Application\DTO;

final class LoginInput
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
