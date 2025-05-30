@extends('adminlte::page')

@section('title', 'Gerenciar Pedidos')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/pedidos">Gerenciar Pedidos</a></li>
            <li class="breadcrumb-item active">Editar Pedido</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Alterar Status do Pedido #{{ $pedido->codigo }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select name="status" id="status" class="form-control" required>
                                @foreach($statusOptions as $key => $option)
                                    <option value="{{ $key }}" {{ $pedido->status == $key ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="observacoes">Observações</label>
                    <textarea name="observacoes" id="observacoes" rows="3" class="form-control">{{ old('observacoes', $pedido->observacoes) }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Atualizar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
