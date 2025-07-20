@extends('adminlte::page')

@section('title', 'Gerenciar Pedidos')

@section('style')
    <style>
        .measures-container {
            max-height: 150px;
            overflow-y: auto;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .measures-container ul {
            margin-bottom: 0;
        }
        .medidas-container {
            max-height: 200px;
            overflow-y: auto;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-top: 5px;
        }

        .medidas-container ul {
            list-style-type: none;
            padding-left: 15px;
            margin-bottom: 0;
        }

        .medidas-container li {
            padding: 3px 0;
            font-size: 0.85rem;
            border-bottom: 1px solid #eee;
        }

        .medidas-container li:last-child {
            border-bottom: none;
        }
    </style>
@endsection

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/pedidos">Gerenciar Pedidos</a></li>
            <li class="breadcrumb-item active">Visualizar Pedido</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalhes do Pedido #{{ $pedido->codigo }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informações do Pedido</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Código:</th>
                                    <td>{{ $pedido->codigo }}</td>
                                </tr>
                                <tr>
                                    <th>Data:</th>
                                    <td>{{ $pedido->data_pedido_formatada }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                    <span
                                        class="badge badge-{{ $pedido->status === 'cancelado' ? 'danger' : ($pedido->status === 'entregue' ? 'success' : 'warning') }}">
                                        {{ $pedido->status_formatado }}
                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Subtotal:</th>
                                    <td>R$ {{ number_format($pedido->subtotal, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Desconto:</th>
                                    <td>R$ {{ number_format($pedido->desconto, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Frete:</th>
                                    <td>R$ {{ number_format($pedido->frete, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td><strong>R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">Cliente</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Nome:</strong> {{ $pedido->usuario->nome }}</p>
                            <p><strong>Email:</strong> {{ $pedido->usuario->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">Endereço de Entrega</h4>
                        </div>
                        <div class="card-body">
                            @if(!empty($pedido->endereco_entrega))
                                <p>{{ $pedido->endereco_entrega['logradouro'] }}
                                    , {{ $pedido->endereco_entrega['numero'] }}</p>
                                <p>{{ $pedido->endereco_entrega['bairro'] }}</p>
                                <p>{{ $pedido->endereco_entrega['cidade'] }}
                                    /{{ $pedido->endereco_entrega['estado'] }}</p>
                                <p>CEP: {{ $pedido->endereco_entrega['cep'] }}</p>
                                @if(!empty($pedido->endereco_entrega['complemento']))
                                    <p>Complemento: {{ $pedido->endereco_entrega['complemento'] }}</p>
                                @endif
                            @else
                                <p class="text-muted">Nenhum endereço de entrega registrado</p>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">Itens do Pedido</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Detalhes</th>
                                    <th>Qtd</th>
                                    <th>Preço Unit.</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pedido->itens as $item)
                                    <tr>
                                        <td>
                                            @if($item->camisaPersonalizada)
                                                Camisa Personalizada #{{ $item->camisaPersonalizada->id }}
                                            @elseif($item->tecido)
                                                Tecido: {{ $item->tecido->nome_produto }}
                                            @else
                                                Item #{{ $item->id }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->camisaPersonalizada)
                                                <small>
                                                    <strong>Modelo:</strong> {{ $item->camisaPersonalizada->genero }} - {{ $item->camisaPersonalizada->modelagem }}<br>
                                                    <strong>Manga:</strong> {{ $item->camisaPersonalizada->manga }}<br>
                                                    <strong>Tecido:</strong> {{ $item->camisaPersonalizada->tecido->nome_produto ?? 'N/A' }}
                                                </small>
                                            @elseif(isset($medidasUsuario[$pedido->usuario_id]) && $medidasUsuario[$pedido->usuario_id]->isNotEmpty())
                                                <div class="medidas-container">
                                                    <strong>Medidas Personalizadas:</strong>
                                                    <ul>
                                                        @foreach($medidasUsuario[$pedido->usuario_id] as $medida)
                                                            <li>{{ ucfirst($medida->nome) }}: {{ $medida->valor }} {{ $medida->unidade }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @elseif($item->tecido)
                                                <small>Tecido: {{ $item->tecido->nome_produto }}</small>
                                            @else
                                                <small>Sem informações detalhadas</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantidade }}</td>
                                        <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                        <td>R$ {{ number_format($item->preco_total, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if($pedido->observacoes)
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Observações</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $pedido->observacoes }}</p>
                    </div>
                </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('admin.pedidos.edit', $pedido->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Alterar Status
                </a>
                <a href="{{ route('admin.pedidos.pagamentos', $pedido->id) }}" class="btn btn-info">
                    <i class="fas fa-money-bill-wave"></i> Ver Pagamentos
                </a>
            </div>
        </div>
    </div>
@endsection
