<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Http\Middleware;

use App\Auth\Domain\Exception\UnauthenticatedException;
use App\Auth\Domain\Port\AccessTokenRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticateAccessToken
{
    public function __construct(
        private AccessTokenRepositoryInterface $tokens,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if ($plainToken === null || trim($plainToken) === '') {
            throw new UnauthenticatedException();
        }

        $userId = $this->tokens->findValidUserIdByTokenHash(
            hash('sha256', $plainToken)
        );

        if ($userId === null) {
            throw new UnauthenticatedException();
        }

        $request->attributes->set('auth_user_id', $userId);

        return $next($request);
    }
}
