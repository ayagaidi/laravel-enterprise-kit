<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivilegedRoleProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_administrator_cannot_assign_super_admin_role(): void
    {
        $actor = $this->createUserWithPermissions(['users.view', 'users.create']);
        $superAdminRole = Role::query()->create([
            'name' => 'Super Administrator',
            'slug' => 'super-admin',
            'description' => 'Privileged role',
        ]);

        $this->actingAs($actor)->post('/admin/users', [
            'name' => 'Escalated User',
            'email' => 'escalated@example.test',
            'password' => 'SecretPass123!',
            'password_confirmation' => 'SecretPass123!',
            'locale' => 'en',
            'is_active' => 1,
            'role_ids' => [$superAdminRole->id],
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'escalated@example.test']);
    }

    public function test_regular_administrator_cannot_modify_super_admin_account(): void
    {
        $actor = $this->createUserWithPermissions(['users.view', 'users.update']);
        $superAdminRole = Role::query()->create([
            'name' => 'Super Administrator',
            'slug' => 'super-admin',
            'description' => 'Privileged role',
        ]);
        $target = User::query()->create([
            'name' => 'Protected Admin',
            'email' => 'protected@example.test',
            'password' => 'password1234',
            'is_active' => true,
            'locale' => 'en',
        ]);
        $target->roles()->sync([$superAdminRole->id]);

        $this->actingAs($actor)->put("/admin/users/{$target->id}", [
            'name' => 'Changed Name',
            'email' => $target->email,
            'locale' => 'en',
            'is_active' => 1,
            'role_ids' => [],
        ])->assertForbidden();

        $this->assertSame('Protected Admin', $target->fresh()->name);
        $this->assertTrue($target->fresh()->hasRole('super-admin'));
    }

    public function test_last_active_super_admin_cannot_be_disabled(): void
    {
        $superAdminRole = Role::query()->create([
            'name' => 'Super Administrator',
            'slug' => 'super-admin',
            'description' => 'Privileged role',
        ]);
        $actor = User::query()->create([
            'name' => 'Last Super Admin',
            'email' => 'last-admin@example.test',
            'password' => 'password1234',
            'is_active' => true,
            'locale' => 'en',
        ]);
        $actor->roles()->sync([$superAdminRole->id]);

        $this->actingAs($actor)->put("/admin/users/{$actor->id}", [
            'name' => $actor->name,
            'email' => $actor->email,
            'locale' => 'en',
            'is_active' => 0,
            'role_ids' => [$superAdminRole->id],
        ])->assertStatus(422);

        $this->assertTrue($actor->fresh()->is_active);
        $this->assertTrue($actor->fresh()->hasRole('super-admin'));
    }

    public function test_last_active_super_admin_cannot_remove_own_privileged_role(): void
    {
        $superAdminRole = Role::query()->create([
            'name' => 'Super Administrator',
            'slug' => 'super-admin',
            'description' => 'Privileged role',
        ]);
        $actor = User::query()->create([
            'name' => 'Last Super Admin',
            'email' => 'last-role-admin@example.test',
            'password' => 'password1234',
            'is_active' => true,
            'locale' => 'en',
        ]);
        $actor->roles()->sync([$superAdminRole->id]);

        $this->actingAs($actor)->put("/admin/users/{$actor->id}", [
            'name' => $actor->name,
            'email' => $actor->email,
            'locale' => 'en',
            'is_active' => 1,
            'role_ids' => [],
        ])->assertStatus(422);

        $this->assertTrue($actor->fresh()->hasRole('super-admin'));
    }
}
