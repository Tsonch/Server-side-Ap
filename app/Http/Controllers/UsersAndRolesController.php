<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUsersAndRolesRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\UpdateUsersAndRolesRequest;
use App\Models\UsersAndRoles;
use Illuminate\Http\Request;

class UsersAndRolesController extends Controller
{
    public function assignRoleToUser(CreateUsersAndRolesRequest $request) {
        $user_id = $request->id;

        $role_id = $request->input('role_id');

        $have_role = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id);
        if($have_role) {
            return response()->json(['error' => 'The user already has such a role']);
        }

        UsersAndRoles::create([
            'user_id' => $user_id,
            'role_id' => $role_id,
            'created_by' => $request->user()->id
        ]);

        return response()->json(['status' => '200']);
    }

    public function hardDeleteUserRole(UpdateUsersAndRolesRequest $request) {
        $user_id = $request->id;
        $role_id = $request->role_id;

        $user_role = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id);
        $user_role->forceDelete();

        return response()->json(['status' => '200']);
    }

    public function softDeleteUserRole(UpdateRoleRequest $request) {
        $user_id = $request->id;
        $role_id = $request->role_id;

        $user_role = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id);
        $user_role->deleted_by = $request->user()->id;
        $user_role->save();
        $user_role->delete;

        return response()->json(['status' => '200']);
    }
    
    public function restoreDeletedUserRole(UpdateRoleRequest $request) {
        $user_id = $request->id;
        $role_id = $request->role_id;

        $user_role = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id);

        $user_role->restore();
        $user_role->deleted_by = null;
        $user_role->save;

        return response()->json(['status' => '200']);
    }
}
