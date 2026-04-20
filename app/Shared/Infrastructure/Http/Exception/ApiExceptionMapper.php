<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Exception;

use App\Shared\Domain\Exception\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ApiExceptionMapper
{
    public function toResponse(Throwable $exception): JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($exception instanceof DomainException) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status());
        }

        Log::error('Unhandled exception', [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'Internal Server Error',
        ], 500);
    }
}
