<?php

namespace Waad\Repository\Traits;

use Illuminate\Support\Arr;

trait HasAnyRolePermissions
{
    /**
     * has Any Role or Permissions
     * @param string|array|null $role_or_permission
     * @param array|string|null $guard
     * @return bool
     */
    public function hasAnyRolePermissions(string|array|null $role_or_permission = null, array|string|null $guard = null)
    {
        if (blank($role_or_permission))
            return false;

        $guard = $guard ?: config('auth.defaults.guard');
        $guard = Arr::wrap($guard);

        $role_or_permission = Arr::wrap($role_or_permission);

        if ($this->roles->whereIn('name', $role_or_permission)->whereIn('guard_name', $guard)->count() > 0)
            return true;

        if ($this->getAllPermissions()->whereIn('name', $role_or_permission)->whereIn('guard_name', $guard)->count() > 0)
            return true;

        return false;
    }
}
