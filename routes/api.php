<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\post;
// Public routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/projects/category/{categoryId}', [ProjectController::class, 'getByCategory']);
Route::get('/projects/category-slug/{categorySlug}', [ProjectController::class, 'getByCategorySlug']);
Route::get('/projects-featured', [ProjectController::class, 'getFeatured']);
// Projects routes
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);
//Services routes
Route::get('/services', [SeviceController::class, 'index']);


// Protected admin routes
Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
Route::post('/verify/otp/', [AuthController::class, 'verifyOTP']);
Route::post('/reset/password/', [AuthController::class, 'resetPassword']);
Route::middleware(['is_login','is_admin' ])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    // Add other category routes here
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    // Services routes
    Route::post('/services', [SeviceController::class, 'store']);

    Route::delete('/services/{id}', [SeviceController::class, 'destroy']);

});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

