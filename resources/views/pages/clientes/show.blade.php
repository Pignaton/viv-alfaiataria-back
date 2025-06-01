@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalhes do Cliente</h3>
            <div class="card-tools">
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Informações Pessoais</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nome:</th>
                            <td>{{ $cliente->cliente->nome_completo }}</td>
                        </tr>
                        <tr>
                            <th>CPF:</th>
                            <td>{{ $cliente->cliente->cpf ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <th>Telefone:</th>
                            <td>{{ $cliente->cliente->telefone ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <th>E-mail:</th>
                            <td>{{ $cliente->email }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Endereços</h4>

                    @if($cliente->enderecos->isEmpty())
                        <div class="alert alert-info">Nenhum endereço cadastrado</div>
                    @else
                        <div class="list-group">
                            @foreach($cliente->enderecos as $endereco)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $endereco->apelido ?? 'Endereço #'.($loop->index+1) }}</strong>
                                            @if($endereco->principal)
                                                <span class="badge badge-primary ml-2">Principal</span>
                                            @endif
                                            @if($endereco->entrega)
                                                <span class="badge badge-success ml-2">Entrega</span>
                                            @endif
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary" data-toggle="collapse"
                                                    data-target="#endereco{{ $endereco->id }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="endereco{{ $endereco->id }}" class="collapse mt-2">
                                        <p>
                                            {{ $endereco->logradouro }}, {{ $endereco->numero }}<br>
                                            @if($endereco->complemento)
                                                {{ $endereco->complemento }}<br>
                                            @endif
                                            {{ $endereco->bairro }} - {{ $endereco->cidade }}/{{ $endereco->estado }}
                                            <br>
                                            CEP: {{ substr($endereco->cep, 0, 5) }}-{{ substr($endereco->cep, 5) }}
                                        </p>
                                        <div class="text-right">
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmarExclusao('{{ route('admin.enderecos.destroy', $endereco->id) }}')">
                                                <i class="fas fa-trash"></i> Remover
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <button class="btn btn-primary mt-3" data-toggle="modal" data-target="#modalAdicionarEndereco">
                        <i class="fas fa-plus"></i> Adicionar Endereço
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Endereço -->
    <div class="modal fade" id="modalAdicionarEndereco" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Endereço</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.enderecos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="usuario_id" value="{{ $cliente->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Apelido (Opcional)</label>
                            <input type="text" name="apelido" class="form-control" placeholder="Ex: Casa, Trabalho">
                        </div>
                        <div class="form-group">
                            <label>CEP *</label>
                            <input type="text" name="cep" class="form-control cep-mask" required>
                        </div>
                        <div class="form-group">
                            <label>Logradouro *</label>
                            <input type="text" name="logradouro" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Número *</label>
                                    <input type="text" name="numero" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Complemento</label>
                                    <input type="text" name="complemento" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Bairro *</label>
                            <input type="text" name="bairro" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Cidade *</label>
                                    <input type="text" name="cidade" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Estado *</label>
                                    <select name="estado" class="form-control" required>
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
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="principal" name="principal"
                                           value="1">
                                    <label class="form-check-label" for="principal">Endereço Principal</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="entrega" name="entrega"
                                           value="1" checked>
                                    <label class="form-check-label" for="entrega">Usar para Entrega</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Endereço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="form-excluir-endereco" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    @section('js')
        <script>
            $('.cep-mask').inputmask('99999-999');

            function confirmarExclusao(url) {
                if (confirm('Tem certeza que deseja remover este endereço?')) {
                    const form = document.getElementById('form-excluir-endereco');
                    form.action = url;
                    form.submit();
                }
            }
        </script>
    @endsection
@endsection
