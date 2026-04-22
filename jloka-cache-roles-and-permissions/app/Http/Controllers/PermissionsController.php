<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
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
        } catch (PermissionDoesNotExist $e) {
            return response()->json([
                'msg' => 'Permission Not Found',
                "details" => $e->getMessage(),
            ], 404);
        }
    }

    public function revokePermissionFromUser(Request $request)
    {
        try {
            $request->user()->revokePermissionTo($request->permission);

            return response()->json([
                'message' => 'Permission revoked'
            ]);
        } catch (PermissionDoesNotExist $e) {
            return response()->json([
                'msg' => 'Permission Not Found',
                "details" => $e->getMessage(),
            ], 404);
        }
    }

    public function assignPermissionToRole(Request $request)
    {
        try {
            $role = Role::findByName($request->role, 'web');
            $role->givePermissionTo($request->permission);

            return response()->json([
                'message' => 'Permission assigned to role'
            ]);
        } catch (RoleDoesNotExist $e) {
            return response()->json([
                'msg' => 'Role Not Found',
                "details" => $e->getMessage(),
            ], 404);
        } catch (PermissionDoesNotExist $e) {
            return response()->json([
                'msg' => 'Permission Not Found',
                "details" => $e->getMessage(),
            ], 404);
        }
    }
}
