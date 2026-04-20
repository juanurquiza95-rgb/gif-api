<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Eloquent;

use App\Shared\Domain\Port\AuditLoggerInterface;

final class EloquentAuditLogger implements AuditLoggerInterface
{
    public function log(array $data): void
    {
        AuditLogModel::query()->create($data);
    }
}
