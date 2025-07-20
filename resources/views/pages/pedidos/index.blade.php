@extends('adminlte::page')

@section('title', 'Gerenciar Pedidos')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/pedidos">Gerenciar Pedidos</a></li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Pedidos</h3>
            <div class="card-tools">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        Filtrar por Status
                    </button>
                    <div class="dropdown-menu">
                        @foreach(App\Models\Pedido::STATUS as $key => $status)
                            <a class="dropdown-item" href="{{ route('admin.pedidos.filtrar', $key) }}">{{ $status }}</a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('admin.pedidos.index') }}">Todos os Pedidos</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Itens</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->codigo }}</td>
                        <td>{{ $pedido->usuario->nome ?? 'N/A' }}</td>
                        <td>{{ $pedido->data_pedido_formatada }}</td>
                        <td>{{ $pedido->itens->sum('quantidade') }}</td>
                        <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                        <td>
                        <span
                            class="badge badge-{{ $pedido->status === 'cancelado' ? 'danger' : ($pedido->status === 'entregue' ? 'success' : 'warning') }}">
                            {{ $pedido->status_formatado }}
                        </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.pedidos.destroy', $pedido->id) }}" method="POST"
                                  style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $pedidos->links() }}
            </div>
        </div>
    </div>
@endsection
