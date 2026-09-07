<?php

namespace Tests;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createUserWithPermissions(array $permissions, array $attributes = []): User
    {
        $user = User::query()->create(array_merge([
            'name' => 'Test User', 'email' => uniqid('user').'@example.test', 'password' => 'password1234', 'is_active' => true, 'locale' => 'en',
        ], $attributes));
        $role = Role::query()->create(['name' => uniqid('Role '), 'slug' => uniqid('role-'), 'description' => 'Test role']);
        $ids = collect($permissions)->map(fn ($slug) => Permission::query()->firstOrCreate(['slug' => $slug], ['name' => $slug, 'group' => 'test'])->id);
        $role->permissions()->sync($ids);
        $user->roles()->sync([$role->id]);
        return $user;
    }
}
