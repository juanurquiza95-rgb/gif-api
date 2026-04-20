<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class AuditLogModel extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'service',
        'method',
        'request_body',
        'response_body',
        'status_code',
        'ip',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'request_body' => 'array',
        ];
    }
}
