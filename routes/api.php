<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;


Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
Route::post('/verify/otp/', [AuthController::class, 'verifyOTP']);
Route::post('/reset/password/', [AuthController::class, 'resetPassword']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/projects', [ProjectController::class, 'index']);

