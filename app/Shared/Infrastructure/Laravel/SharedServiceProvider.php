<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel;

use App\Shared\Domain\Port\AuditLoggerInterface;
use App\Shared\Infrastructure\Persistence\Eloquent\EloquentAuditLogger;
use Illuminate\Support\ServiceProvider;

final class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuditLoggerInterface::class, EloquentAuditLogger::class);
    }
}
