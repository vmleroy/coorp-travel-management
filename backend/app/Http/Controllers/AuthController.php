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

    /**
     * Create a new user (protected route - admin only)
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|in:admin,user',
        ]);

        $user = $this->authService->createUser($validated);

        return response()->json([
            'user' => $user,
            'message' => 'User created successfully',
        ], 201);
    }

    /**
     * Get all users (protected route - admin only)
     */
    public function getAllUsers(Request $request)
    {
        $validated = $request->validate([
            'role' => 'nullable|string|in:admin,user',
            'email' => 'nullable|string',
            'name' => 'nullable|string',
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $filters = array_filter([
            'role' => $validated['role'] ?? null,
            'email' => $validated['email'] ?? null,
            'name' => $validated['name'] ?? null,
            'search' => $validated['search'] ?? null,
        ]);

        $perPage = $validated['per_page'] ?? 15;

        $result = $this->authService->getAllUsers($filters, $perPage);

        return response()->json([
            'users' => $result['data'],
            'pagination' => [
                'current_page' => $result['current_page'],
                'per_page' => $result['per_page'],
                'total' => $result['total'],
                'last_page' => $result['last_page'],
                'from' => $result['from'],
                'to' => $result['to'],
            ],
            'message' => 'Users retrieved successfully',
        ], 200);
    }

    /**
     * Get a specific user by ID (protected route - admin only)
     */
    public function getUserById(Request $request) {
        $id = $request->route('id');
        $user = $this->authService->getUserById($id);
        return response()->json([
            'user' => $user,
            'message' => 'User retrieved successfully',
        ], 200);
    }

    /**
     * Update a user (protected route - admin only)
     */
    public function updateUser(Request $request)
    {
        $id = $request->route('id');
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string|in:admin,user',
        ]);

        $user = $this->authService->updateUser($id, $validated);

        return response()->json([
            'user' => $user,
            'message' => 'User updated successfully',
        ], 200);
    }

    /**
     * Update current user (protected route - user)
     */
    public function updateMe(Request $request) {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        $user = $this->authService->updateMe($request->user()->id, $validated);

        return response()->json([
            'user' => $user,
            'message' => 'Profile updated successfully',
        ], 200);
    }
}
