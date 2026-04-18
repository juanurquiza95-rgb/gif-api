<?php

declare(strict_types=1);

namespace App\Gif\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;
use Throwable;

final class GifNotFoundException extends DomainException
{
    public function __construct(string $id, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('GIF %s not found.', $id), 404, $previous);
    }
}
