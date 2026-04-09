<?php

namespace App\Services;

use App\Models\User;

class UserPermissionService
{
    public function giveUserPermission(User $user, string $permission) 
    {
        if (!$user->hasPermissionTo($permission)) 
        {
            $user->givePermissionTo($permission);
        }
    }

    public function revokeUserPermission(User $user, string $permission)
    {
        if ($user->hasPermissionTo($permission))
        {
            $user->revokePermissionTo($permission);
        }
        
    }
}
