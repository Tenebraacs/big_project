<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\JarController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\QuestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('/quest', QuestController::class);
Route::resource('/jar', JarController::class);
Route::resource('/library', LibraryController::class);
Route::resource('/achievement', AchievementController::class);
//TODO (frontend needed)