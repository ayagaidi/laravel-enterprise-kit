<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_writes_an_audit_event_without_password_data(): void
    {
        $admin = $this->createUserWithPermissions(['users.view', 'users.create']);
        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New User', 'email' => 'new@example.test', 'password' => 'SecretPass123!', 'password_confirmation' => 'SecretPass123!', 'locale' => 'en', 'is_active' => 1, 'role_ids' => [],
        ]);
        $response->assertRedirect('/admin/users');
        $log = AuditLog::query()->where('action', 'user.created')->firstOrFail();
        $this->assertSame('new@example.test', $log->new_values['email']);
        $this->assertArrayNotHasKey('password', $log->new_values);
    }
}
