<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Laravel;

use App\Auth\Domain\Port\AccessTokenRepositoryInterface;
use App\Auth\Domain\Port\UserRepositoryInterface;
use App\Auth\Infrastructure\Persistence\Eloquent\EloquentAccessTokenRepository;
use App\Auth\Infrastructure\Persistence\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(AccessTokenRepositoryInterface::class, EloquentAccessTokenRepository::class);
    }
}
