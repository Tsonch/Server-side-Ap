<?php

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
            Route::get('/', [RoleController::class, "getRoles"]);
            Route::get('/{id}', [RoleController::class, "getTargetRoles"]);
            Route::post('/', [RoleController::class, "createRole"]);
            Route::put('/{id}, [RoleController::class, "updateRole"]');
            Route::delete('/{id}', [RoleController::class, "hardDeleteRole"]);
            Route::delete('/{id}/soft', [RoleController::class, "softDeleteRole"]);
            Route::post('/{id}/restore', [RoleController::class, "restoreDeletedRole"]);
        });
    
        Route::prefix('permission')->group(function () {
            Route::get('/', [PermissionController::class, "getPermissions"]);
            Route::get('/{id}', [PermissionController::class, "getTargetPermission"]);
            Route::post('/', [PermissionController::class, "createPermission"]);
            Route::put('/{id}', [PermissionController::class, "updatePermission"]);
            Route::delete('/{id}', [PermissionController::class, "hardDeletePermission"]);
            Route::delete('/{id}/soft', [PermissionController::class, "softDeletePermission"]);
            Route::post('/{id}/restore', [PermissionController::class, "restoreDeletedPermission"]);
        }); 
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, "getUsers"]);
        Route::get('/{id}/role', [UserController::class, "getUserRoles"]);
        Route::post('/{id}/role', [UsersAndRolesController::class, "assignRoleToUser"]);
        Route::delete('/{id}/role/{role_id}', [UsersAndRolesController::class, "hardDeleteUserRole"]);
        Route::delete('/{id}/role/{role_id}/soft', [UsersAndRolesController::class, "softDeleteUserRole"]);
        Route::post('/{id}/role/{role_id}/restore', [UsersAndRolesController::class, "restoreDeletedUserRole"]);

        Route::get('/{id}/permission', [RolesAndPermissionsController::class, 'getRolePermission']);
        Route::get('/{id}/permission/{permission_id}', [RolesAndPermissionsController::class, 'assignPermissionToRole']);
        Route::delete('/{id}/permission/{permission_id}', [RolesAndPermissionsController::class, 'hardDeleteRolePermission']);
        Route::delete('/{id}/permission/{permission_id}/soft', [RolesAndPermissionsController::class, 'softDeleteRolePermission']);
        Route::post('/{id}/permission/{permission_id}/restore', [RolesAndPermissionsController::class, 'restoreDeletedRolePermission']);
    });
});