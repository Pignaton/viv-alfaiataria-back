@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalhes do Cliente</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informações da Conta</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{ $cliente->id }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $cliente->email }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($cliente->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Data de Criação</th>
                            <td>{{ $cliente->data_criacao->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Informações Pessoais</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nome Completo</th>
                            <td>{{ $cliente->cliente->nome_completo }}</td>
                        </tr>
                        <tr>
                            <th>CPF</th>
                            <td>{{ $cliente->cliente->cpf ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <th>Telefone</th>
                            <td>{{ $cliente->cliente->telefone ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <th>Data de Nascimento</th>
                            <td>{{ $cliente->cliente->data_nascimento ? $cliente->cliente->data_nascimento->format('d/m/Y') : 'Não informada' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-default">
                    Voltar
                </a>
            </div>
        </div>
    </div>
@endsection
