<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\JarController;
use App\Http\Controllers\JarQuestController;
use App\Http\Controllers\JarInvitationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [JarController::class, 'home'])->name('home');
Route::redirect('/home', '/');

Route::post('/jars/{jar}/join', [JarController::class, 'join'])->middleware('auth')->name('jars.join');
Route::delete('/jars/{jar}/leave', [JarController::class, 'leave'])->middleware('auth')->name('jars.leave');
Route::get('/jars/{jar}/quests/create', [JarQuestController::class, 'create'])->name('jars.quests.create');
Route::post('/jars/{jar}/quests', [JarQuestController::class, 'store'])->name('jars.quests.store');
Route::get('/jars/{jar}/quests/{jarQuest}/edit', [JarQuestController::class, 'edit'])->name('jars.quests.edit');
Route::put('/jars/{jar}/quests/{jarQuest}', [JarQuestController::class, 'update'])->name('jars.quests.update');
Route::delete('/jars/{jar}/quests/{jarQuest}', [JarQuestController::class, 'destroy'])->name('jars.quests.destroy');

// 'index' is handled by home(); 'show' has an optional {quest?} parameter so it is registered separately.
Route::resource('/jars', JarController::class)->except(['index', 'show'])->middleware('auth');
Route::get('/jars/{jar}/{quest?}', [JarController::class, 'show'])->name('jars.show');

Route::get('/users/profile', [UserController::class, 'user'])
    ->middleware('auth')
    ->name('users.profile');





Route::get('/users', [UserController::class, 'index'])->middleware('auth')->name('users.index');



Route::get('/users/search', [UserController::class, 'search'])
    ->middleware('auth')
    ->name('users.search');

Route::get('/users/{username}', [UserController::class, 'show'])
    ->middleware('auth')
    ->name('users.show');


use App\Http\Controllers\FriendController;
use App\Http\Controllers\MessageController;

Auth::routes();

Route::middleware('auth')->group(function () {
    // Friends
    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/add', [FriendController::class, 'addByUsername'])->name('friends.add');
    Route::post('/friends/{user}', [FriendController::class, 'store'])->name('friends.store');
    Route::post('/friends/{user}/accept', [FriendController::class, 'accept'])->name('friends.accept');
    Route::post('/friends/{user}/decline', [FriendController::class, 'decline'])->name('friends.decline');
    Route::delete('/friends/{user}', [FriendController::class, 'destroy'])->name('friends.destroy');

    // Chat
    Route::get('/chat', [MessageController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [MessageController::class, 'show'])->name('chat.show');
    Route::get('/chat/{user}/messages', [MessageController::class, 'fetchMessages'])->name('chat.fetch');
    Route::post('/chat/{user}/messages', [MessageController::class, 'store'])->name('chat.store');

    // Jar Invitations
    Route::get('/jar-invitations/pending', [JarInvitationController::class, 'pending'])->name('jar-invitations.pending');
    Route::post('/jar-invitations', [JarInvitationController::class, 'store'])->name('jar-invitations.store');
    Route::post('/jar-invitations/{jarInvitation}/accept', [JarInvitationController::class, 'accept'])->name('jar-invitations.accept');
    Route::post('/jar-invitations/{jarInvitation}/decline', [JarInvitationController::class, 'decline'])->name('jar-invitations.decline');
});