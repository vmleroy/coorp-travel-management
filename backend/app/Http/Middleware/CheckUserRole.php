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
                'success' => false,
                'message' => 'Autenticação necessária. Por favor, faça login para acessar este recurso.',
                'error' => 'unauthenticated'
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
                'success' => false,
                'message' => 'Acesso negado. Você não possui permissão para executar esta ação.',
                'error' => 'forbidden',
                'required_role' => implode(', ', $roles)
            ], 403);
        }

        return $next($request);
    }
}
