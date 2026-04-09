<?php

namespace App\Services;

use App\Models\User;

class UserRoleService
{
    public function assignRole(User $user, string $role): void
    {
        if(!$user->hasRole($role))
        {
            $user->assignRole($role);
        } 
    }

    public function removeRole(User $user, string $role): void
    {
        if($user->hasRole($role))
        {
            $user->removeRole($role);
        }
    }
}