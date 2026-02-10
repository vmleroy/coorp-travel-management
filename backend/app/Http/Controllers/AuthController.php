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
            'role' => 'nullable|string|in:admin,user',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'role.in' => 'O papel deve ser "admin" ou "user".',
        ]);

        $result = $this->authService->register($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso!',
            'data' => [
                'user' => $result['user'],
                'token' => $result['token'],
            ]
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
        ], [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        $result = $this->authService->login($validated);

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'data' => [
                'user' => $result['user'],
                'token' => $result['token'],
            ]
        ], 200);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        $user = $this->authService->getCurrentUser($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Dados do usuário recuperados com sucesso.',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!',
        ], 200);
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        $this->authService->logoutAll($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado em todos os dispositivos com sucesso!',
        ], 200);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $token = $this->authService->refreshToken($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Token renovado com sucesso!',
            'data' => [
                'token' => $token,
            ]
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
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'role.in' => 'O papel deve ser "admin" ou "user".',
        ]);

        $user = $this->authService->createUser($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso!',
            'data' => [
                'user' => $user,
            ]
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
            'success' => true,
            'message' => 'Usuários recuperados com sucesso.',
            'data' => [
                'users' => $result['data'],
                'pagination' => [
                    'current_page' => $result['current_page'],
                    'per_page' => $result['per_page'],
                    'total' => $result['total'],
                    'last_page' => $result['last_page'],
                    'from' => $result['from'],
                    'to' => $result['to'],
                ],
            ]
        ], 200);
    }

    /**
     * Get a specific user by ID (protected route - admin only)
     */
    public function getUserById(Request $request) {
        $id = $request->route('id');
        $user = $this->authService->getUserById($id);
        return response()->json([
            'success' => true,
            'message' => 'Usuário recuperado com sucesso.',
            'data' => [
                'user' => $user,
            ]
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
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'role.in' => 'O papel deve ser "admin" ou "user".',
        ]);

        $user = $this->authService->updateUser($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }

    /**
     * Update current user (protected route - user)
     */
    public function updateMe(Request $request) {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:8|confirmed',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
        ]);

        $user = $this->authService->updateMe($request->user()->id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso!',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }
}
