<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // If no roles specified, just check authentication
        if (empty($roles)) {
            return $next($request);
        }

        // Convert string roles to UserRole enums
        $allowedRoles = array_map(function ($role) {
            return match ($role) {
                'admin' => UserRole::ADMIN,
                'user' => UserRole::USER,
                default => null,
            };
        }, $roles);

        // Check if user has one of the allowed roles
        if (!in_array($request->user()->role, $allowedRoles, true)) {
            return response()->json([
                'message' => 'Forbidden. You do not have the required role.',
            ], 403);
        }

        return $next($request);
    }
}
