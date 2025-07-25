<?php

namespace App\Http\Controllers;

use App\Mail\PedidoEnviadoMail;
use App\Models\Medida;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Pagamento;
use App\Models\Reembolso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use App\Rules\CodigoRastreio;


class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['usuario', 'itens'])
            ->latest('data_pedido')
            ->paginate(10);

        return view('pages.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load([
            'usuario',
            'itens.tecido',
            'itens.camisaPersonalizada.tecido'
        ]);

        $medidasUsuario = Medida::where('usuario_id', $pedido->usuario_id)->get()->groupBy('usuario_id');

        return view('pages.pedidos.show', compact('pedido', 'medidasUsuario'));
    }

    public function edit(Pedido $pedido)
    {
        $pedido->load(['itens']);
        $statusOptions = Pedido::STATUS;
        return view('pages.pedidos.edit', compact('pedido', 'statusOptions'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $validStatus = array_keys(Pedido::STATUS);

        $request->validate([
            'status' => 'required|in:' . implode(',', $validStatus),
            'codigo_rastreio' => [
                'nullable',
                'string',
                'max:13',
                new CodigoRastreio(),
                function ($attribute, $value, $fail) use ($request, $pedido) {
                    if ($request->status === 'enviado' && empty($value)) {
                        $fail('O código de rastreio é obrigatório quando o status é "enviado".');
                    }
                }
            ],
            'observacoes' => 'nullable|string'
        ]);

        $updateData = [
            'status' => $request->status,
            'observacoes' => $request->observacoes
        ];

        if ($request->status === 'enviado' && $request->filled('codigo_rastreio')) {
            $updateData['codigo_rastreio'] = $request->codigo_rastreio;
            $updateData['data_envio'] = now();

            if ($pedido->status === 'enviado') {

                try {
                  Mail::to($pedido->usuario->email)
                        ->send(new PedidoEnviadoMail($pedido, $pedido->usuario));

                    Log::info("Email de pedido enviado para {$pedido->usuario->email}");
                } catch (\Exception $e) {
                    Log::error("Falha ao enviar email: " . $e->getMessage());
                }
            }
        } elseif ($request->status !== 'enviado') {
            $updateData['codigo_rastreio'] = null;
            $updateData['data_envio'] = null;
        }


        $pedido->update($updateData);

        return redirect()->route('admin.pedidos.show', $pedido->id)
            ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Pedido removido com sucesso!');
    }

    public function filtrarPorStatus($status)
    {
        $pedidos = Pedido::filtrarPorStatus($status)
            ->with(['usuario'])
            ->latest('data_pedido')
            ->paginate(10);

        return view('pages.pedidos.index', compact('pedidos'));
    }

    public function pagamentos(Pedido $pedido)
    {
        $pedido->load(['pagamentos' => function ($query) {
            $query->with(['pix', 'boleto', 'reembolsos'])->latest('data_criacao');
        }]);

        return view('pages.pedidos.pagamentos', compact('pedido'));
    }

    public function atualizarPagamento(Request $request, Pagamento $pagamento)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Pagamento::STATUS)),
            'codigo_transacao' => 'nullable|string|max:100'
        ]);

        $pagamento->update([
            'status' => $request->status,
            'codigo_transacao' => $request->codigo_transacao
        ]);

        return redirect()->back()
            ->with('success', 'Status do pagamento atualizado com sucesso!');
    }

    public function solicitarReembolso(Request $request, Pagamento $pagamento)
    {
        if (!$pagamento->podeReembolsar()) {
            return redirect()->back()
                ->with('error', 'Este pagamento não pode ser reembolsado no momento.');
        }

        $request->validate([
            'valor' => 'required|numeric|min:0.01|max:' . $pagamento->valor,
            'motivo' => 'required|string|max:100',
            'metodo_estorno' => 'required|in:original,credito_loja,pix'
        ]);

        Reembolso::create([
            'pagamento_id' => $pagamento->id,
            'valor' => $request->valor,
            'motivo' => $request->motivo,
            'status' => 'pendente',
            'metodo_estorno' => $request->metodo_estorno
        ]);

        $pagamento->update(['status' => 'reembolsado']);

        return redirect()->back()
            ->with('success', 'Solicitação de reembolso criada com sucesso!');
    }


    public function datatable(Request $request)
    {
        $query = Pedido::with(['usuario', 'usuario.cliente', 'itens'])
            ->select('pedido.*');


        // Filtro por status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('cliente', function (Pedido $pedido) {
                return $pedido->usuario->cliente->nome_completo ?? null;
            })
            ->addColumn('total_itens', function (Pedido $pedido) {
                return $pedido->itens->sum('quantidade');
            })
            ->editColumn('data_pedido', function (Pedido $pedido) {
                return $pedido->data_pedido->format('d/m/Y H:i');
            })
            ->editColumn('total', function (Pedido $pedido) {
                return 'R$ ' . number_format($pedido->total, 2, ',', '.');
            })
            ->editColumn('status', function (Pedido $pedido) {
                return '<span class="badge badge-' . badgeStatus($pedido->status) . '">' . $pedido->status_formatado . '</span>';
            })
            ->addColumn('actions', function (Pedido $pedido) {
                return view('pages.pedidos.actions', compact('pedido'))->render();
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }
}
