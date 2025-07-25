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

@section('style')
    <style>
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875em;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
    </style>
@endsection


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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                @foreach($statusOptions as $key => $option)
                                    <option value="{{ $key }}" {{ old('status', $pedido->status) == $key ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6" id="codigoRastreioField"
                         style="display: {{ old('status', $pedido->status) === 'enviado' ? 'block' : 'none' }};">
                        <div class="form-group">
                            <label for="codigo_rastreio">Código de Rastreio (Correios) *</label>
                            <input type="text" name="codigo_rastreio" id="codigo_rastreio"
                                   class="form-control @error('codigo_rastreio') is-invalid @enderror"
                                   value="{{ old('codigo_rastreio', $pedido->codigo_rastreio) }}"
                                   placeholder="Ex: PL123456789BR"
                                {{ old('status', $pedido->status) === 'enviado' ? 'required' : '' }}>
                            <small class="text-muted">Informe o código de rastreamento dos Correios</small>
                            @error('codigo_rastreio')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="observacoes">Observações</label>
                    <textarea name="observacoes" id="observacoes" rows="3"
                              class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $pedido->observacoes) }}</textarea>
                    @error('observacoes')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
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

@section('js')
    <script>
        $(document).ready(function() {
            function toggleCodigoRastreio() {
                if ($('#status').val() === 'enviado') {
                    $('#codigoRastreioField').show();
                    $('#codigo_rastreio').prop('required', true);
                } else {
                    $('#codigoRastreioField').hide();
                    $('#codigo_rastreio').prop('required', false);
                }
            }

            toggleCodigoRastreio();

            $('#status').change(toggleCodigoRastreio);
        });
    </script>
@endsection
