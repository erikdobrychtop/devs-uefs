<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('jwt.custom')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);       // Listar todos os usuários
        Route::get('/{id}', [UserController::class, 'show']);    // Exibir um usuário
        Route::post('/', [UserController::class, 'store']);      // Criar um novo usuário
        Route::put('/{id}', [UserController::class, 'update']);  // Atualizar um usuário
        Route::delete('/{id}', [UserController::class, 'destroy']); // Remover um usuário
    });
    
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::post('/', [PostController::class, 'store']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });
    
    Route::prefix('tags')->group(function () {
        Route::get('/', [TagController::class, 'index']);
        Route::get('/{id}', [TagController::class, 'show']);
        Route::post('/', [TagController::class, 'store']);
        Route::put('/{id}', [TagController::class, 'update']);
        Route::delete('/{id}', [TagController::class, 'destroy']);
    });
});