<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AppLog;

class LogRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log mutating requests that modify state (POST, PUT, PATCH, DELETE)
        if ($request->isMethodSafe()) {
            return $response;
        }

        // Avoid logging trivial actions (like marking notifications as read) if desired
        if ($request->is('api/*/notifications*')) {
            return $response;
        }

        try {
            AppLog::create([
                'user_id' => $request->user()?->id,
                'action' => $request->method() . ' ' . $request->path(),
                'payload' => $this->filterPayload($request->except(['password', 'password_confirmation', 'token'])),
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail log so it doesn't break the response
        }

        return $response;
    }

    private function filterPayload(array $payload): array
    {
        // Simple sanitization or limiting rules could go here
        return $payload;
    }
}
