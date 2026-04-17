<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        // Assuming $role is cast to Enum UserRole
        if ($role === UserRole::ADMIN || $role === UserRole::SUPERADMIN || $role?->value === 'admin' || $role?->value === 'superadmin') {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden - Restricted to administrators'], 403);
    }
}
