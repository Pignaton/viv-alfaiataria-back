@extends('adminlte::page')

@section('title', 'Gerenciar Tecidos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Gerenciar Tecidos</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.tecidos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Tecido
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Tecidos</h3>
        </div>
        <div class="card-body">
            <table id="tecidos-table" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Composição</th>
                    <th>Preço</th>
                    <th>Origem</th>
                    <th>Cadastrado em</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css">
    <style>
        .img-table-thumb {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tecidos-table').DataTable({
                processing: true,
                serverSide: true,
                order: [[1, 'asc']],
                responsive: true,
                ajax: "{{ route('admin.tecidos.datatable') }}",
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.4/i18n/pt_br.json'
                },
                columns: [
                    {
                        data: 'imagem_url',
                        name: 'imagem_url',
                        render: function (data) {
                            return data ?
                                `<img src="${data}" class="img-table-thumb" alt="Imagem do tecido">` :
                                '<span class="text-muted">Sem imagem</span>';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nome_produto',
                        name: 'nome_produto',
                        render: function (data, type, row) {
                            return `<a href="/admin/tecidos/${row.id}">${data}</a>`;
                        }
                    },
                    {data: 'composicao', name: 'composicao'},
                    {
                        data: 'preco',
                        name: 'preco',
                        render: function (data, type, row) {
                            if (row.preco_promocional > 0) {
                                return `<span class="text-danger"><del>R$ ${parseFloat(data).toFixed(2).replace('.', ',')}</del></span><br>
                                    <span class="text-success">R$ ${parseFloat(row.preco_promocional).toFixed(2).replace('.', ',')}</span>`;
                            }
                            return `R$ ${parseFloat(data).toFixed(2).replace('.', ',')}`;
                        }
                    },
                    {data: 'origem', name: 'origem'},
                    {
                        data: 'data_cadastro',
                        name: 'data_cadastro',
                        render: function (data) {
                            return new Date(data).toLocaleDateString('pt-BR');
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@stop
