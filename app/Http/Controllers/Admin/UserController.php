<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', ['users' => User::query()->with('roles')->latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User,
            'roles' => $this->availableRolesFor(auth()->user()),
        ]);
    }

    public function store(StoreUserRequest $request, AuditService $audit): RedirectResponse
    {
        $roleIds = $request->input('role_ids', []);
        $this->assertCanAssignRoles($request->user(), $roleIds);

        $data = $request->safe()->except(['role_ids']);
        $data['is_active'] = $request->boolean('is_active');
        $user = User::query()->create($data);
        $user->roles()->sync($roleIds);
        $audit->record('user.created', $user, [], $user->only(['name', 'email', 'is_active', 'locale']));

        return redirect()->route('admin.users.index')->with('status', __('app.saved'));
    }

    public function edit(User $user): View
    {
        $this->assertCanManageTarget(auth()->user(), $user);

        return view('admin.users.form', [
            'user' => $user->load('roles'),
            'roles' => $this->availableRolesFor(auth()->user()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, AuditService $audit): RedirectResponse
    {
        $roleIds = $request->input('role_ids', []);
        $this->assertCanManageTarget($request->user(), $user);
        $this->assertCanAssignRoles($request->user(), $roleIds);
        $this->assertLastSuperAdminRemainsActive($user, $roleIds, $request->boolean('is_active'));

        $old = $user->only(['name', 'email', 'is_active', 'locale']);
        $data = $request->safe()->except(['role_ids', 'password']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->filled('password')) {
            $data['password'] = $request->string('password')->toString();
        }
        $user->update($data);
        $user->roles()->sync($roleIds);
        $audit->record('user.updated', $user, $old, $user->only(['name', 'email', 'is_active', 'locale']));

        return redirect()->route('admin.users.index')->with('status', __('app.saved'));
    }

    private function availableRolesFor(User $actor)
    {
        return Role::query()
            ->when(! $actor->hasRole('super-admin'), fn ($query) => $query->where('slug', '!=', 'super-admin'))
            ->orderBy('name')
            ->get();
    }

    private function assertCanManageTarget(User $actor, User $target): void
    {
        if ($target->hasRole('super-admin') && ! $actor->hasRole('super-admin')) {
            abort(403, 'Only a super administrator can manage another super administrator.');
        }
    }

    private function assertCanAssignRoles(User $actor, array $roleIds): void
    {
        if ($actor->hasRole('super-admin')) {
            return;
        }

        $assignsSuperAdmin = Role::query()
            ->where('slug', 'super-admin')
            ->whereIn('id', $roleIds)
            ->exists();

        abort_if($assignsSuperAdmin, 403, 'Only a super administrator can assign the super-admin role.');
    }

    private function assertLastSuperAdminRemainsActive(User $target, array $roleIds, bool $willBeActive): void
    {
        if (! $target->hasRole('super-admin')) {
            return;
        }

        $superAdminRoleId = Role::query()->where('slug', 'super-admin')->value('id');
        $keepsSuperAdminRole = $superAdminRoleId && in_array($superAdminRoleId, $roleIds, true);

        if ($willBeActive && $keepsSuperAdminRole) {
            return;
        }

        $activeSuperAdmins = User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->where('slug', 'super-admin'))
            ->count();

        abort_if($activeSuperAdmins <= 1, 422, 'The last active super administrator cannot be disabled or demoted.');
    }
}
