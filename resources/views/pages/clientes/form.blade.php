@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ isset($client) ? 'Editar' : 'Criar' }} Cliente</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ isset($client) ? route('admin.clientes.update', $client->id) : route('admin.clientes.store') }}">
                @csrf
                @if(isset($client))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="{{ old('name', $client->name ?? '') }}" required>
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email', $client->email ?? '') }}" required>
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                           value="{{ old('phone', $client->phone ?? '') }}">
                </div>

                <div class="form-group">
                    <label for="address">Endereço</label>
                    <textarea class="form-control" id="address" name="address">{{ old('address', $client->address ?? '') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($client) ? 'Atualizar' : 'Salvar' }}
                </button>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-default">Cancelar</a>
            </form>
        </div>
    </div>
@endsection
