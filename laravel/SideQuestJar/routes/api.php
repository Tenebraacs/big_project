<?php

use App\Http\Controllers\Api\JarController;
use App\Http\Controllers\Api\JarInteractionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\LikeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API works'
    ]);
});




Route::post("/register", [UserController::class, "register"]);
Route::post("/login", [UserController::class, "login"]);


Route::middleware('auth:sanctum')->group(function() {

    Route::controller(UserController::class)->group(function (){
        Route::get('/user', 'user');
        Route::get('/user/navbar', 'navbarData');
        Route::post("/logout", "logout");
    });

    Route::controller(UserProfileController::class)->group(function () {
        Route::get('/user/profile', 'profile');
        Route::post('/user/profile/update', 'updateProfile');
        Route::post('/user/profile/password', 'updatePassword');
    });

    

    

    Route::controller(JarController::class)->group(function(){
        Route::get('/jars', "index")->withoutMiddleware('auth:sanctum');
        Route::get('/discover', 'discover')->withoutMiddleware('auth:sanctum');
        Route::get('/jars/popular', 'popularJars')->withoutMiddleware('auth:sanctum');
        Route::get('/jars/recommended', 'recommendedJars')->withoutMiddleware('auth:sanctum');
        Route::get('/jars/{jar}', 'show');
        Route::post('/jars/{jar}/join', [JarController::class, 'join']);
        Route::post('/jars/{jar}/leave', [JarController::class, 'leave']);
        Route::post('/jars',"store");
        Route::put("/jars/{jar}","update");
        Route::delete("/jars/{jar}", "destroy");

        // Quests management under Jars
        Route::post('/jars/{jar}/quests', 'storeQuest');
        Route::put('/jars/{jar}/quests/{quest}', 'updateQuest');
        Route::delete('/jars/{jar}/quests/{quest}', 'destroyQuest');
    });

    // Jar interactions
    Route::get('/jars/{jarId}/comments',       [JarInteractionController::class, 'getComments']);
    Route::post('/jars/{jarId}/comments',      [JarInteractionController::class, 'postComment']);
    Route::get('/jars/{jarId}/completions',    [JarInteractionController::class, 'getCompletions']);
    Route::post('/quests/{quest}/complete',    [JarInteractionController::class, 'completeQuest']);
    Route::post('/jars/{jar}/like',             [LikeController::class, 'toggleJar']);

    // Friends
    Route::get('/friends',                     [FriendController::class, 'index']);
    Route::post('/friends/add',                [FriendController::class, 'addByUsername']);
    Route::post('/friends/{user}',             [FriendController::class, 'store']);
    Route::post('/friends/{user}/accept',      [FriendController::class, 'accept']);
    Route::post('/friends/{user}/decline',     [FriendController::class, 'decline']);
    Route::delete('/friends/{user}',           [FriendController::class, 'destroy']);

    // User search (used by friends autocomplete)
    Route::get('/users/search',                [UserController::class, 'search']);

    // Messaging
    Route::get('/chat/{user}/messages',        [\App\Http\Controllers\Api\MessageController::class, 'fetchMessages']);
    Route::post('/chat/{user}/messages',       [\App\Http\Controllers\Api\MessageController::class, 'store']);

    // Jar Management Extensions
    Route::get('/user/jars',                   [JarController::class, 'userJars']);
    Route::post('/jar-invitations',            [JarController::class, 'invite']);

    // Admin
    Route::get('/admin/users',                 [\App\Http\Controllers\Api\AdminController::class, 'index']);
    Route::post('/admin/users/{id}/toggle-admin', [\App\Http\Controllers\Api\AdminController::class, 'toggleAdmin']);
    Route::delete('/admin/users/{id}',         [\App\Http\Controllers\Api\AdminController::class, 'deleteUser']);
});

