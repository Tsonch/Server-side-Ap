<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUsersAndRolesRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\UpdateUsersAndRolesRequest;
use App\Models\UsersAndRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersAndRolesController extends Controller
{
    public function assignRoleToUser(CreateUsersAndRolesRequest $request) {
        $user_id = $request->id;
        $user = Auth::id();

        $role_id = $request->input('role_id');

        $have_role = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id)->first();

        if($have_role) {
            return response()->json(['error' => 'The user already has such a role']);
        }

        try {
            DB::beginTransaction();

            $UAR = UsersAndRoles::create([
                'user_id' => $user_id,
                'role_id' => $role_id,
                'created_by' => $request->user()->id
            ]);

            $Log = new LogsController;
            $Log->createLogs('user_and_roles', 'create', $UAR->id, null, $UAR, $user);

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function hardDeleteUserRole(UpdateUsersAndRolesRequest $request) {

        try {
            DB::beginTransaction();

            $user = $request->user()->id;
            $user_id = $request->id;
            $role_id = $request->role_id;

            $user_role = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id);

            $Log = new LogsController;
            $Log->createLogs('user_and_roles', 'hardDelete', $user_role->id, $user_role, null, $user);

            $user_role->forceDelete();
            
            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function softDeleteUserRole(UpdateRoleRequest $request) {

        try {
            DB::beginTransaction();

            $user = $request->user()->id;
            $user_id = $request->id;
            $role_id = $request->role_id;

            $user_role = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id);
            $user_role->deleted_by = $user;

            $Log = new LogsController;
            $Log->createLogs('roles_and_permission', 'softDelete', $user_role->id, $user_role, null, $user);

            $user_role->delete();
            $user_role->save();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }
    
    public function restoreDeletedUserRole(UpdateRoleRequest $request) {

        try {
            DB::beginTransaction();

            $user = $request->user()->id;
            $user_id = $request->id;
            $role_id = $request->role_id;

            $user_role = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id)->first();

            $Log = new LogsController();
			$Log->createLogs('roles_and_permissions', 'restore', $user_role->id, null, $user_role, $user);

            $user_role->restore();
            $user_role->deleted_by = null;
            $user_role->save();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }
}
