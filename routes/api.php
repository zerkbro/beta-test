<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Register new user.
Route::post('/register',[AuthController::class,'store']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/profile',[AuthController::class,'profile'])->middleware('auth:sanctum');
Route::get('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

Route::post('/upload', ImageUploadController::class);