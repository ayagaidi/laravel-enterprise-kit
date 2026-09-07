<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_middleware_blocks_unauthorized_user(): void
    {
        $user = $this->createUserWithPermissions(['dashboard.view']);
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_permission_middleware_allows_authorized_user(): void
    {
        $user = $this->createUserWithPermissions(['users.view']);
        $this->actingAs($user)->get('/admin/users')->assertOk();
    }
}
