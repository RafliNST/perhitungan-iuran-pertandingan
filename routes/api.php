<?php

use App\Http\Controllers\CommunityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'register'])->name('users.register');
Route::post('/auth/login', [UserController::class, 'login'])->name('users.login');

Route::get('/communities', [CommunityController::class, 'index'])->name('communities.all');
Route::get('/communities/{community}', [CommunityController::class, 'show'])->name('communities.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('users.index');
    Route::post('/auth/logout', [UserController::class, 'logout'])->name('users.logout');
    Route::patch('/user/update', [UserController::class, 'update'])->name('users.patch');
    Route::put('/user/update', [UserController::class, 'update'])->name('users.put');

    Route::post('/communities', [CommunityController::class, 'store'])->name('communities.create');
    Route::patch('/communities/{community}', [CommunityController::class, 'update'])->name('communities.patch');
    Route::put('/communities/{community}', [CommunityController::class, 'update'])->name('communities.put');
    Route::delete('/communities/{community}', [CommunityController::class, 'destroy'])->name('communities.delete');
});