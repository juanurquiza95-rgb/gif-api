<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Middleware;

use App\Shared\Domain\Port\AuditLoggerInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AuditMiddleware
{
    private const MAX_BODY_LENGTH = 5000;

    public function __construct(
        private AuditLoggerInterface $auditLogger,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $this->auditLogger->log([
                'service' => $request->path(),
                'method' => $request->method(),
                'request_body' => $this->sanitizeRequestBody($request->all()),
                'response_body' => $this->truncate($response->getContent()),
                'status_code' => $response->getStatusCode(),
                'ip' => $request->ip(),
                'user_id' => $request->attributes->get('auth_user_id'),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Audit log could not be persisted.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    private function sanitizeRequestBody(array $body): array
    {
        if (array_key_exists('password', $body)) {
            $body['password'] = '[FILTERED]';
        }

        return $body;
    }

    private function truncate(string|false $content): ?string
    {
        if ($content === false) {
            return null;
        }

        return mb_substr($content, 0, self::MAX_BODY_LENGTH);
    }
}
