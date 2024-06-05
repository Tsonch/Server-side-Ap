<?php

namespace App\Http\Controllers;

use app\DTO\UserCollectionDTO;
use App\Http\Requests\UserRequest;
use App\Models\Roles;
use App\Models\UsersAndRoles;
use Illuminate\Http\Request;
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
}
