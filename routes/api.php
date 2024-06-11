<?php

use App\Http\Controllers\LogsController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolesAndPermissionsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersAndRolesController;
use App\Http\Middleware\AuthCheck;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {

    Route::post('login', [MainController::class, "auth"])->name('login');

    Route::post('register', [MainController::class, "registration"])->middleware(AuthCheck::class);

    Route::middleware('auth:api')->group(function () {

        Route::get('me', [MainController::class, "me"])->name('me');

        Route::post('out', [MainController::class, "out"]);

        Route::get('tokens', [MainController::class, "tokens"]);

        Route::post('out_all', [MainController::class, "outAll"]);
    });
});


Route::prefix('ref')->group(function () {

    Route::prefix('policy')->group(function () {
        Route::prefix('role')->group(function () {
            Route::get('/', [RoleController::class, "getRoles"])->middleware('CheckRole:get-list-role');
            Route::get('/{id}', [RoleController::class, "getTargetRole"])->middleware('CheckRole:read-role');
            Route::post('', [RoleController::class, "createRole"])->middleware('CheckRole:create-role');
            Route::put('/{id}', [RoleController::class, "updateRole"])->middleware('CheckRole:update-role');
            Route::delete('/{id}', [RoleController::class, "hardDeleteRole"])->middleware('CheckRole:delete-role');
            Route::delete('/{id}/soft', [RoleController::class, "softDeleteRole"])->middleware('CheckRole:delete-role');
            Route::post('/{id}/restore', [RoleController::class, "restoreDeletedRole"])->middleware('CheckRole:restore-role');

            Route::get('/{id}/permission', [RolesAndPermissionsController::class, 'getRolePermission'])->middleware('CheckRole:read-role');
            Route::get('/{id}/permission/{permission_id}', [RolesAndPermissionsController::class, 'assignPermissionToRole'])->middleware('CheckRole:update-role');
            Route::delete('/{id}/permission/{permission_id}', [RolesAndPermissionsController::class, 'hardDeleteRolePermission'])->middleware('CheckRole:delete-role');
            Route::delete('/{id}/permission/{permission_id}/soft', [RolesAndPermissionsController::class, 'softDeleteRolePermission'])->middleware('CheckRole:delete-role');
            Route::post('/{id}/permission/{permission_id}/restore', [RolesAndPermissionsController::class, 'restoreDeletedRolePermission'])->middleware('CheckRole:restore-role');
        });

        Route::prefix('permission')->group(function () {
            Route::get('/', [PermissionController::class, "getPermissions"])->middleware('CheckRole:get-list-permission');
            Route::get('/{id}', [PermissionController::class, "getTargetPermission"])->middleware('CheckRole:read-permission');
            Route::post('', [PermissionController::class, "createPermission"])->middleware('CheckRole:create-permission');
            Route::put('/{id}', [PermissionController::class, "updatePermission"])->middleware('CheckRole:update-permission');
            Route::delete('/{id}', [PermissionController::class, "hardDeletePermission"])->middleware('CheckRole:delete-permission');
            Route::delete('/{id}/soft', [PermissionController::class, "softDeletePermission"])->middleware('CheckRole:delete-permission');
            Route::post('/{id}/restore', [PermissionController::class, "restoreDeletedPermission"])->middleware('CheckRole:restore-permission');
        });

    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, "getUsers"])->middleware('CheckRole:get-list-user');
        Route::get('/{id}/role', [UserController::class, "getUserRoles"])->middleware('CheckRole:read-user');
        Route::post('/{id}/role', [UsersAndRolesController::class, "assignRoleToUser"])->middleware('CheckRole:read-user');
        Route::put('updateUser', [UserController::class, 'updateUser'])->middleware('CheckRole:read-user');
        Route::delete('{id}/hard', [UserController::class, 'hardDeleteUser'])->middleware('CheckRole:delete-user');
        Route::delete('{id}/soft', [UserController::class, 'softDeleteUser'])->middleware('CheckRole:delete-user');
        Route::post('{id}/restore', [UserController::class, 'restoreDeletedUser'])->middleware('CheckRole:restore-user');
        Route::put('{id}/changeUserRole', [UserController::class, 'changeUserRole'])->middleware('CheckRole:update-user');
        Route::delete('/{id}/role/{role_id}', [UsersAndRolesController::class, "hardDeleteUserRole"])->middleware('CheckRole:delete-user');
        Route::delete('/{id}/role/{role_id}/soft', [UsersAndRolesController::class, "softDeleteUserRole"])->middleware('CheckRole:delete-user');
        Route::post('/{id}/role/{role_id}/restore', [UsersAndRolesController::class, "restoreDeletedUserRole"])->middleware('CheckRole:delete-user');
    });

    Route::prefix('log')->group(function () {
        Route::get('{model}/{id}/story', [LogsController::class, "getLogs"])->middleware('CheckRole:get-story-user');
        Route::get('{id}/restore', [LogsController::class, "restoreRow"]);
    });
});
