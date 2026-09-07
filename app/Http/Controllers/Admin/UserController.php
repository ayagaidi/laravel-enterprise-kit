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
        return view('admin.users.form', ['user' => new User(), 'roles' => Role::query()->orderBy('name')->get()]);
    }

    public function store(StoreUserRequest $request, AuditService $audit): RedirectResponse
    {
        $data = $request->safe()->except(['role_ids']);
        $data['is_active'] = $request->boolean('is_active');
        $user = User::query()->create($data);
        $user->roles()->sync($request->input('role_ids', []));
        $audit->record('user.created', $user, [], $user->only(['name', 'email', 'is_active', 'locale']));

        return redirect()->route('admin.users.index')->with('status', __('app.saved'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user->load('roles'), 'roles' => Role::query()->orderBy('name')->get()]);
    }

    public function update(UpdateUserRequest $request, User $user, AuditService $audit): RedirectResponse
    {
        $old = $user->only(['name', 'email', 'is_active', 'locale']);
        $data = $request->safe()->except(['role_ids', 'password']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->filled('password')) $data['password'] = $request->string('password')->toString();
        $user->update($data);
        $user->roles()->sync($request->input('role_ids', []));
        $audit->record('user.updated', $user, $old, $user->only(['name', 'email', 'is_active', 'locale']));

        return redirect()->route('admin.users.index')->with('status', __('app.saved'));
    }
}
