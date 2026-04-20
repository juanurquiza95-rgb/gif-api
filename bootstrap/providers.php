<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Auth\Infrastructure\Laravel\AuthServiceProvider::class,
    App\Gif\Infrastructure\Laravel\GifServiceProvider::class,
    App\Shared\Infrastructure\Laravel\SharedServiceProvider::class,
];
