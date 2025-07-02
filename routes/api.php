<?php

use App\Http\Controllers\GedungController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware("auth")->group(function (){
    Route::post("/users", [UserController::class, "create"]);
    Route::patch("/users/update-password", [UserController::class, "updatePassword"]);
    Route::delete("/users/logout", [UserController::class, "logout"]);
    Route::get("/users/{id}", [UserController::class, "get"]);
    Route::delete("/users/{id}", [UserController::class, "delete"]);
    Route::put("/users/{id}", [UserController::class, "update"]);
    Route::get("/users", [UserController::class, "search"]);

    Route::post("/jurusans", [JurusanController::class, "create"]);
    Route::get("/jurusans/{id}", [JurusanController::class, "get"]);
    Route::delete("/jurusans/{id}", [JurusanController::class, "delete"]);
    Route::get("/jurusans", [JurusanController::class, "search"]);

    Route::post("/gedung", [GedungController::class, "create"]);
    Route::delete("/gedung/{id}", [GedungController::class, "delete"]);
    Route::get("/gedung", [GedungController::class, "getAll"]);
    Route::get("/gedung/on", [GedungController::class, "getAllOn"]);
    Route::patch("/gedung/status/{id}", [GedungController::class, "switchStatus"]);
    Route::patch("/gedung/{id}", [GedungController::class, "update"]);

    Route::post("/gedung/{id}/ruangan", [RuanganController::class, "create"]);
    Route::get("/gedung/{gedungId}/ruangan/{ruanganId}", [RuanganController::class, "get"]);
    Route::delete("/gedung/{gedungId}/ruangan/{ruanganId}", [RuanganController::class, "delete"]);
    Route::patch("/gedung/{gedungId}/ruangan/{ruanganId}", [RuanganController::class, "switchStatus"]);
    Route::get("/gedung/{gedungId}/ruangan", [RuanganController::class, "search"]);

    Route::post("/gedung/{gedungId}/ruangan/{ruangId}/request", [RequestController::class, "create"]);
    Route::get("/request/{id}", [RequestController::class, "getMy"]);
    Route::get("/request", [RequestController::class, "search"]);
    Route::put("/request/{id}", [RequestController::class, "update"]);
});

Route::post("/users/login", [UserController::class, "login"]);
