<?php

use App\Http\Controllers\JurusanController;
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
    Route::get("/users/{id}", [UserController::class, "get"]);
    Route::delete("/users/{id}", [UserController::class, "delete"]);
    Route::post("/users/{id}", [UserController::class, "update"]);
    Route::get("/users", [UserController::class, "search"]);

    Route::post("/jurusans", [JurusanController::class, "create"]);
    Route::get("/jurusans/{id}", [JurusanController::class, "get"]);
    Route::delete("/jurusans/{id}", [JurusanController::class, "delete"]);
    Route::get("/jurusans", [JurusanController::class, "search"]);
});

Route::post("/users/login", [UserController::class, "login"]);
