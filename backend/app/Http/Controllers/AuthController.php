<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->register($validated);

        return response()->json([
            'user' => $result['user'],
            'token' => $result['token'],
            'message' => 'User registered successfully',
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($validated);

        return response()->json([
            'user' => $result['user'],
            'token' => $result['token'],
            'message' => 'Login successful',
        ], 200);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        $user = $this->authService->getCurrentUser($request->user());

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        $this->authService->logoutAll($request->user());

        return response()->json([
            'message' => 'Logged out from all devices',
        ], 200);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $token = $this->authService->refreshToken($request->user());

        return response()->json([
            'token' => $token,
            'message' => 'Token refreshed successfully',
        ], 200);
    }
}
