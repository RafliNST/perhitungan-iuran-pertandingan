<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'store']);
Route::apiResource('/users', UserController::class)
    ->except(['store'])
    ->middleware('auth:sanctum');
