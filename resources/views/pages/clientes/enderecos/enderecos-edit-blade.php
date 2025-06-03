@extends('admin.layout')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Endereço</h3>
        <div class="card-tools">
            <a href="{{ route('admin.clientes.enderecos') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.clientes.enderecos.update', $endereco->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="apelido">Apelido (Opcional)</label>
                        <input type="text" name="apelido" id="apelido" class="form-control"
                               value="{{ old('apelido', $endereco->apelido) }}" maxlength="50">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cep">CEP *</label>
                        <input type="text" name="cep" id="cep" class="form-control"
                               value="{{ old('cep', $endereco->cep) }}" required maxlength="9">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="logradouro">Logradouro *</label>
                        <input type="text" name="logradouro" id="logradouro" class="form-control"
                               value="{{ old('logradouro', $endereco->logradouro) }}" required maxlength="100">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="numero">Número *</label>
                        <input type="text" name="numero" id="numero" class="form-control"
                               value="{{ old('numero', $endereco->numero) }}" required maxlength="10">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" name="complemento" id="complemento" class="form-control"
                               value="{{ old('complemento', $endereco->complemento) }}" maxlength="50">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="bairro">Bairro *</label>
                        <input type="text" name="bairro" id="bairro" class="form-control"
                               value="{{ old('bairro', $endereco->bairro) }}" required maxlength="50">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="cidade">Cidade *</label>
                        <input type="text" name="cidade" id="cidade" class="form-control"
                               value="{{ old('cidade', $endereco->cidade) }}" required maxlength="50">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="estado">Estado *</label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="">UF</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" {{ old('estado', $endereco->estado) == $uf ? 'selected' : '' }}>
                            {{ $uf }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="principal" name="principal"
                                   value="1" {{ old('principal', $endereco->principal) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="principal">Definir como endereço principal</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="entrega" name="entrega"
                                   value="1" {{ old('entrega', $endereco->entrega) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="entrega">Usar para entrega</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Endereço
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
    });
</script>
@endsection
