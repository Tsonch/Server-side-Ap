<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\DTO\RolesCollectionDTO;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function getRoles(Request $request) {
        $roles = new RolesCollectionDTO(Roles::all());
        return response()->json($roles->roles);
    }

    public function getTargetRole(Request $request) {
        $role = Roles::find($request->id);
        return response()->json($role);
    }

    public function createRole(CreateRoleRequest $request) {
        $user = $request->user;
        $role_data = $request->createDTO();

        $new_role = Roles::create([
            'name' => $role_data->name,
            'description' => $role_data->description,
            'encryption' => $role_data->encryption,
            'created_by' => $user->id
        ]);

        return response()->json($new_role);
    }

    public function updateRole(UpdateRoleRequest $request){
        $role = Roles::find($request->id);
        dd($role);

        $role->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return response()->json($role);
    }

    public function hardDeleteRole(UpdateRoleRequest $request) {
        $role_id = $request->id;
        if($role_id == 1) {
            return response()->json(['error' => "You can't remove the admin"]);
        }
        
        $role = Roles::withTrashed()->find($role_id);
        $role->forceDelete();

        return response()->json(['status' => '200']);
    }

    public function softDeleteRole(UpdateRoleRequest $request) {
        $role_id = $request->id;
        if($role_id == 1) {
            return response()->json(['error' => "You can't remove the admin"]);
        }

        $role = Roles::find($role_id);
        if(!$role) {
            return response()->json(['error' => "The role with this id was not found"]);
        }

        $user = $request->user()->id;

        $role->deleted_by = $user;
        $role->save();
        $role->delete;

        return response()->json(['status' => '200']);
    }

    public function restoreDeletedRole(UpdateRoleRequest $request) {
        $role = Roles::withTrashed()->find($request->id);

        $role->restore();
        $role->deleted_by = null;
        $role->save;

        return response()->json(['status' => '200']);
    }
}
