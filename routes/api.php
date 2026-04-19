<?php

declare(strict_types=1);

use App\Auth\Infrastructure\Http\Controllers\AuthController;
use App\Gif\Infrastructure\Http\Controllers\GifController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('gifs')
    ->middleware('auth.token')
    ->controller(GifController::class)
    ->group(function (): void {
        Route::get('/search', 'search');
        Route::get('/{id}', 'show');
        Route::post('/favorites', 'storeFavorite');
    });
