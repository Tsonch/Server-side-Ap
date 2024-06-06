<?php

namespace App\Http\Controllers;

use App\DTO\PermissionsCollectionDTO;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function getPermissions(Request $request) {
        $permissions = new PermissionsCollectionDTO(Permissions::all());
        return response()->json($permissions->permissions);
    }

    public function getTargetPermisson(Request $request) {
        $permission = Permissions::find($request->id);
        return response()->json($permission);
    }

    public function createPermission(CreatePermissionRequest $request) {
        $user = Auth::id();
        $permission_data = $request->createDTO();

        $new_permission = Permissions::create([
            'name' => $permission_data->name,
            'description' => $permission_data->description,
            'encryption' => $permission_data->encryption,
            'created_by' => $user
        ]);

        return response()->json($new_permission);
    }

    public function updatePermission(UpdatePermissionRequest $request){
        $permission = Permissions::find($request->id);

        $permission->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return response()->json($permission);
    }

    public function hardDeletePermission(UpdatePermissionRequest $request) {
        $permission = Permissions::withTrashed()->find($request->id);
        $permission->forceDelete();

        return response()->json(['status' => '200']);
    }

    public function softDeletePermission(UpdatePermissionRequest $request) {
        $permission = Permissions::find($request->id);

        $user = $request->user()->id;

        $permission->deleted_by = $user;
        $permission->delete();
        $permission->save();

        return response()->json(['status' => '200']);
    }

    public function restoreDeletedPermission(UpdatePermissionRequest $request) {
        $permission = Permissions::withTrashed()->find($request->id);

        $permission->restore();
        $permission->deleted_by = null;
        $permission->save();

        return response()->json(['status' => '200']);
    }
}
