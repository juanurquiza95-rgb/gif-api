<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class FavoriteGifModel extends Model
{
    protected $table = 'favorite_gifs';

    protected $fillable = [
        'gif_id',
        'alias',
        'user_id',
    ];
}
