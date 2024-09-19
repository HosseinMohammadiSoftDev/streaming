 <?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Straem\Http\Controllers\FileController;
use Modules\Straem\Http\Controllers\InstituteEpisodeController;
use Modules\Straem\Http\Controllers\StraemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
        // این یک تست است برای نمایش در پلیر video.js
 Route::group(['prefix' => '/straem'], function () {
        Route::get('/show/{filename}', [StraemController::class,'show']); // برسی شود.
        Route::get('/{filename}', [StraemController::class,'stream'])->name('video.stream');
    }); 