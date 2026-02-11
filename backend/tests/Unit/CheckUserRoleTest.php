<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Http\Middleware\CheckUserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CheckUserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected CheckUserRole $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new CheckUserRole();
    }

    public function test_middleware_allows_authenticated_user_without_role_restriction(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => response('OK'));

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_middleware_denies_unauthenticated_user(): void
    {
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => null);

        $response = $this->middleware->handle($request, fn() => response('OK'));

        $this->assertEquals(401, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertEquals('Autenticação necessária. Por favor, faça login para acessar este recurso.', $json['message']);
    }

    public function test_middleware_allows_admin_user_for_admin_role(): void
    {
        $admin = User::factory()->admin()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $admin);

        $response = $this->middleware->handle($request, fn() => response('OK'), 'admin');

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_middleware_denies_regular_user_for_admin_role(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => response('OK'), 'admin');

        $this->assertEquals(403, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertEquals('Acesso negado. Você não possui permissão para executar esta ação.', $json['message']);
    }

    public function test_middleware_allows_regular_user_for_user_role(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => response('OK'), 'user');

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_middleware_denies_admin_for_user_only_role(): void
    {
        $admin = User::factory()->admin()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $admin);

        $response = $this->middleware->handle($request, fn() => response('OK'), 'user');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_middleware_allows_user_with_multiple_allowed_roles(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => response('OK'), 'admin', 'user');

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_middleware_allows_admin_with_multiple_allowed_roles(): void
    {
        $admin = User::factory()->admin()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $admin);

        $response = $this->middleware->handle($request, fn() => response('OK'), 'admin', 'user');

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_middleware_handles_invalid_role_gracefully(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => response('OK'), 'invalid-role');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_middleware_passes_request_through_next_closure(): void
    {
        $admin = User::factory()->admin()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $admin);

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('Next called');
        };

        $response = $this->middleware->handle($request, $next, 'admin');

        $this->assertTrue($nextCalled);
        $this->assertEquals('Next called', $response->getContent());
    }

    public function test_middleware_does_not_call_next_for_unauthorized_user(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('Next called');
        };

        $response = $this->middleware->handle($request, $next, 'admin');

        $this->assertFalse($nextCalled);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
