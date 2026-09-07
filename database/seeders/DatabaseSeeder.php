<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['Dashboard', 'dashboard.view', 'dashboard'],
            ['View users', 'users.view', 'users'], ['Create users', 'users.create', 'users'], ['Update users', 'users.update', 'users'],
            ['Manage roles', 'roles.manage', 'security'], ['View audit logs', 'audit.view', 'security'], ['Manage settings', 'settings.manage', 'system'],
        ];

        foreach ($permissions as [$name, $slug, $group]) {
            Permission::query()->updateOrCreate(['slug' => $slug], ['name' => $name, 'group' => $group]);
        }

        $superAdmin = Role::query()->updateOrCreate(['slug' => 'super-admin'], ['name' => 'Super Administrator', 'description' => 'Full system access.']);
        $admin = Role::query()->updateOrCreate(['slug' => 'administrator'], ['name' => 'Administrator', 'description' => 'Operational administration access.']);
        $admin->permissions()->sync(Permission::query()->whereIn('slug', ['dashboard.view', 'users.view', 'users.create', 'users.update', 'audit.view', 'settings.manage'])->pluck('id'));

        foreach ([
            ['app.name', 'Laravel Enterprise Kit', 'string', 'general', true],
            ['app.support_email', 'support@example.test', 'string', 'general', true],
            ['security.session_timeout_minutes', '120', 'integer', 'security', false],
            ['security.require_verified_email', '0', 'boolean', 'security', false],
        ] as [$key, $value, $type, $group, $public]) {
            SystemSetting::query()->updateOrCreate(['key' => $key], compact('value', 'type', 'group') + ['is_public' => $public]);
        }

        if ($password = env('SEED_ADMIN_PASSWORD')) {
            $user = User::query()->updateOrCreate(
                ['email' => env('SEED_ADMIN_EMAIL', 'admin@example.test')],
                ['name' => env('SEED_ADMIN_NAME', 'Demo Administrator'), 'password' => Hash::make($password), 'is_active' => true, 'locale' => 'en'],
            );
            $user->roles()->sync([$superAdmin->id]);
        }
    }
}
