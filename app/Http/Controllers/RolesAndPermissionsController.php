<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRolesAndPermissionsRequest;
use App\Http\Requests\UpdateRolesAndPermissionsRequest;
use App\Models\Permissions;
use App\Models\RolesAndPermissions;
use Illuminate\Http\Request;

class RolesAndPermissionsController extends Controller
{
    public function getRolePermission(Request $request) {
        $role_id = $request->id;
        
        $permissions_id = RolesAndPermissions::select('permission_id')->where('role_id', $role_id)->get();

        $permissions = $permissions_id->map(function ($id) {
            return Permissions::where('id', $id->permission_id)->first();
        });

        return response()->json($permissions);
    }

    public function assignPermissionToRole(CreateRolesAndPermissionsRequest $request) {
        $role_id = $request->id;

        $permission_id = $request->input('permission_id');

        $have_permission = RolesAndPermissions::where('role_id', $role_id)->where('permission_id', $permission_id);
        if($have_permission) {
            return response()->json(['error' => 'The role already has such a permission']);
        }

        RolesAndPermissions::create([
            'role_id' => $role_id,
            'permission_id' => $permission_id,
            'created_by' => $request->user()->id
        ]);

        return response()->json(['status' => '200']);
    }

    public function hardDeleteRolePermission(UpdateRolesAndPermissionsRequest $request) {
        $role_id = $request->id;
        $permission_id = $request->permission_id;

        $user_role = RolesAndPermissions::withTrashed()->where('role_id', $role_id)->where('permission_id', $permission_id);
        $user_role->forceDelete();

        return response()->json(['status' => '200']);
    }

    public function softDeleteRolePermission(UpdateRolesAndPermissionsRequest $request) {
        $role_id = $request->id;
        $permission_id = $request->permission_id;

        $role_permission = RolesAndPermissions::where('role_id', $role_id)->where('permission_id', $permission_id);
        $role_permission->deleted_by = $request->user()->id;
        $role_permission->save();
        $role_permission->delete;

        return response()->json(['status' => '200']);
    }

    public function restoreDeletedRolePermission(UpdateRolesAndPermissionsRequest $request) {
        $role_id = $request->id;
        $permission_id = $request->permission_id;

        $role_permission = RolesAndPermissions::where('role_id', $role_id)->where('permission_id', $permission_id);

        $role_permission->restore();
        $role_permission->deleted_by = null;
        $role_permission->save;

        return response()->json(['status' => '200']);
    }
}
