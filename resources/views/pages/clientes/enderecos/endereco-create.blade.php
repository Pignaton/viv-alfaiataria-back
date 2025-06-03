@extends('admin.layout')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Adicionar Novo Endereço</h3>
            <div class="card-tools">
                <a href="{{ route('admin.clientes.enderecos') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.clientes.enderecos.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apelido">Apelido (Opcional)</label>
                            <input type="text" name="apelido" id="apelido" class="form-control"
                                   placeholder="Ex: Casa, Trabalho" maxlength="50">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cep">CEP *</label>
                            <div class="input-group">
                                <input type="text" name="cep" id="cep" class="form-control"
                                       required maxlength="9" placeholder="00000-000">
                                <div class="input-group-append">
                                    <button type="button" id="buscar-cep" class="btn btn-info">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="logradouro">Logradouro *</label>
                            <input type="text" name="logradouro" id="logradouro" class="form-control" required maxlength="100">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="numero">Número *</label>
                            <input type="text" name="numero" id="numero" class="form-control" required maxlength="10">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="complemento">Complemento</label>
                            <input type="text" name="complemento" id="complemento" class="form-control" maxlength="50">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="bairro">Bairro *</label>
                            <input type="text" name="bairro" id="bairro" class="form-control" required maxlength="50">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="cidade">Cidade *</label>
                            <input type="text" name="cidade" id="cidade" class="form-control" required maxlength="50">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="estado">Estado *</label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="">UF</option>
                                @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                    <option value="{{ $uf }}">{{ $uf }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="principal" name="principal" value="1">
                                <label class="custom-control-label" for="principal">Definir como endereço principal</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="entrega" name="entrega" value="1" checked>
                                <label class="custom-control-label" for="entrega">Usar para entrega</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Endereço
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#cep').inputmask('99999-999');

            $('#buscar-cep').click(function() {
                const cep = $('#cep').val().replace(/\D/g, '');
                if (cep.length === 8) {
                    $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                        if (!data.erro) {
                            $('#logradouro').val(data.logradouro);
                            $('#bairro').val(data.bairro);
                            $('#cidade').val(data.localidade);
                            $('#estado').val(data.uf);
                            $('#complemento').val(data.complemento);
                            $('#numero').focus();
                        } else {
                            alert('CEP não encontrado');
                        }
                    }).fail(function() {
                        alert('Erro ao buscar CEP');
                    });
                } else {
                    alert('Informe um CEP válido');
                }
            });
        });
    </script>
@endsection
