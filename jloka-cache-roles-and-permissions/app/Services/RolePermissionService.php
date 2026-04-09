<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RolePermissionService
{
    public function assignPermssion(string $t1, string $permission)
    {
        $role = Role::findByName($t1);
        $role->assignPermssion($permission);
    }

    public function revokePermission(string $t1, string $permission) 
    {
        $role = Role::findByName($t1);
        $role->revokePermissionTo($permission);
    }

    public function syncPermissions(string $t1, array $permissions) 
    {
        $role = Role::findByName($t1);
        $role->syncPermissions($permissions);
    }
}