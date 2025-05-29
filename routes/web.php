<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::resource('/clientes', \App\Http\Controllers\ClienteController::class)
        ->names('clientes')
        ->except(['show']);

    Route::resource('/tecidos', \App\Http\Controllers\TecidoController::class)
        ->names('tecidos')
        ->except(['show']);

    Route::resource('/blog', \App\Http\Controllers\BlogController::class)
        ->names('blog');

    // Rotas de perfil
    Route::get('perfil', [\App\Http\Controllers\PerfilController::class, 'perfil'])->name('perfil');
    Route::post('perfil/atualizar', [\App\Http\Controllers\PerfilController::class, 'atualizarPerfil'])->name('perfil.atualizar');
    Route::post('perfil/alterar-senha', [\App\Http\Controllers\PerfilController::class, 'alterarSenha'])->name('perfil.alterar-senha');
    Route::delete('perfil/revogar-sessao/{token}', [\App\Http\Controllers\PerfilController::class, 'revogarSessao'])->name('perfil.revogar-sessao');

    /* Route::get('/gerenciar-clientes', [App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
     Route::get('/criar/clientes', [App\Http\Controllers\ClienteController::class, 'create'])->name('clientes.create');
     Route::post('/salvar/clientes', [App\Http\Controllers\ClienteController::class, 'store'])->name('clientes.store');
     Route::get('/editar/clientes', [App\Http\Controllers\ClienteController::class, 'edit'])->name('clientes.edit');
     Route::put('/atualizar/clientes', [App\Http\Controllers\ClienteController::class, 'update'])->name('clientes.update');
     Route::delete('/deleta/clientes', [App\Http\Controllers\ClienteController::class, 'destroy'])->name('clientes.destroy');*/
    //Route::get('/atualizar/clientes', [App\Http\Controllers\ClienteController::class, 'forms'])->name('clientes.update');
});
