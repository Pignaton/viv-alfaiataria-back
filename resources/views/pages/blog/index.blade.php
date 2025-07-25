@extends('adminlte::page')

@section('title', 'Gerenciar Blog')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Gerenciar Blog</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Post
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Posts do Blog</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table id="blog-table" class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Publicação</th>
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
            max-width: 80px;
            max-height: 60px;
            border-radius: 4px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#blog-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.blog.datatable') }}",
                initComplete: function() {
                    this.api().columns([3,4]).every(function() {
                        var column = this;
                        var select = $('<select><option value="">Todos</option></select>')
                            .appendTo($(column.header()))
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^'+val+'$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d) {
                            select.append('<option value="'+d+'">'+d+'</option>');
                        });
                    });
                },
                columns: [
                    {
                        data: 'imagem_url',
                        name: 'imagem_url',
                        render: function(data) {
                            return data ?
                                `<img src="${data}" class="img-table-thumb" alt="Imagem do post">` :
                                '<span class="text-muted">Sem imagem</span>';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'titulo',
                        name: 'titulo',
                        render: function(data, type, row) {
                            return `<a href="/admin/blog/${row.id}">${data}</a>`;
                        }
                    },
                    {
                        data: 'usuario.nome',
                        name: 'usuario.nome',
                        render: function(data) {
                            return data || 'N/A';
                        }
                    },
                    {
                        data: 'tipo_conteudo',
                        name: 'tipo_conteudo',
                        render: function(data) {
                            switch(data) {
                                case 'video': return '<span class="badge bg-info">Vídeo</span>';
                                case 'galeria': return '<span class="badge bg-success">Galeria</span>';
                                default: return '<span class="badge bg-secondary">Padrão</span>';
                            }
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (row.publicado && new Date(row.data_publicacao) <= new Date()) {
                                return '<span class="badge bg-success">Publicado</span>';
                            } else if (row.publicado) {
                                return '<span class="badge bg-warning">Agendado</span>';
                            }
                            return '<span class="badge bg-secondary">Rascunho</span>';
                        }
                    },
                    {
                        data: 'data_publicacao',
                        name: 'data_publicacao',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('pt-BR') : 'Não publicado';
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        render: function(data) {
                            return `

                            <a href="/admin/blog/${data}" class="btn btn-sm btn-info" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>&nbsp;
                            <a href="/admin/blog/${data}/edit" class="btn btn-sm btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>&nbsp;
                            <form action="/admin/blog/${data}" method="POST" style="display:inline">
                                @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

`;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.4/i18n/pt_br.json'
                },
                dom: '<"top"Bf>rt<"bottom"lip><"clear">',
                /*buttons: [
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    }
                ]*/
            });
        });
    </script>
@stop
