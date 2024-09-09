<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Permission\Http\Controllers\PermissionController;
use Modules\Permission\Http\Controllers\RoleController;



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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => '/roles'], function () {
        Route::group(['prefix' => '/show'], function () {
            Route::get('/all', [RoleController::class, 'getAllRoles']);
            Route::get('/details/{roleID}', [RoleController::class, 'getRoleDetails']);
            Route::get('/user-role/{userID}', [RoleController::class, 'getUserRoles']);
        });  
        Route::post('create-role', [RoleController::class, 'createRole']);
        Route::put('update-name', [RoleController::class, 'updateRoleName']);
        Route::delete('delete-role/{roleID}', [RoleController::class, 'deleteRole']);

        Route::post('assign-role-to-user', [RoleController::class, 'assignRoleToUser']);
        Route::delete('delete-role-from-user', [RoleController::class, 'removeRoleFromUser']);
    });


    Route::group(['prefix' => '/permissions'], function () {
        Route::get('role-by-permission/{permissionID}', [PermissionController::class,'getRolesByPermission']);
        Route::post('assign-permissions-to-role', [PermissionController::class, 'assignPermissionsToRole']);
        Route::delete('remove-permissions-from-role', [PermissionController::class, 'removePermissionsFromRole']);
        
        Route::get('show-all', [PermissionController::class, 'getAllPermissions']);
        Route::post('create', [PermissionController::class, 'createPermission']);
        Route::delete('delete/{permissionID}', [PermissionController::class, 'deletePermission']);
    });
}); 