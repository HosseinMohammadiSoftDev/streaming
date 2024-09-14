<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\UserController;
use Modules\Permission\Http\Middleware\CheckPermission;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function(){
    return response()->json('ادرس درست وارد نشده است');
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::delete('/delete_user', [AuthController::class, 'deleteUser'])->middleware('auth:sanctum');

// Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
// Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

Route::get('/users', [UserController::class, 'index'])->middleware('permission:view user information,sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => '/users'], function () {
            Route::get('/{id}', [UserController::class, 'show'])->middleware('permission:view user information,sanctum');
        Route::put('/{id}', [UserController::class, 'update']);
    });
});
