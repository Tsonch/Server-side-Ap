<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRolesAndPermissionsRequest;
use App\Http\Requests\UpdateRolesAndPermissionsRequest;
use App\Models\Permissions;
use App\Models\RolesAndPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $user = Auth::id();

        $permission_id = $request->permission_id;

        $have_permission = RolesAndPermissions::where('role_id', $role_id)->where('permission_id', $permission_id)->first();
        if($have_permission) {
            return response()->json(['error' => 'The role already has such a permission']);
        }

        try {
            DB::beginTransaction();

            $RAP = RolesAndPermissions::create([
                'role_id' => $role_id,
                'permission_id' => $permission_id,
                'created_by' => $request->user()->id
            ]);

            $Log = new LogsController;
            $Log->createLogs('roles_and_permissions', 'create', $RAP->id, null, $RAP, $user);

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function hardDeleteRolePermission(UpdateRolesAndPermissionsRequest $request) {
        try {
            DB::beginTransaction();

            $role_id = $request->id;
            $permission_id = $request->permission_id;
            $user = $request->user();

            $user_role = RolesAndPermissions::withTrashed()->where('role_id', $role_id)->where('permission_id', $permission_id)->first();
            
            $Log = new LogsController;
            $Log->createLogs('roles_and_permissions', 'hardDelete', $user_role->id, $user_role, null, $user->id);

            $user_role->forceDelete();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function softDeleteRolePermission(UpdateRolesAndPermissionsRequest $request) {
        try {
            DB::beginTransaction();

            $role_id = $request->id;
            $permission_id = $request->permission_id;
            $user = $request->user()->id;

            $role_permission = RolesAndPermissions::where('role_id', $role_id)->where('permission_id', $permission_id)->first();
            $role_permission->deleted_by = $user;

            $Log = new LogsController;
            $Log->createLogs('roles_and_permission', 'softDelete', $role_permission->id, $role_permission, null, $user);

            $role_permission->delete();
            $role_permission->save();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function restoreDeletedRolePermission(UpdateRolesAndPermissionsRequest $request) {

        try {
            DB::beginTransaction();

            $user = $request->user()->id;
            $role_id = $request->id;
            $permission_id = $request->permission_id;

            $role_permission = RolesAndPermissions::where('role_id', $role_id)->where('permission_id', $permission_id)->first();

            $Log = new LogsController();
			$Log->createLogs('roles_and_permissions', 'restore', $role_permission->id, null, $role_permission, $user);

            $role_permission->restore();
            $role_permission->deleted_by = null;
            $role_permission->save();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }
}
