<?php

use App\Http\Controllers\GedungController;
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

    Route::post("/gedung", [GedungController::class, "create"]);
    Route::delete("/gedung/{id}", [GedungController::class, "delete"]);
    Route::get("/gedung", [GedungController::class, "getAll"]);
    Route::get("/gedung/on", [GedungController::class, "getAllOn"]);
    Route::put("/gedung/status/{id}", [GedungController::class, "switchStatus"]);
    Route::put("/gedung/{id}", [GedungController::class, "update"]);
});

Route::post("/users/login", [UserController::class, "login"]);
