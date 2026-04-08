<?php


use App\Http\Controllers\JarController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [JarController::class, 'home'])->name('root');
Route::redirect('/home', '/');
//Route::resource('/quest', QuestController::class);
Route::resource('/jars', JarController::class)->except(['show']);
Route::get('/jars/{jar}/{quest?}', [JarController::class, 'show'])->name('jars.show');
Route::post('/jars/{jar}/join', [JarController::class, 'join'])->middleware('auth')->name('jars.join');
Route::delete('/jars/{jar}/leave', [JarController::class, 'leave'])->middleware('auth')->name('jars.leave');
//Route::resource('/library', LibraryController::class);
//Route::resource('/achievement', AchievementController::class);
//TODO (frontend needed)
Auth::routes();

//Route::get('/home', [JarController::class, 'home'])->name('home');
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
