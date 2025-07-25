<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\MetodoPagamentoController;
use App\Http\Controllers\Api\TecidoController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('ping', function () {
    return ['pong' => true];
});

Route::post('/registrar', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/check-reset-token/{token}', [AuthController::class, 'checkResetToken']);
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword']);

Route::get('/email/verify/{id}/{hash}', function (string $id, string $hash) {
    $user = \App\Models\Usuario::findOrFail($id);

    if (!hash_equals(sha1($user->email), $hash)) {
        abort(403, 'Link de verificação inválido');
    }

    if ($user->email_verificado_em) {
        return redirect('https://vivalfaiataria.com.br/login?message=Email já verificado');
    }

    $user->forceFill([
        'email_verificado_em' => now(),
        'ativo' => true
    ])->save();

    return redirect('https://vivalfaiataria.com.br/login?message=Email verificado com sucesso');
})->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('resend-verification', [AuthController::class, 'resendVerificationEmail']);
});

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

        Route::prefix('metodos-pagamento')->group(function () {
            Route::get('/{id}', [MetodoPagamentoController::class, 'getMetodoPagamento']);
            Route::post('/cartao', [MetodoPagamentoController::class, 'saveCartao']);
            Route::delete('/cartao/{id}', [MetodoPagamentoController::class, 'removeCartao']);
        });

        Route::prefix('medidas')->group(function () {
            Route::get('/{id}', [UserController::class, 'getMedidas']);
            Route::get('/data/{id}', [UserController::class, 'getMedida']);
            Route::post('/{id}', [UserController::class, 'saveMedidas']);
            Route::delete('/{id}', [UserController::class, 'deleteMedida']);

            // Rotas para perfis de medidas
            Route::post('/perfil', [UserController::class, 'savePerfilMedidas']);
            Route::get('/perfil/{nomePerfil}', [UserController::class, 'getPerfilMedidas']);
        });

        Route::get('/purchase-history/{usuario_id}', [UserController::class, 'getPurchaseHistory']);
    });

//Rotas carrinho
    Route::prefix('cart')->group(function () {
        Route::put('/item/{itemId}', [CartController::class, 'updateCartItem']);
        Route::delete('/item/{itemId}', [CartController::class, 'removeFromCart']);
        Route::post('/checkout', [CartController::class, 'checkout']);
    });

});

//públicas carrinho
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getCart']);
    Route::post('/add', [CartController::class, 'addToCart']);
});

Route::post('/cart/checkout', [CartController::class, 'checkout']);
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);

Route::get('/blog/posts', [BlogController::class, 'index']);
Route::get('/blog/posts/{slug}', [BlogController::class, 'show']);
Route::get('/blog/posts/latest', [BlogController::class, 'latest']);

Route::prefix('tecidos')->group(function () {
    Route::get('/', [TecidoController::class, 'index']);
    Route::get('/filtros', [TecidoController::class, 'opcoesFiltros']);
    Route::get('/{id}', [TecidoController::class, 'show']);
});

