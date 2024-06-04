<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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

    Route::post('login', [UserController::class, "auth"])->name('login');

    Route::post('register', [UserController::class, "registration"])->middleware(AuthCheck::class);

    Route::middleware('auth:api')->group(function () {

        Route::get('me', [UserController::class, "me"])->name('me');

        Route::post('out', [UserController::class, "out"]);

        Route::get('tokens', [UserController::class, "tokens"]);

        Route::post('out_all', [UserController::class, "outAll"]);
    });
});

Route::prefix('ref')->group(function () {

    Route::prefix('policy/role')->group(function () {
        Route::get('/', [RoleController::class, "getRoles"]);
        Route::get('/{id}');
        Route::post('/');
        Route::put('/{id}');
        Route::delete('/{id}');
        Route::delete('/{id}/soft');
        Route::post('/{id}/restore');
    });

});