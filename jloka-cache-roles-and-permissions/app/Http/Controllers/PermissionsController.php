<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function assignPermissionToUser(Request $request)
    {
        try {
            $request->user()->givePermissionTo($request->permission);
            return response()->json([
                'message' => 'Permission assigned'
            ]);
        }catch (PermissionDoesNotExist $e) {
            return response()->json([
                'msg' => 'Permission Not Found',
                "details" => $e->getMessage(),
            ], 404);
        }
        
    }

    public function revokePermissionFromUser(Request $request)
    {
        $permission = Permission::findByName($request->permission);
        
        if($permission == null) {
            return response()->json([
                'message' => 'No Permission Found',
            ]);
        }

        $request->user()->revokePermissionTo($request->permission);

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