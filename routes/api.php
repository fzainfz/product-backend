<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductStatusController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no auth required)
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Products (any user can view)
    Route::get('/get-products', [ProductController::class, 'index']);
    Route::get('/get-product/{id}', [ProductController::class, 'show']);
    Route::get('/get-categories', [ProductCategoryController::class, 'index']);
    Route::get('/get-statuses', [ProductStatusController::class, 'index']);
    Route::get('/get-dashboard', [DashboardController::class, 'dashboard']);
});

// Admin protected routes (JWT + Admin middleware)
Route::prefix('v1')->middleware(['auth', 'admin'])->group(function () {

    // Products
    Route::post('/store-product', [ProductController::class, 'store']);
    Route::post('/update-product/{id}', [ProductController::class, 'update']);
    Route::delete('/delete-product/{id}', [ProductController::class, 'destroy']);

    // Categories
    Route::get('/get-category/{id}', [ProductCategoryController::class, 'show']);
    Route::post('/store-category', [ProductCategoryController::class, 'store']);
    Route::put('/update-category/{id}', [ProductCategoryController::class, 'update']);
    Route::delete('/delete-category/{id}', [ProductCategoryController::class, 'destroy']);

    // Statuses
    Route::get('/get-status/{id}', [ProductStatusController::class, 'show']);
    Route::post('/store-status', [ProductStatusController::class, 'store']);
    Route::put('/update-status/{id}', [ProductStatusController::class, 'update']);
    Route::delete('/delete-status/{id}', [ProductStatusController::class, 'destroy']);
});
