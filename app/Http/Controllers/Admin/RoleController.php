<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', ['roles' => Role::query()->withCount(['users', 'permissions'])->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.roles.form', ['role' => new Role, 'permissions' => Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group')]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('roles.manage'), 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:roles,name'], 'description' => ['nullable', 'string', 'max:255'], 'permission_ids' => ['array'], 'permission_ids.*' => ['integer', 'exists:permissions,id']]);
        $role = Role::query()->create(['name' => $data['name'], 'slug' => Str::slug($data['name']), 'description' => $data['description'] ?? null]);
        $role->permissions()->sync($data['permission_ids'] ?? []);
        $audit->record('role.created', $role, [], $role->only(['name', 'slug']));

        return redirect()->route('admin.roles.index')->with('status', __('app.saved'));
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.form', ['role' => $role->load('permissions'), 'permissions' => Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group')]);
    }

    public function update(Request $request, Role $role, AuditService $audit): RedirectResponse
    {
        abort_if($role->slug === 'super-admin', 422, 'The super-admin role is immutable.');
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string', 'max:255'], 'permission_ids' => ['array'], 'permission_ids.*' => ['integer', 'exists:permissions,id']]);
        $old = $role->only(['name', 'description']);
        $role->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        $role->permissions()->sync($data['permission_ids'] ?? []);
        $audit->record('role.updated', $role, $old, $role->only(['name', 'description']));

        return redirect()->route('admin.roles.index')->with('status', __('app.saved'));
    }
}
