<?php

use App\Http\Controllers\Api\JarController;
use App\Http\Controllers\Api\UserController;
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
        Route::post("/logout", "logout");
    });

    

    

    Route::controller(JarController::class)->group(function(){
        Route::get('/jars', "index")->withoutMiddleware('auth:sanctum');
        Route::get('/jars/main', 'mainJar')->withoutMiddleware('auth:sanctum');
        Route::post('/jars',"store");
        Route::put("/jars/{jar}","update");
        Route::delete("/jars/{jar}", "destroy");
    });

});

