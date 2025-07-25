@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <!-- Usuários -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['users']['total'] }}</h3>
                        <p>Usuários Registrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.clientes.index') }}" class="small-box-footer">
                        Mais info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Pedidos -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['orders']['total'] }}</h3>
                        <p>Pedidos Totais</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('admin.pedidos.index') }}" class="small-box-footer">
                        Mais info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Receita -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>R$ {{ number_format($stats['orders']['revenue'], 2, ',', '.') }}</h3>
                        <p>Receita Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        Mais info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Novos usuários -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['users']['new'] }}</h3>
                        <p>Novos Usuários (30 dias)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <a href="{{ route('admin.clientes.index') }}" class="small-box-footer">
                        Mais info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Distribuição de Tipos de Usuário</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="userTypesChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Status dos Pedidos</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Receita Mensal (Últimos 12 Meses)</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="monthlyRevenueChart"
                                    style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Pedidos -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Últimos Pedidos</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($latestOrders as $order)
                                <tr>
                                    <td>{{ $order->codigo }}</td>
                                    <td>{{ $order->user->cliente->nome_completo ?? 'Cliente não encontrado' }}</td>
                                    <td>{{ $order->data_pedido->format('d/m/Y H:i') }}</td>
                                    <td>R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                    <td>
                                    <span class="badge bg-{{badgeStatus($order->status)}}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pedidos.show', $order->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function () {
            // Tipos de Usuário
            var userTypesChart = new Chart(document.getElementById('userTypesChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($stats['user_types']->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($stats['user_types']->values()) !!},
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                }
            });

            // Status dos Pedidos
            var orderStatusChart = new Chart(document.getElementById('orderStatusChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($stats['order_statuses']->pluck('status')) !!},
                    datasets: [{
                        label: 'Pedidos',
                        data: {!! json_encode($stats['order_statuses']->pluck('total')) !!},
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(0, 123, 255, 0.8)'
                        ]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });


            // Gráfico de Receita Mensal
            var monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            var monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyRevenue->pluck('label')) !!},
                    datasets: [{
                        label: 'Receita Mensal',
                        data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
                        backgroundColor: 'rgba(23, 162, 184, 0.2)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                        fill: true
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'R$ ' + tooltipItem.yLabel.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                                return label;
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
