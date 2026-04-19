<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class UnauthenticatedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Unauthenticated.', 401);
    }
}
