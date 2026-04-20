<?php

declare(strict_types=1);

namespace App\Shared\Domain\Port;

interface AuditLoggerInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function log(array $data): void;
}
