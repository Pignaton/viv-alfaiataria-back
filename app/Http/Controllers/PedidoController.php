<?php

namespace App\Http\Controllers;

use App\Models\Medida;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Pagamento;
use App\Models\Reembolso;
use Illuminate\Http\Request;

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
            'observacoes' => 'nullable|string'
        ]);

        $pedido->update([
            'status' => $request->status,
            'observacoes' => $request->observacoes
        ]);

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
        $pedido->load(['pagamentos' => function($query) {
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
}
