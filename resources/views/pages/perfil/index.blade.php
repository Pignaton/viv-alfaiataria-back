@extends('adminlte::page')

@section('title', 'Gerenciar Perfil')

@section('content')
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="#dados" data-toggle="tab">
                        <i class="fas fa-user"></i> Dados Pessoais
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#senha" data-toggle="tab">
                        <i class="fas fa-lock"></i> Alterar Senha
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#sessoes" data-toggle="tab">
                        <i class="fas fa-desktop"></i> Sessões Ativas
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="dados">
                    <form action="{{ route('admin.perfil.atualizar') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome Completo</label>
                                    <input type="text" name="nome" id="nome"
                                           class="form-control @error('nome') is-invalid @enderror"
                                           value="{{ old('nome', $usuario->nome) }}" required>
                                    @error('nome')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" name="email" id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $usuario->email) }}" required>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo de Usuário</label>
                                    <input type="text" class="form-control"
                                           value="{{ ucfirst($usuario->tipo_usuario) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nível de Acesso</label>
                                    <input type="text" class="form-control"
                                           value="{{ ucfirst($usuario->nivel_acesso) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <input type="text" class="form-control"
                                           value="{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Dados
                            </button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane" id="senha">
                    <form action="{{ route('admin.perfil.alterar-senha') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="senha_atual">Senha Atual</label>
                            <input type="password" name="senha_atual" id="senha_atual"
                                   class="form-control @error('senha_atual') is-invalid @enderror" required>
                            @error('senha_atual')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nova_senha">Nova Senha</label>
                            <input type="password" name="nova_senha" id="nova_senha"
                                   class="form-control @error('nova_senha') is-invalid @enderror" required>
                            @error('nova_senha')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <small class="form-text text-muted">
                                Mínimo 8 caracteres, incluindo maiúsculas, minúsculas, números e símbolos
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="nova_senha_confirmation">Confirmar Nova Senha</label>
                            <input type="password" name="nova_senha_confirmation" id="nova_senha_confirmation"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane" id="sessoes">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Dispositivo</th>
                                <th>IP</th>
                                <th>Última Atividade</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($usuario->tokensAcesso()->where('utilizado', false)->where('expiracao', '>', now())->latest()->get() as $token)
                                <tr>
                                    <td>{{ $token->user_agent ? Str::limit($token->user_agent, 50) : 'Desconhecido' }}</td>
                                    <td>{{ $token->ip_origem ?? 'N/A' }}</td>
                                    <td>{{ $token->data_criacao->diffForHumans() }}</td>
                                    <td>
                                        <form action="{{ route('admin.perfil.revogar-sessao', $token->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Revogar este acesso?')">
                                                <i class="fas fa-ban"></i> Revogar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
