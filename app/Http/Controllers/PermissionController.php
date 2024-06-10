<?php

namespace App\Http\Controllers;

use App\DTO\PermissionsCollectionDTO;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permissions;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        try {
            DB::beginTransaction();

            $user = Auth::id();
            $permission_data = $request->createDTO();

            $new_permission = Permissions::create([
                'name' => $permission_data->name,
                'description' => $permission_data->description,
                'encryption' => $permission_data->encryption,
                'created_by' => $user
            ]);

            $Log = new LogsController;
            $Log->createLogs('permissions', 'create', $new_permission->id, null, $new_permission, $user);

            DB::commit();

            return response()->json($new_permission);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function updatePermission(UpdatePermissionRequest $request){
        try {
            DB::beginTransaction();

            $user = $request->user();
            $permission = Permissions::find($request->id);
            $permission_before = clone $permission;

            $permission->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'encryption' => $request->input('encryption')
            ]);

            $Log = new LogsController;
            $Log->createLogs('permissions', 'update', $permission->id, $permission_before, $permission, $user->id);

            DB::commit();

            return response()->json($permission);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function hardDeletePermission(UpdatePermissionRequest $request) {

        try {
            DB::beginTransaction();

            $user = $request->user();
            $permission = Permissions::withTrashed()->find($request->id);

            $Log = new LogsController;
            $Log->createLogs('permissions', 'hardDelete', $permission->id, $permission, null, $user->id);

            $permission->forceDelete();

            DB::commit();
                
            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function softDeletePermission(UpdatePermissionRequest $request) {
        try {
            DB::beginTransaction();

            $permission = Permissions::find($request->id);
            $user = $request->user()->id;
            $permission->deleted_by = $user;

            $Log = new LogsController;
            $Log->createLogs('permissions', 'softDelete', $permission->id, $permission, null, $user->id);

            $permission->delete();
            $permission->save();

            DB::commit();
                
            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function restoreDeletedPermission(UpdatePermissionRequest $request) {
        try {
            DB::beginTransaction();

            $user = $request->user()->id;
            $permission = Permissions::withTrashed()->find($request->id);

            $Log = new LogsController();
			$Log->createLogs('permissions', 'restore', $permission->id, null, $permission, $user);

            $permission->restore();
            $permission->deleted_by = null;
            $permission->save();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }
}
