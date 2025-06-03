@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/tecidos">Gerenciar Tecidos</a></li>
            <li class="breadcrumb-item active">Visualizar Tecido</li>
        </ol>
    </div>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalhes do Tecido</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="{{ $tecido->imagem_url }}" alt="{{ $tecido->nome_produto }}"
                         class="img-fluid img-thumbnail" style="max-height: 300px;">
                </div>
                <div class="col-md-8">
                    <h2>{{ $tecido->nome_produto }}</h2>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Informações Técnicas</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Composição:</th>
                                    <td>{{ $tecido->composicao }}</td>
                                </tr>
                                <tr>
                                    <th>Padrão:</th>
                                    <td>{{ $tecido->padrao }}</td>
                                </tr>
                                <tr>
                                    <th>Suavidade:</th>
                                    <td>{{ $tecido->suavidade }}</td>
                                </tr>
                                <tr>
                                    <th>Tecelagem:</th>
                                    <td>{{ $tecido->tecelagem }}</td>
                                </tr>
                                <tr>
                                    <th>Fio:</th>
                                    <td>{{ $tecido->fio }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informações Comerciais</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Origem:</th>
                                    <td>{{ $tecido->origem }}</td>
                                </tr>
                                <tr>
                                    <th>Fabricante:</th>
                                    <td>{{ $tecido->fabricante ?? 'Não informado' }}</td>
                                </tr>
                                <tr>
                                    <th>Peso:</th>
                                    <td>{{ $tecido->peso }}</td>
                                </tr>
                                <tr>
                                    <th>Preço:</th>
                                    <td>
                                        @if($tecido->preco_promocional > 0)
                                            <span class="text-danger"><del>R$ {{ number_format($tecido->preco, 2, ',', '.') }}</del></span>
                                            <br>
                                            <span
                                                class="text-success">R$ {{ number_format($tecido->preco_promocional, 2, ',', '.') }}</span>
                                        @else
                                            R$ {{ number_format($tecido->preco, 2, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cadastrado em:</th>
                                    <td>{{ $tecido->data_cadastro->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.tecidos.edit', $tecido->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.tecidos.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
@endsection
