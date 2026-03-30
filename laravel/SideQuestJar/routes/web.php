<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\JarController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\QuestController;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
    return view('home');
});
*/
//Route::resource('/quest', QuestController::class);
Route::resource('/jars', JarController::class);
//Route::resource('/library', LibraryController::class);
//Route::resource('/achievement', AchievementController::class);
//TODO (frontend needed)
Auth::routes();

Route::get('/home', [JarController::class, 'home'])->name('home');
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
