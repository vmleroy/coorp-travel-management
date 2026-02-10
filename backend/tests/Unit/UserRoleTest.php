<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_user_role_has_admin_case(): void
    {
        $this->assertEquals('admin', UserRole::ADMIN->value);
    }

    public function test_user_role_has_user_case(): void
    {
        $this->assertEquals('user', UserRole::USER->value);
    }

    public function test_user_role_can_be_created_from_string(): void
    {
        $adminRole = UserRole::from('admin');
        $userRole = UserRole::from('user');

        $this->assertInstanceOf(UserRole::class, $adminRole);
        $this->assertInstanceOf(UserRole::class, $userRole);
        $this->assertEquals(UserRole::ADMIN, $adminRole);
        $this->assertEquals(UserRole::USER, $userRole);
    }

    public function test_user_role_try_from_returns_null_for_invalid_value(): void
    {
        $invalidRole = UserRole::tryFrom('invalid');

        $this->assertNull($invalidRole);
    }

    public function test_user_role_can_compare_instances(): void
    {
        $admin1 = UserRole::ADMIN;
        $admin2 = UserRole::from('admin');
        $user = UserRole::USER;

        $this->assertTrue($admin1 === $admin2);
        $this->assertFalse($admin1 === $user);
    }

    public function test_user_role_has_exactly_two_cases(): void
    {
        $cases = UserRole::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(UserRole::ADMIN, $cases);
        $this->assertContains(UserRole::USER, $cases);
    }
}
