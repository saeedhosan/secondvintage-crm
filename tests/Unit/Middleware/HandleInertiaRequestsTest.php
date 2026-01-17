<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;
use Tighten\Ziggy\Ziggy;

final class HandleInertiaRequestsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_version_returns_string_or_null(): void
    {
        $middleware = new HandleInertiaRequests();
        $request = Request::create('/');

        $version = $middleware->version($request);

        $this->assertTrue(
            is_null($version) || is_string($version),
            'Version should return null or string'
        );
    }

    public function test_share_includes_all_flash_message_types(): void
    {
        $flashData = [
            'data' => 'some data',
            'info' => 'info message',
            'error' => 'error message',
            'status' => 'status message',
            'warning' => 'warning message',
            'success' => 'success message',
            'message' => 'message content',
        ];

        $request = Request::create('/');
        $request->setLaravelSession(app('session.store'));

        foreach ($flashData as $key => $value) {
            $request->session()->flash($key, $value);
        }

        $middleware = new HandleInertiaRequests();
        $shared = $middleware->share($request);

        foreach ($flashData as $key => $value) {
            $this->assertEquals($value, $shared['flash'][$key]());
        }
    }

    public function test_share_includes_authenticated_user_data(): void
    {
        $userMock = Mockery::mock();
        $userMock->shouldReceive('getRoleNames')->andReturn(['admin', 'editor']);
        $userMock->shouldReceive('getAllPermissions')->andReturn(collect([
            (object) ['name' => 'edit articles'],
            (object) ['name' => 'delete posts'],
        ]));

        $request = Request::create('/');
        $request->setUserResolver(fn () => $userMock);
        $request->setLaravelSession(app('session.store'));

        $middleware = new HandleInertiaRequests();
        $shared = $middleware->share($request);

        $auth = $shared['auth']();

        $this->assertEquals($userMock, $auth['user']);
        $this->assertEquals(['admin', 'editor'], $auth['roles']);
        $this->assertEquals(['edit articles', 'delete posts'], $auth['permissions']);
    }

    public function test_share_includes_empty_auth_data_for_guest(): void
    {
        $request = Request::create('/');
        $request->setLaravelSession(app('session.store'));

        $middleware = new HandleInertiaRequests();
        $shared = $middleware->share($request);

        $auth = $shared['auth']();

        $this->assertNull($auth['user']);
        $this->assertEmpty($auth['roles']);
        $this->assertEmpty($auth['permissions']);
    }

    public function test_share_includes_ziggy_data(): void
    {
        $request = Request::create('https://example.com/test');
        $request->setLaravelSession(app('session.store'));

        $ziggyMock = Mockery::mock(Ziggy::class);
        $ziggyMock->shouldReceive('toArray')->andReturn([
            'namedRoutes' => [
                'home' => '/',
                'dashboard' => '/dashboard',
            ],
            'baseUrl' => 'https://example.com',
        ]);
        app()->instance(Ziggy::class, $ziggyMock);

        $middleware = new HandleInertiaRequests();
        $shared = $middleware->share($request);

        $ziggy = $shared['ziggy']();

        $this->assertArrayHasKey('location', $ziggy);
        $this->assertEquals('https://example.com/test', $ziggy['location']);
        $this->assertArrayHasKey('namedRoutes', $ziggy);
        $this->assertArrayHasKey('baseUrl', $ziggy);
        $this->assertEquals('https://example.com', $ziggy['baseUrl']);
    }

    public function test_share_includes_default_shared_data(): void
    {
        $request = Request::create('/');
        $request->setLaravelSession(app('session.store'));

        $middleware = new HandleInertiaRequests();
        $shared = $middleware->share($request);

        $this->assertArrayHasKey('flash', $shared);
        $this->assertArrayHasKey('auth', $shared);
        $this->assertArrayHasKey('ziggy', $shared);

        $flash = $shared['flash']();
        $auth = $shared['auth']();

        $this->assertIsArray($flash);
        $this->assertIsArray($auth);
        $this->assertArrayHasKey('user', $auth);
        $this->assertArrayHasKey('roles', $auth);
        $this->assertArrayHasKey('permissions', $auth);
    }
}
