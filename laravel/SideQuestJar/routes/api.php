<?php

use App\Http\Controllers\Api\JarController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/jars', [JarController::class, "index"]);
Route::post('/jars', [JarController::class, "store"]);
Route::put("/jars/{jar}", [JarController::class, "update"]);
Route::delete("/jars/{jar}", [JarController::class, "destroy"]);

Route::post("register", [UserController::class, "register"]);