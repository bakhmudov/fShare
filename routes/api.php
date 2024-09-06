<?php

use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\FileController;
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

Route::middleware(['auth:sanctum'])->group(function () {
    // Для директорий
    Route::post('/directories', [DirectoryController::class, 'store']);
    Route::delete('/directories/{id}', [DirectoryController::class, 'destroy']);
    Route::patch('/directories/{id}', [DirectoryController::class, 'update']);

    // Для файлов
    Route::post('/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    Route::patch('/files/{id}', [FileController::class, 'update']);
    Route::get('/files/{id}', [FileController::class, 'show']);
    Route::patch('/files/{id}/toggle-public', [FileController::class, 'togglePublic']);
    Route::get('/used-space', [FileController::class, 'usedSpace']);
    Route::get('/files/{id}/generate-download-link', [FileController::class, 'generateDownloadLink']);
});

Route::get('/files/download/{token}', [FileController::class, 'download']);
