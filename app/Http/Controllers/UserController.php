<?php

namespace App\Http\Controllers;

use App\DTO\UserCollectionDTO;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\Roles;
use App\Models\User;
use App\Models\UsersAndRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
	public function getUsers(Request $request)
	{
		$users = new UserCollectionDTO();
		return response()->json($users->users);
	}

	public function getUserRoles(UserRequest $request)
	{
		$user_id = $request->id;

		$roles_id = UsersAndRoles::select('role_id')->where('user_id', $user_id)->get();

		$roles = $roles_id->map(function ($id) {
			return Roles::where('id', $id->role_id)->first();
		});

		return response()->json($roles);
	}

	public function hardDeleteUser(Request $request)
	{

		try {
			DB::beginTransaction();

			$user_id = $request->id;

			$user = User::find($user_id);

			if (!$user) {
				return response()->json(['status' => '404', 'message' => 'User not found'], 404);
			}

			$UAR = UsersAndRoles::where('user_id', $user_id)->get();

			$Log = new LogsController;
			$Log->createLogs('users', 'hardDelete', $user->id, $user, null, $request->user()->id);

			$UAR->each(function ($UAR) {
				$UAR->forcedelete();
			});
			$user->forceDelete();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $err) {
			DB::rollback();
			throw $err;
		}
	}

	public function softDeleteUser(Request $request)
	{
		try {
			DB::beginTransaction();

			$user_id = $request->id;

			$user = User::find($user_id);

			if (!$user) {
				return response()->json(['status' => '404', 'message' => 'User not found'], 404);
			}

			$UAR = UsersAndRoles::where('user_id', $user_id)->get();

			$Log = new LogsController;
			$Log->createLogs('users', 'softDeleteUser', $user->id, $user, null, $request->user()->id);

			$UAR->each(function ($UAR) {
				$UAR->deleted_by = Auth::id();
				$UAR->delete();
				$UAR->save();
			});

			$user->delete();
			$user->save();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $err) {
			DB::rollback();
			throw $err;
		}
	}

	public function restoreDeletedUser(Request $request)
	{

		try {
			DB::beginTransaction();

			$user_id = $request->id;
			$UAR = UsersAndRoles::withTrashed()->where('user_id', $user_id)->restore();

			$UAR->each(function ($UAR) {
				$UAR->restore();
				$UAR->deleted_by = null;
				$UAR->save();
			});

			$user = User::withTrashed()->find($user_id)->restore();

			$user->restore();

			$Log = new LogsController();
			$Log->createLogs('user', 'restore', $user->id, null, $user, Auth::id());

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $err) {
			DB::rollback();
			throw $err;
		}
	}

	public function changeUserRole(Request $request)
	{

		try {
			DB::beginTransaction();

			$user = UsersAndRoles::where('user_id', $request->id)->first();
			$user_before = clone $user;
			$role = $request->role;

			$user->update([
				'role_id' => $role,
			]);

			$user->save();

			$Log = new LogsController();
			$Log->createLogs('users_and_roles', 'update', $user->id, $user_before, $user, Auth::id());

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $err) {
			DB::rollback();
			throw $err;
		}
	}

	public function updateUser(UpdateUserRequest $request)
	{
		$new_pass = $request->new_password;
		$new_email = $request->new_email;
		$new_birthday = $request->new_birthday;
		$new_username = $request->new_username;

		try {
			DB::beginTransaction();

			$user = User::find(Auth::id());
			$user_before = clone $user;
			if (!$user || !Hash::check($request->old_password, $user->password)) {
				return response()->json(['message' => 'Old password is incorrect'], 401);
			}
			if ($new_pass != '') {
				$user->update([
					'password' => $new_pass,
				]);
				$user->token()->revoke();
			}
			if ($new_email != '') {
				$user->update([
					'email' => $new_email,
				]);
			}
			if ($new_birthday != '') {
				$user->update([
					'birthday' => $new_birthday,
				]);
			}
			if ($new_username != '') {
				$user->update([
					'username' => $new_username,
				]);
			}

			$user->save();
			$Log = new LogsController();
			$Log->createLogs('users', 'update', $user->id, $user_before, $user, Auth::id());

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $err) {
			DB::rollback();
			throw $err;
		}
	}
}
