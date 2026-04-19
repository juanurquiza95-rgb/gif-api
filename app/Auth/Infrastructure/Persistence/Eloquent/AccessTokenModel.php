<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class AccessTokenModel extends Model
{
    protected $table = 'access_tokens';

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
