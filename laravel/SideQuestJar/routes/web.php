<?php


use App\Http\Controllers\JarController;
use App\Http\Controllers\JarQuestController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [JarController::class, 'home'])->name('home');
Route::redirect('/home', '/');
//Route::resource('/quest', QuestController::class);

Route::post('/jars/{jar}/join', [JarController::class, 'join'])->middleware('auth')->name('jars.join');
Route::delete('/jars/{jar}/leave', [JarController::class, 'leave'])->middleware('auth')->name('jars.leave');
Route::get('/jars/{jar}/quests/create', [JarQuestController::class, 'create'])->name('jars.quests.create');
Route::post('/jars/{jar}/quests', [JarQuestController::class, 'store'])->name('jars.quests.store');
Route::get('/jars/{jar}/quests/{jarQuest}/edit', [JarQuestController::class, 'edit'])->name('jars.quests.edit');
Route::put('/jars/{jar}/quests/{jarQuest}', [JarQuestController::class, 'update'])->name('jars.quests.update');
Route::delete('/jars/{jar}/quests/{jarQuest}', [JarQuestController::class, 'destroy'])->name('jars.quests.destroy');

Route::resource('/jars', JarController::class)->except(['show']);
Route::get('/jars/{jar}/{quest?}', [JarController::class, 'show'])->name('jars.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/jars/{jar}/like', [LikeController::class, 'toggleJar']);

});
//Route::resource('/achievement', AchievementController::class);
//TODO (frontend needed)
Auth::routes();

//Route::get('/home', [JarController::class, 'home'])->name('home');
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
