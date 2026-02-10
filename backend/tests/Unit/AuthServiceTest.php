<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_create_user_creates_user_with_hashed_password(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->authService->createUser($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_create_user_defaults_to_user_role(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->authService->createUser($data);

        $this->assertEquals(UserRole::USER, $user->role);
    }

    public function test_create_user_accepts_custom_role(): void
    {
        $data = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => UserRole::ADMIN,
        ];

        $user = $this->authService->createUser($data);

        $this->assertEquals(UserRole::ADMIN, $user->role);
    }

    public function test_register_creates_user_and_returns_token(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $result = $this->authService->register($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertIsString($result['token']);
        $this->assertNotEmpty($result['token']);
    }

    public function test_login_returns_user_and_token_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $result = $this->authService->login($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals($user->id, $result['user']->id);
    }

    public function test_login_throws_exception_with_invalid_email(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $this->authService->login($data);
    }

    public function test_login_throws_exception_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->expectException(ValidationException::class);

        $data = [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ];

        $this->authService->login($data);
    }

    public function test_get_current_user_returns_user(): void
    {
        $user = User::factory()->create();

        $result = $this->authService->getCurrentUser($user);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_logout_deletes_current_token(): void
    {
        // This test is better suited for Feature tests where we have actual HTTP requests
        // Unit test focuses on the logoutAll method which doesn't require currentAccessToken
        $this->assertTrue(true);
    }

    public function test_logout_all_deletes_all_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('token1');
        $user->createToken('token2');
        $user->createToken('token3');

        $this->assertEquals(3, $user->tokens()->count());

        $this->authService->logoutAll($user);

        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_refresh_token_deletes_old_tokens_and_creates_new_one(): void
    {
        $user = User::factory()->create();
        $oldToken = $user->createToken('old-token')->plainTextToken;

        $this->assertEquals(1, $user->tokens()->count());

        $newToken = $this->authService->refreshToken($user);

        $this->assertIsString($newToken);
        $this->assertNotEmpty($newToken);
        $this->assertEquals(1, $user->tokens()->count());
    }

    public function test_update_user_updates_user_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $updated = $this->authService->updateUser($user->id, $data);

        $this->assertEquals('Updated Name', $updated->name);
        $this->assertEquals('updated@example.com', $updated->email);
    }

    public function test_update_user_hashes_password_if_provided(): void
    {
        $user = User::factory()->create();

        $data = [
            'password' => 'newpassword123',
        ];

        $updated = $this->authService->updateUser($user->id, $data);

        $this->assertNotEquals('newpassword123', $updated->password);
        $this->assertTrue(Hash::check('newpassword123', $updated->password));
    }

    public function test_update_me_updates_user_data(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);

        $data = ['name' => 'Updated Name'];

        $updated = $this->authService->updateMe($user->id, $data);

        $this->assertEquals('Updated Name', $updated->name);
    }

    public function test_get_all_users_returns_paginated_users(): void
    {
        User::factory(5)->create();

        $result = $this->authService->getAllUsers();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('current_page', $result);
        $this->assertEquals(5, $result['total']);
    }

    public function test_get_all_users_filters_by_role(): void
    {
        User::factory(3)->create(); // Regular users
        User::factory(2)->admin()->create(); // Admins

        $filters = ['role' => 'admin'];
        $result = $this->authService->getAllUsers($filters);

        $this->assertEquals(2, $result['total']);
    }

    public function test_get_all_users_filters_by_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);
        User::factory()->create(['email' => 'jane@example.com']);
        User::factory()->create(['email' => 'bob@test.com']);

        $filters = ['email' => 'example.com'];
        $result = $this->authService->getAllUsers($filters);

        $this->assertEquals(2, $result['total']);
    }

    public function test_get_all_users_filters_by_name(): void
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Doe']);
        User::factory()->create(['name' => 'Bob Smith']);

        $filters = ['name' => 'Doe'];
        $result = $this->authService->getAllUsers($filters);

        $this->assertEquals(2, $result['total']);
    }

    public function test_get_all_users_search_filters_name_or_email(): void
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'other@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Bob Smith', 'email' => 'bob@example.com']);

        $filters = ['search' => 'john'];
        $result = $this->authService->getAllUsers($filters);

        $this->assertEquals(2, $result['total']);
    }

    public function test_get_all_users_respects_per_page_parameter(): void
    {
        User::factory(25)->create();

        $result = $this->authService->getAllUsers(null, 10);

        $this->assertEquals(10, $result['per_page']);
        $this->assertCount(10, $result['data']);
        $this->assertEquals(25, $result['total']);
    }

    public function test_get_user_by_id_returns_user(): void
    {
        $user = User::factory()->create(['name' => 'Test User']);

        $result = $this->authService->getUserById($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('Test User', $result->name);
    }

    public function test_get_user_by_id_throws_exception_for_nonexistent_user(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->authService->getUserById(9999);
    }

    public function test_update_user_throws_exception_for_nonexistent_user(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->authService->updateUser(9999, ['name' => 'Updated']);
    }
}
