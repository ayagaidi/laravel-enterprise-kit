<?php

namespace App\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return Permission::query()
            ->where('slug', $permission)
            ->whereHas('roles.users', fn ($query) => $query->whereKey($this->getKey()))
            ->exists();
    }

    public function assignRole(Role|string $role): static
    {
        $roleModel = $role instanceof Role ? $role : Role::query()->where('slug', $role)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$roleModel->getKey()]);

        return $this;
    }
}
