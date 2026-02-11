<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'pass',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                ],
            ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_get_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email']
                ]
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Verify token exists
        $this->assertEquals(1, $user->tokens()->count());

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout realizado com sucesso!'
            ]);

        // Verify token was deleted from database
        $user->refresh();
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_user_can_logout_from_all_devices(): void
    {
        $user = User::factory()->create();
        $token1 = $user->createToken('token1')->plainTextToken;
        $token2 = $user->createToken('token2')->plainTextToken;

        // Verify both tokens work initially
        $this->withHeader('Authorization', "Bearer $token1")
            ->getJson('/api/auth/me')
            ->assertStatus(200);

        $this->withHeader('Authorization', "Bearer $token2")
            ->getJson('/api/auth/me')
            ->assertStatus(200);

        // Logout from all devices
        $response = $this->withHeader('Authorization', "Bearer $token1")
            ->postJson('/api/auth/logout-all');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout realizado em todos os dispositivos com sucesso!'
            ]);

        // Verify both tokens are deleted from database
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_user_can_refresh_token(): void
    {
        $user = User::factory()->create();
        $oldToken = $user->createToken('auth_token')->plainTextToken;

        // Verify old token count
        $this->assertEquals(1, $user->tokens()->count());

        $response = $this->withHeader('Authorization', "Bearer $oldToken")
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['token']
            ]);

        $newToken = $response->json('data.token');

        // Verify new token was created and only one token exists
        $user->refresh();
        $this->assertEquals(1, $user->tokens()->count());

        // New token should work
        $this->withHeader('Authorization', "Bearer $newToken")
            ->getJson('/api/auth/me')
            ->assertStatus(200);
    }

    public function test_protected_routes_require_authentication(): void
    {
        $this->getJson('/api/auth/me')->assertStatus(401);
        $this->postJson('/api/auth/logout')->assertStatus(401);
    }

    public function test_user_registered_has_default_user_role(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'role@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'role@example.com')->first();
        $this->assertEquals(UserRole::USER, $user->role);
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/create-user', [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'role' => 'user',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_admin_can_create_admin_user(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/create-user', [
                'name' => 'New Admin',
                'email' => 'newadmin@example.com',
                'password' => 'password123',
                'role' => 'admin',
            ]);

        $response->assertStatus(201);

        $user = User::where('email', 'newadmin@example.com')->first();
        $this->assertEquals(UserRole::ADMIN, $user->role);
    }

    public function test_regular_user_cannot_create_users(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/create-user', [
                'name' => 'Unauthorized User',
                'email' => 'unauthorized@example.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Acesso negado. Você não possui permissão para executar esta ação.',
                'error' => 'forbidden',
                'required_role' => 'admin'
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'unauthorized@example.com',
        ]);
    }

    public function test_admin_can_get_all_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory(5)->create();
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'users',
                    'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
                ],
            ]);
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory(3)->create(); // Regular users
        User::factory(2)->admin()->create(); // Admin users
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/users?role=admin');

        $response->assertStatus(200);
        $users = $response->json('data.users');

        // Should have 3 admins total (including the one making the request)
        $this->assertCount(3, $users);
        foreach ($users as $user) {
            $this->assertEquals('admin', $user['role']);
        }
    }

    public function test_admin_can_search_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/users?search=john');

        $response->assertStatus(200);
        $users = $response->json('data.users');

        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users[0]['name']);
    }

    public function test_admin_can_paginate_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory(25)->create();
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/users?per_page=10');

        $response->assertStatus(200);
        $pagination = $response->json('data.pagination');

        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(26, $pagination['total']); // 25 + 1 admin
    }

    public function test_regular_user_cannot_get_all_users(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_get_user_by_id(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create(['name' => 'Target User']);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/auth/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Usuário recuperado com sucesso.',
                'data' => [
                    'user' => [
                        'id' => $targetUser->id,
                        'name' => 'Target User',
                    ],
                ],
            ]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create(['name' => 'Old Name']);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/auth/users/{$targetUser->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200);
        $targetUser->refresh();
        $this->assertEquals('Updated Name', $targetUser->name);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create();
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/auth/users/{$targetUser->id}", [
                'role' => 'admin',
            ]);

        $response->assertStatus(200);
        $targetUser->refresh();
        $this->assertEquals(UserRole::ADMIN, $targetUser->role);
    }

    public function test_user_can_update_own_profile(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/auth/me', [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
    }

    public function test_user_cannot_update_own_role(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson('/api/auth/me', [
                'role' => 'admin',
            ]);

        $response->assertStatus(200);
        $user->refresh();
        // Role should remain USER
        $this->assertEquals(UserRole::USER, $user->role);
    }

    public function test_create_user_validates_role(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/create-user', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'role' => 'invalid-role',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }
}

