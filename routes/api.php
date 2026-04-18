<?php

declare(strict_types=1);

use App\Gif\Infrastructure\Http\Controllers\GifController;
use Illuminate\Support\Facades\Route;

Route::prefix('gifs')
    ->controller(GifController::class)
    ->group(function (): void {
        Route::get('/search', 'search');
        Route::get('/{id}', 'show');
        Route::post('/favorites', 'storeFavorite');
    });
