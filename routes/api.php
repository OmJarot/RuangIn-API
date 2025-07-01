<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware("auth")->group(function (){
    Route::post("/users", [UserController::class, "create"]);
    Route::post("/users/update-password", [UserController::class, "updatePassword"]);
    Route::delete("/users/logout", [UserController::class, "logout"]);
});

Route::post("/users/login", [UserController::class, "login"]);
