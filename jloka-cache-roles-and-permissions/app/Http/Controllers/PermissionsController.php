<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function assignPermissionToUser(Request $request)
    {
        // $user = User::findOrFail(auth()->user()->id);

        auth()->user()->givePermissionTo($request->permission);

        return response()->json([
            'message' => 'Permission assigned'
        ]);
    }

    public function revokePermissionFromUser(Request $request)
    {
        // $user = User::findOrFail(auth()->user()->id);

        auth()->user()->revokePermissionTo($request->permission);

        return response()->json([
            'message' => 'Permission revoked'
        ]);
    }

    public function assignPermissionToRole(Request $request)
    {
        $role = Role::findByName($request->role);

        $role->givePermissionTo($request->permission);

        return response()->json([
            'message' => 'Permission assigned to role'
        ]);
    }
}