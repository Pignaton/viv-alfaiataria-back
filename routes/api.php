<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\TecidoController;

Route::get('ping', function () {
    return ['pong' => true];
});

Route::post('/registrar', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/usuario', function (Request $request) {
        return $request->user()->load('cliente');
    });
});

Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{slug}', [BlogController::class, 'show']);

Route::prefix('tecidos')->group(function () {
    Route::get('/', [TecidoController::class, 'index']);
    Route::get('/filtros', [TecidoController::class, 'opcoesFiltros']);
    Route::get('/{id}', [TecidoController::class, 'show']);
});

