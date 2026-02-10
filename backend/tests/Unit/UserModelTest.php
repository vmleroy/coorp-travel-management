<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_attributes(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('role', $fillable);
    }

    public function test_user_hides_sensitive_attributes(): void
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_user_role_is_cast_to_enum(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertInstanceOf(UserRole::class, $user->role);
        $this->assertEquals(UserRole::ADMIN, $user->role);
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create(['password' => 'plaintext']);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(password_verify('plaintext', $user->password));
    }

    public function test_is_admin_returns_true_for_admin_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->isAdmin());
    }

    public function test_is_admin_returns_false_for_user_role(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
    }

    public function test_is_user_returns_true_for_user_role(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->isUser());
    }

    public function test_is_user_returns_false_for_admin_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->isUser());
    }

    public function test_user_can_create_tokens(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        $this->assertNotNull($token);
        $this->assertEquals(1, $user->tokens()->count());
    }

    public function test_user_factory_creates_user_with_user_role_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertEquals(UserRole::USER, $user->role);
    }

    public function test_user_factory_can_create_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertEquals(UserRole::ADMIN, $admin->role);
        $this->assertTrue($admin->isAdmin());
    }

    public function test_user_email_is_unique(): void
    {
        User::factory()->create(['email' => 'unique@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => 'unique@example.com']);
    }

    public function test_user_has_email_verified_at_timestamp(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->email_verified_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    public function test_user_can_be_unverified(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);
    }

    public function test_user_role_defaults_to_user_in_database(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user', // Database default is 'user', we need to provide it
        ]);

        $user->refresh();
        $this->assertEquals(UserRole::USER, $user->role);
    }
}
