<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Pagamento;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {

        $stats = [
            'users' => [
                'total' => Usuario::count(),
                'active' => Usuario::where('ativo', 1)->count(),
                'new' => Usuario::where('data_criacao', '>=', Carbon::now()->subDays(30))->count(),
            ],
            'orders' => [
                'total' => Pedido::count(),
                'month' => Pedido::where('data_pedido', '>=', Carbon::now()->subDays(30))->count(),
                'revenue' => Pedido::sum('total'),
                'month_revenue' => Pedido::where('data_pedido', '>=', Carbon::now()->subDays(30))->sum('total'),
            ],
            'user_types' => Usuario::selectRaw('tipo_usuario, count(*) as total')
                ->groupBy('tipo_usuario')
                ->get()
                ->pluck('total', 'tipo_usuario'),
            'order_statuses' => Pedido::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get(),
            'payment_methods' => Pagamento::selectRaw('tipo_pagamento, count(*) as total')
                ->groupBy('tipo_pagamento')
                ->get(),
        ];

        // Estatísticas de pedidos
        $latestOrders = Pedido::with(['usuario.cliente'])
            ->latest()
            ->take(5)
            ->get();

        $monthlyRevenue = Pedido::selectRaw('
            YEAR(data_pedido) as year,
            MONTH(data_pedido) as month,
            SUM(total) as total
        ')
            ->where('data_pedido', '>=', now()->subMonths(12))
            ->groupByRaw('YEAR(data_pedido), MONTH(data_pedido)')
            ->orderByRaw('YEAR(data_pedido) ASC, MONTH(data_pedido) ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'month' => $item->month,
                    'total' => $item->total,
                    'label' => Carbon::createFromDate($item->year, $item->month, 1)->format('m/Y'),
                    'year_month' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT)
                ];
            });

        return view('pages.dashboard.index', compact('stats', 'latestOrders',  'monthlyRevenue'));
    }
}
