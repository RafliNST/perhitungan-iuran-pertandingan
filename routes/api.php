<?php

use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityMemberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemExpenseController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'register'])->name('users.register');
Route::post('/auth/login', [UserController::class, 'login'])->name('users.login');

Route::get('/communities', [CommunityController::class, 'index'])->name('communities.all');
Route::get('/communities/{community}', [CommunityController::class, 'show'])->name('communities.show');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('users.index');
    Route::post('/auth/logout', [UserController::class, 'logout'])->name('users.logout');
    Route::patch('/user/update', [UserController::class, 'update'])->name('users.patch');
    Route::put('/user/update', [UserController::class, 'update'])->name('users.put');

    Route::post('/communities', [CommunityController::class, 'store'])->name('communities.create');
    Route::patch('/communities/{community}', [CommunityController::class, 'update'])->name('communities.patch');
    Route::put('/communities/{community}', [CommunityController::class, 'update'])->name('communities.put');
    Route::delete('/communities/{community}', [CommunityController::class, 'destroy'])->name('communities.delete');

    Route::post('/community-members', [CommunityMemberController::class, 'store'])
        ->name('community-members.store');
    Route::delete('/communities/{community}/members/{user}', [CommunityMemberController::class, 'destroyByUser']);

    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::match(['put', 'patch'], '/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');

    // Games
    Route::get('/events/{event}/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::delete('/games/{game}', [GameController::class, 'destroy']);

    // Items
    Route::get('/events/{event}/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::match(['put', 'patch'], '/items/{item}', [ItemController::class, 'update']);
    Route::delete('/items/{item}', [ItemController::class, 'destroy']);

    // Item Expenses
    Route::post('/item-expenses', [ItemExpenseController::class, 'store']);
    Route::match(['put', 'patch'], '/item-expenses/{itemExpense}', [ItemExpenseController::class, 'update']);
    Route::delete('/item-expenses/{itemExpense}', [ItemExpenseController::class, 'destroy']);
});