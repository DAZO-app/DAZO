<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ConfigService;

class EnsureValidApiKey
{
    public function __construct(private ConfigService $configService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = $this->configService->get('public_api_key');

        if (empty($expectedKey)) {
            return response()->json(['message' => 'API is not configured.'], 503);
        }

        $providedKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$providedKey || !hash_equals($expectedKey, $providedKey)) {
            return response()->json(['message' => 'Unauthorized. Invalid API Key.'], 401);
        }

        return $next($request);
    }
}
