@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
    <h1 class="m-0 text-dark">
        <small>Gerenciar Clientes</small>
    </h1>
@stop

@php
    function formatPhone($phone) {
        $phone = preg_replace('/\D/', '', $phone ?? '');
        if (strlen($phone) === 11) {
            return '('.substr($phone, 0, 2).') '.substr($phone, 2, 5).'-'.substr($phone, 7);
        } elseif (strlen($phone) === 10) {
            return '('.substr($phone, 0, 2).') '.substr($phone, 2, 4).'-'.substr($phone, 6);
        }
        return $phone;
    }
@endphp

@php
    $enderecoCompleto = '';
    if ($cliente->enderecos && $cliente->enderecos->count() > 0) {
        $endereco = $cliente->enderecos->first();
        $enderecoCompleto = trim(
            ($endereco->logradouro ?? '') . ', ' .
            ($endereco->numero ?? '')  . ', ' .
            ($endereco->complemento ?? ''). ' - ' .
            ($endereco->bairro ?? '') . ', ' .
            ($endereco->cidade ?? '') . '/' .
            ($endereco->estado ?? '')
        );
    }
@endphp


@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ isset($cliente) ? 'Editar' : 'Criar' }} Cliente</h3>
        </div>
        <div class="card-body">
            <form method="POST"
                  action="{{ isset($cliente) ? route('admin.clientes.update', $cliente->id) : route('admin.clientes.store') }}">
                @csrf
                @if(isset($cliente))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="ativo" name="ativo"
                               value="1" {{ old('ativo', $cliente->ativo ?? 1) == 1 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="ativo">Ativo</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nome_completo">Nome</label>
                    <input type="text" class="form-control" id="name" name="nome_completo"
                           value="{{ old('nome_completo', $cliente->cliente->nome_completo ?? '') }}" required>
                    @error('nome_completo')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email', $cliente->email ?? '') }}" required>
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone"
                           value="{{ old('telefone', formatPhone($cliente->cliente->telefone ?? '')) }}">
                    @error('telefone')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <textarea class="form-control" id="endereco"
                              name="endereco">{{ old('endereco', $enderecoCompleto) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($cliente) ? 'Atualizar' : 'Salvar' }}
                </button>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-default">Cancelar</a>
            </form>
        </div>
    </div>
@endsection
