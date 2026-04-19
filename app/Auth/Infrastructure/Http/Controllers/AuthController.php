<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Http\Controllers;

use App\Auth\Application\DTO\LoginInput;
use App\Auth\Application\UseCase\LoginUseCase;
use App\Auth\Infrastructure\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

final class AuthController
{
    public function __construct(
        private LoginUseCase $loginUseCase,
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->loginUseCase->execute(
            new LoginInput(
                email: (string) $request->validated('email'),
                password: (string) $request->validated('password'),
            )
        );

        return response()->json($response->toArray());
    }
}
