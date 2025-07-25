@extends('adminlte::page')

@section('title', 'Gerenciar Clientes')

@section('content_header')
    <h1 class="m-0 text-dark">
        <small>Gerenciar Clientes</small>
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Clientes</h3>
            <div class="card-tools">
                <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Cliente
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table id="clientes-table" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Endereços</th>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#clientes-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                colReorder: true,
                ajax: "{{ route('admin.clientes.datatable') }}",
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.4/i18n/pt_br.json'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    {
                        data: 'nome_completo',
                        name: 'cliente.nome_completo',
                        render: function(data, type, row) {
                            return `<a href="/admin/clientes/${row.id}">${data}</a>`;
                        }
                    },
                    { data: 'email', name: 'email' },
                    {
                        data: 'cpf',
                        name: 'cliente.cpf',
                        render: function(data) {
                            return data || 'Não informado';
                        }
                    },
                    {
                        data: 'telefone',
                        name: 'cliente.telefone',
                        render: function(data) {
                            return data || 'Não informado';
                        }
                    },
                    {
                        data: 'ativo',
                        name: 'ativo',
                        render: function(data) {
                            return data ?
                                '<span class="badge bg-success">Ativo</span>' :
                                '<span class="badge bg-danger">Inativo</span>';
                        }
                    },
                    {
                        data: 'enderecos_count',
                        name: 'enderecos_count',
                        render: function(data, type, row) {
                            let html = data;
                            if (row.endereco_principal) {
                                html += ' <span class="badge badge-primary" title="Endereço principal"><i class="fas fa-home"></i></span>';
                            }
                            return html;
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        render: function(data) {
                            return `
                            <a href="/admin/clientes/${data}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/clientes/${data}/edit" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/admin/clientes/${data}" method="POST" style="display:inline">
                                @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"top"Bf>rt<"bottom"lip><"clear">',
                buttons: [
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }
                ]
            });
        });
    </script>
@stop
