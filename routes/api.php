<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'register'])->name('users.register');
Route::post('/auth/login', [UserController::class, 'login'])->name('users.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [UserController::class, 'logout'])->name('users.logout');
    Route::patch('/user/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/user', [UserController::class, 'index'])->name('users.index');
});
// Route::post('/auth/logout', [UserController::class, 'logout'])->middleware('auth:sanctum')->name('users.logout');