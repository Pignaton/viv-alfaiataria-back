@extends('adminlte::page')

@section('title', 'Gerenciar Pedidos')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/pedidos">Gerenciar Pedidos</a></li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pagamentos do Pedido #{{ $pedido->codigo }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Voltar ao Pedido
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Transação</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pedido->pagamentos as $pagamento)
                        <tr>
                            <td>{{ $pagamento->id }}</td>
                            <td>{{ $pagamento->tipo_formatado }}</td>
                            <td>R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
                            <td>
                            <span class="badge badge-{{ $pagamento->status === 'aprovado' ? 'success' : ($pagamento->status === 'recusado' ? 'danger' : 'warning') }}">
                                {{ $pagamento->status_formatado }}
                            </span>
                            </td>
                            <td>{{ $pagamento->data_criacao_formatada }}</td>
                            <td>{{ $pagamento->codigo_transacao ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalPagamento{{ $pagamento->id }}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                @if($pagamento->podeReembolsar())
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalReembolso{{ $pagamento->id }}">
                                        <i class="fas fa-undo"></i> Reembolsar
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Editar Pagamento -->
                        <div class="modal fade" id="modalPagamento{{ $pagamento->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Pagamento #{{ $pagamento->id }}</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.pagamentos.update', $pagamento->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    @foreach(App\Models\Pagamento::STATUS as $key => $status)
                                                        <option value="{{ $key }}" {{ $pagamento->status == $key ? 'selected' : '' }}>
                                                            {{ $status }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Código de Transação</label>
                                                <input type="text" name="codigo_transacao" class="form-control"
                                                       value="{{ $pagamento->codigo_transacao }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Solicitar Reembolso -->
                        @if($pagamento->podeReembolsar())
                            <div class="modal fade" id="modalReembolso{{ $pagamento->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Solicitar Reembolso</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('admin.pagamentos.reembolsar', $pagamento->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Valor do Reembolso</label>
                                                    <input type="number" name="valor" class="form-control"
                                                           min="0.01" max="{{ $pagamento->valor }}" step="0.01"
                                                           value="{{ $pagamento->valor }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Motivo</label>
                                                    <input type="text" name="motivo" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Método de Estorno</label>
                                                    <select name="metodo_estorno" class="form-control" required>
                                                        @foreach(App\Models\Reembolso::METODOS_ESTORNO as $key => $metodo)
                                                            <option value="{{ $key }}">{{ $metodo }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning">Solicitar Reembolso</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>

            <h4 class="mt-4">Reembolsos</h4>
            @if($pedido->pagamentos->flatMap->reembolsos->count() > 0)
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pagamento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Método</th>
                        <th>Solicitado em</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pedido->pagamentos as $pagamento)
                        @foreach($pagamento->reembolsos as $reembolso)
                            <tr>
                                <td>{{ $reembolso->id }}</td>
                                <td>#{{ $pagamento->id }}</td>
                                <td>R$ {{ number_format($reembolso->valor, 2, ',', '.') }}</td>
                                <td>
                                <span class="badge badge-{{ $reembolso->status === 'processado' ? 'success' : ($reembolso->status === 'falha' ? 'danger' : 'warning') }}">
                                    {{ $reembolso->status_formatado }}
                                </span>
                                </td>
                                <td>{{ $reembolso->metodo_estorno_formatado }}</td>
                                <td>{{ $reembolso->data_solicitacao->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Nenhum reembolso registrado para este pedido.</p>
            @endif
        </div>
    </div>
@endsection
