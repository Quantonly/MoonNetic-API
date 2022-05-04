<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('forgot', [App\Http\Controllers\AuthController::class, 'forgot']);
    Route::post('update', [App\Http\Controllers\AuthController::class, 'update']);
    Route::get('logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
    Route::get('user', [App\Http\Controllers\AuthController::class, 'getUser']);
});

Route::get('/folder', [App\Http\Controllers\FolderController::class, 'getFolder']);
Route::post('/upload', [App\Http\Controllers\FileController::class, 'uploadFile']);
Route::post('/file', [App\Http\Controllers\FileController::class, 'downloadFile']);
Route::post('/readfile', [App\Http\Controllers\FileController::class, 'readFile']);
Route::post('/createfolder', [App\Http\Controllers\FileController::class, 'createFolder']);
Route::post('/createfile', [App\Http\Controllers\FileController::class, 'createFile']);
Route::post('/renamefile', [App\Http\Controllers\FileController::class, 'renameFile']);
Route::post('/deletefiles', [App\Http\Controllers\FileController::class, 'deleteFiles']);
Route::post('/editfile', [App\Http\Controllers\FileController::class, 'editfile']);