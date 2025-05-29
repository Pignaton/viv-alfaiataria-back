@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/tecidos">Gerenciar Tecidos</a></li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Tecidos</h3>
            <div class="card-tools">
                <a href="{{ route('admin.tecidos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Tecido
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Composição</th>
                    <th>Padrão</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tecidos as $tecido)
                    <tr>
                        <td class="text-center">
                            <img src="{{ $tecido->imagem_url }}" alt="{{ $tecido->nome_produto }}"
                                 style="max-width: 60px; max-height: 60px;" class="img-thumbnail">
                        </td>
                        <td>{{ $tecido->nome_produto }}</td>
                        <td>{{ $tecido->composicao }}</td>
                        <td>{{ $tecido->padrao }}</td>
                        <td>
                            @if($tecido->preco_promocional > 0)
                                <span class="text-danger"><del>R$ {{ number_format($tecido->preco, 2, ',', '.') }}</del></span>
                                <br>
                                <span class="text-success">R$ {{ number_format($tecido->preco_promocional, 2, ',', '.') }}</span>
                            @else
                                R$ {{ number_format($tecido->preco, 2, ',', '.') }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.tecidos.show', $tecido->id) }}" class="btn btn-sm btn-info" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.tecidos.edit', $tecido->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.tecidos.destroy', $tecido->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir este tecido?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $tecidos->links() }}
            </div>
        </div>
    </div>
@endsection
