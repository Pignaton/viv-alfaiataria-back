<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\TecidoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AddressController;

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
    Route::prefix('user')->group(function () {

        Route::get('/data/{email}', [UserController::class, 'getUserData']);
        Route::put('/{id}', [UserController::class, 'updateUserData']);

        Route::get('/addresses/data/{id}', [AddressController::class, 'getUserAddresses']);
        Route::post('/addresses', [AddressController::class, 'createAddress']);
        Route::put('/addresses/{id}', [AddressController::class, 'updateAddress']);
        Route::delete('/addresses/{id}', [AddressController::class, 'deleteAddress']);

        Route::prefix('medidas')->group(function () {
            //Route::get('/', [UserController::class, 'getMedidas']);
            Route::get('/data/{id}', [UserController::class, 'getMedida']);
            Route::post('/{id}', [UserController::class, 'saveMedidas']);
            Route::delete('/{id}', [UserController::class, 'deleteMedida']);

            // Rotas para perfis de medidas
            Route::post('/perfil', [UserController::class, 'savePerfilMedidas']);
            Route::get('/perfil/{nomePerfil}', [UserController::class, 'getPerfilMedidas']);
        });
    });
});

Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{slug}', [BlogController::class, 'show']);

Route::prefix('tecidos')->group(function () {
    Route::get('/', [TecidoController::class, 'index']);
    Route::get('/filtros', [TecidoController::class, 'opcoesFiltros']);
    Route::get('/{id}', [TecidoController::class, 'show']);
});

