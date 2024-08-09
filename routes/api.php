<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BorrowController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function(){
    Route::get('/dashboard',[BookController::class, 'dashboard']);
    Route::apiResource('book', BookController::class);
    Route::apiResource('category', CategoryController::class);

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/user', [UserController::class, 'index'])->middleware('isOwner');
        Route::apiResource('role', RoleController::class)->middleware('isOwner');
        Route::post('/profile', [ProfileController::class, 'store']);
        Route::get('/borrow', [BorrowController::class, 'index'])->middleware('isOwner');
        Route::post('/borrow', [BorrowController::class, 'store']);
    });
});
