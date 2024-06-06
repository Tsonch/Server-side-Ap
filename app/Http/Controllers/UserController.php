<?php

namespace App\Http\Controllers;

use app\DTO\UserCollectionDTO;
use App\Http\Requests\Auth;
use App\Http\Requests\UserRequest;
use App\Models\Roles;
use App\Models\User;
use App\Models\UsersAndRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function getUsers(Request $request) {
        $users = new UserCollectionDTO();
        return response()->json($users->users);
    }

    public function getUserRoles(UserRequest $request) {
        $user_id = $request->id;

        $roles_id = UsersAndRoles::select('role_id')->where('user_id', $user_id)->get();

    	$roles = $roles_id->map(function($id) {
    		return Roles::where('id', $id->role_id)->first();
    	});

    	return response()->json($roles);
    }

    public function hardDeleteUser(Request $request)
	{
		$user_id = $request->id;
		UsersAndRoles::where('user_id', $user_id)->forceDelete();

		$user = User::find($user_id);
		if ($user) {
			$user->forceDelete();
		} else {
			return response()->json(['status' => '404', 'message' => 'User not found'], 404);
		}
		return response()->json(['status' => '200']);
	}

	public function softDeleteUser(Request $request)
	{
		$user_id = $request->id;
		UsersAndRoles::where('user_id', $user_id)->delete();
		User::find($user_id)->delete();
		return response()->json(['status' => '200']);
	}

	public function restoreDeletedUser(Request $request)
	{
		$user_id = $request->id;
		UsersAndRoles::withTrashed()->where('user_id', $user_id)->restore();
		User::withTrashed()->find($user_id)->restore();
		return response()->json(['status' => '200']);
	}

}
