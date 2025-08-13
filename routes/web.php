<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/clientes/datatable', [\App\Http\Controllers\ClienteController::class, 'datatable'])
        ->name('clientes.datatable');

    Route::resource('/clientes', \App\Http\Controllers\ClienteController::class)
        ->names('clientes');

    Route::get('/tecidos/datatable', [\App\Http\Controllers\TecidoController::class, 'datatable'])
        ->name('tecidos.datatable');

    Route::resource('/tecidos', \App\Http\Controllers\TecidoController::class)
        ->names('tecidos');


    // Rotas de Blog
    Route::get('/blog/datatable', [\App\Http\Controllers\BlogController::class, 'datatable'])
        ->name('blog.datatable');
    Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])
        ->name('blog.index');
    Route::get('/blog/create', [\App\Http\Controllers\BlogController::class, 'create'])
        ->name('blog.create');
    Route::post('/blog/store', [\App\Http\Controllers\BlogController::class, 'store'])
        ->name('blog.store');
    Route::get('/blog/{id}/edit', [\App\Http\Controllers\BlogController::class, 'edit'])
        ->name('blog.edit');
    Route::put('/blog/update/{post}', [\App\Http\Controllers\BlogController::class, 'update'])
        ->name('blog.update');
    Route::get('/blog/{id}', [\App\Http\Controllers\BlogController::class, 'show'])
        ->name('blog.show');
    Route::delete('/blog/delete/{id}', [\App\Http\Controllers\BlogController::class, 'destroy'])
        ->name('blog.destroy');

    Route::get('pedidos/datatable', [\App\Http\Controllers\PedidoController::class, 'datatable'])
        ->name('pedidos.datatable');

    Route::resource('pedidos', \App\Http\Controllers\PedidoController::class)
        ->except(['create', 'store'])
        ->names('pedidos');

    Route::get('pedidos/filtrar/{status}', [\App\Http\Controllers\PedidoController::class, 'filtrarPorStatus'])
        ->name('pedidos.filtrar');

    Route::get('pedidos/{pedido}/pagamentos', [\App\Http\Controllers\PedidoController::class, 'pagamentos'])
        ->name('pedidos.pagamentos');


    Route::put('pagamentos/{pagamento}', [\App\Http\Controllers\PedidoController::class, 'atualizarPagamento'])
        ->name('pagamentos.update');

    Route::post('pagamentos/{pagamento}/reembolsar', [\App\Http\Controllers\PedidoController::class, 'solicitarReembolso'])
        ->name('pagamentos.reembolsar');

    // Rotas de perfil
    Route::get('perfil', [\App\Http\Controllers\PerfilController::class, 'perfil'])->name('perfil');
    Route::post('perfil/atualizar', [\App\Http\Controllers\PerfilController::class, 'atualizarPerfil'])->name('perfil.atualizar');
    Route::post('perfil/alterar-senha', [\App\Http\Controllers\PerfilController::class, 'alterarSenha'])->name('perfil.alterar-senha');
    Route::delete('perfil/revogar-sessao/{token}', [\App\Http\Controllers\PerfilController::class, 'revogarSessao'])->name('perfil.revogar-sessao');

    Route::post('enderecos', [\App\Http\Controllers\EnderecoController::class, 'store'])->name('enderecos.store');
    Route::delete('enderecos/{endereco}', [\App\Http\Controllers\EnderecoController::class, 'destroy'])->name('enderecos.destroy');

    // API para busca de CEP
    Route::get('api/buscar-cep', [\App\Http\Controllers\EnderecoController::class, 'buscarCep'])
        ->name('api.buscar-cep');


    Route::resource('carousel', \App\Http\Controllers\Configuracao\CarouselController::class)
        ->except(['show'])
        ->names([
            'index' => 'carousel.index',
            'create' => 'carousel.create',
            'store' => 'carousel.store',
            'edit' => 'carousel.edit',
            'update' => 'carousel.update',
            'destroy' => 'carousel.destroy'
        ]);

    Route::get('service', [\App\Http\Controllers\Configuracao\ServiceController::class, 'edit'])->name('service.edit');
    Route::put('service', [\App\Http\Controllers\Configuracao\ServiceController::class, 'update'])->name('service.update');
    Route::get('service/create', [\App\Http\Controllers\Configuracao\ServiceController::class, 'create'])->name('service.create');
    Route::post('service', [\App\Http\Controllers\Configuracao\ServiceController::class, 'store'])->name('service.store');
    Route::get('service', [\App\Http\Controllers\Configuracao\ServiceController::class, 'edit'])->name('service.edit');
    Route::put('service', [\App\Http\Controllers\Configuracao\ServiceController::class, 'update'])->name('service.update');


});
