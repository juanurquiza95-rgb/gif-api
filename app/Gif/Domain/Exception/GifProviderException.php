<?php

declare(strict_types=1);

namespace App\Gif\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;
use Throwable;

final class GifProviderException extends DomainException
{
    public function __construct(
        string $message = 'Failed to communicate with GIF provider.',
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 502, $previous);
    }
}
