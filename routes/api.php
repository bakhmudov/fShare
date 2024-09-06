<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

// Маршруты регистрации и авторизации от Laravel Breeze
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store']);
Route::get('/user/verify/{id}/{hash}', [EmailVerificationPromptController::class, '__invoke'])->name('verification.verify');

Route::middleware(['auth:sanctum'])->group(function () {
    // Маршруты для работы с директориями
    Route::post('/directories', [DirectoryController::class, 'store']);
    Route::delete('/directories/{id}', [DirectoryController::class, 'destroy']);
    Route::patch('/directories/{id}', [DirectoryController::class, 'update']);

    // Маршруты для работы с файлами
    Route::post('/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    Route::patch('/files/{id}', [FileController::class, 'update']);
    Route::get('/files/{id}', [FileController::class, 'show']);
    Route::patch('/files/{id}/toggle-public', [FileController::class, 'togglePublic']);

    // Маршрут для показа занятого пространства
    Route::get('/used-space', [FileController::class, 'usedSpace']);

    // Генерация ссылки для скачивания
    Route::get('/files/{id}/generate-download-link', [FileController::class, 'generateDownloadLink']);
});

// Публичное скачивание файла
Route::get('/files/download/{token}', [FileController::class, 'download']);
