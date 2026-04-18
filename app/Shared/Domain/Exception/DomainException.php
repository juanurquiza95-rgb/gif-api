<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use RuntimeException;
use Throwable;

abstract class DomainException extends RuntimeException
{
    public function __construct(
        string $message,
        protected int $status = 500,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function status(): int
    {
        return $this->status;
    }
}
