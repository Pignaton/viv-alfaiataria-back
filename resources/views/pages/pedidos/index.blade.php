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
            <h3 class="card-title">Lista de Pedidos</h3>
            <div class="card-tools">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        Filtrar por Status
                    </button>
                    <div class="dropdown-menu">
                        @foreach(App\Models\Pedido::STATUS as $key => $status)
                            <a class="dropdown-item filter-status" data-status="{{ $key }}" href="#">{{ $status }}</a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item filter-status" data-status="all" href="#">Todos os Pedidos</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table id="pedidos-table" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Itens</th>
                    <th>Total</th>
                    <th>Status</th>
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
        var table = $('#pedidos-table').DataTable({
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.pedidos.datatable') }}",
                data: function (d) {
                    d.status = $('.filter-status.active').data('status') || 'all';
                }
            },
            columns: [
                { data: 'codigo', name: 'codigo' },
                {
                    data: 'cliente',
                    name: 'cliente',
                    render: function(data) {
                        console.log(data);
                        return data || 'N/A';
                    }
                },
                { data: 'data_pedido', name: 'data_pedido' },
                {
                    data: 'itens',
                    name: 'itens',
                    render: function(data) {
                        return data.reduce((sum, item) => sum + item.quantidade, 0);
                    },
                    orderable: false
                },
                { data: 'total', name: 'total' },
                { data: 'status', name: 'status' },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.4/i18n/pt_br.json'
            },
            dom: '<"top"Bf>rt<"bottom"lip><"clear">',
            buttons: [
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    className: 'btn btn-default',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                }
            ]
        });

        // Filtro por status
        $('.filter-status').click(function(e) {
            e.preventDefault();
            $('.filter-status').removeClass('active');
            $(this).addClass('active');
            table.ajax.reload();
        });
    });
</script>
@stop
