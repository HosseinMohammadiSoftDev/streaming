<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Straem\Http\Controllers\FileController;

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


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => '/file'], function () {
        Route::get('show-all', [FileController::class, 'index']);
        Route::post('upload', [FileController::class, 'new']);
        Route::get('download/{file}', [FileController::class, 'show']);
        Route::delete('delete/{file}', [FileController::class, 'delete']);
    });
});

