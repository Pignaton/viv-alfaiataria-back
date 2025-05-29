@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/clientes">Gerenciar Cliente</a></li>
            <li class="breadcrumb-item active">Adicionar Cliente</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cadastrar Novo Cliente</h3>
            <div class="card-tools">
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.clientes.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_completo">Nome Completo *</label>
                            <input type="text" name="nome_completo" id="nome_completo"
                                   class="form-control @error('nome_completo') is-invalid @enderror"
                                   value="{{ old('nome_completo') }}" required>
                            @error('nome_completo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="senha">Senha *</label>
                            <input type="password" name="senha" id="senha"
                                   class="form-control @error('senha') is-invalid @enderror" required>
                            @error('senha')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="senha_confirmation">Confirmar Senha *</label>
                            <input type="password" name="senha_confirmation" id="senha_confirmation"
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" name="cpf" id="cpf"
                                   class="form-control @error('cpf') is-invalid @enderror"
                                   value="{{ old('cpf') }}"
                                   placeholder="000.000.000-00"
                                   data-inputmask="'mask': '999.999.999-99'"
                                   data-mask>
                            @error('cpf')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" name="telefone" id="telefone"
                                   class="form-control @error('telefone') is-invalid @enderror"
                                   value="{{ old('telefone') }}"
                                   placeholder="(00) 00000-0000"
                                   data-inputmask="'mask': ['(99) 9999-9999', '(99) 99999-9999']"
                                   data-mask>
                            @error('telefone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="data_nascimento">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" id="data_nascimento"
                                   class="form-control @error('data_nascimento') is-invalid @enderror"
                                   value="{{ old('data_nascimento') }}">
                            @error('data_nascimento')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="ativo" name="ativo"
                                       value="1" {{ old('ativo', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="ativo">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cadastrar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <!-- InputMask -->
    <script src="{{ asset('vendor/adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            // Aplicar máscaras
            $('[data-mask]').inputmask();
        });
    </script>
@endsection
