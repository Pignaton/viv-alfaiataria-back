<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\CamisaPersonalizada;
use App\Models\Tecido;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CartController extends Controller
{

    public function getCart(Request $request)
    {
        $cart = $this->getActiveCart($request);

        if (!$cart) {
            return response()->json(['status' => 'error','message' => 'Nenhum carrinho ativo encontrado'], 404);
        }

        return response()->json($cart->load(['itens.camisaPersonalizada', 'itens.tecido']));

    }

    public function addToCart(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|exists:cliente,usuario_id',
            'tecido_id' => 'required|exists:tecido,id',
            'genero' => 'required|string',
            'modelagem' => 'required|string',
            'manga' => 'required|string',
            'punho' => 'nullable|string',
            'bolso' => 'nullable|string',
            'vista' => 'required|string',
            'colarinho' => 'required|string',
            'medidas' => 'nullable|array',
            'quantidade' => 'required|integer|min:1',
            'preco_unitario' => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return \DB::transaction(function () use ($request) {

            $cliente = Cliente::where('usuario_id', $request->usuario_id)->exists();

            $guestId = $cliente ? null : $this->getGuestId($request);

            // Busca ou cria um pedido
            $pedido = Pedido::firstOrCreate(
                [
                    $request->usuario_id ? 'usuario_id' : 'guest_id' => $request->usuario_id ?: $guestId,
                    'status' => 'pendente'
                ],
                [
                    'codigo' => 'CART-' . Str::upper(Str::random(10)),
                    'subtotal' => 0,
                    'frete' => 0,
                    'total' => 0
                ]
            );

            // salva a camisa personalizada e adicona o item ao pedido
            $camisa = CamisaPersonalizada::create([
                'usuario_id' => $request->usuario_id,
                'genero' => $request->genero,
                'modelagem' => $request->modelagem,
                'manga' => $request->manga,
                'punho' => $request->punho,
                'bolso' => $request->bolso,
                'vista' => $request->vista,
                'colarinho' => $request->colarinho,
                'tecido_id' => $request->tecido_id,
                'imagem_preview' => $request->imagem_preview,
                'guest_id' => $guestId,
                'medidas' => $request->medidas ? json_encode($request->medidas) : null
            ]);

            $item = ItemPedido::create([
                'pedido_id' => $pedido->id,
                'camisa_personalizada_id' => $camisa->id,
                'tecido_id' => $request->tecido_id,
                'quantidade' => $request->quantidade,
                'preco_unitario' => $request->preco_unitario
            ]);

            $this->updateOrderTotals($pedido);

            $responseData = [
                'message' => 'Item adicionado ao carrinho',
                'data' => $pedido->load(['itens.camisaPersonalizada', 'itens.tecido'])
            ];

            if (!$request->usuario_id) {
                return response()
                    ->json($responseData)
                    ->cookie('guest_id', $guestId, 60 * 24 * 30, '/', null, false, true);
            }

            return response()->json($responseData);
        });
    }

    public function updateCartItem(Request $request, $itemId)
    {

        $validator = Validator::make($request->all(), [
            'quantidade' => 'required|integer|min:1',
            'preco_unitario' => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $usuarioId = $request->usuario_id;

        $item = $this->getCartItem($itemId, $usuarioId, $request);

        if (!$item) {
            return response()->json(['message' => 'Item não encontrado no carrinho'], 404);
        }

        $item->update([
            'quantidade' => $request->quantidade,
            'preco_unitario' => $request->preco_unitario
        ]);

        $this->updateOrderTotals($item->pedido);

        return response()->json([
            'message' => 'Item atualizado no carrinho',
            'data' => $item->pedido->load(['itens.camisaPersonalizada', 'itens.tecido'])
        ]);
    }

    public function removeFromCart(Request $request, $itemId, $usuarioId)
    {

        $cliente = Cliente::with('usuario')->where('usuario_id', $usuarioId)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }

        $item = $this->getCartItem($itemId, $usuarioId, $request);

        if (!$item) {
            return response()->json(['message' => 'Item não encontrado no carrinho'], 404);
        }

        $pedido = $item->pedido;
        $item->delete();

        if ($pedido->itens()->count() === 0) {
            $pedido->delete();
            return response()->json(['message' => 'Carrinho vazio']);
        }

        $this->updateOrderTotals($pedido);

        return response()->json([
            'message' => 'Item removido do carrinho',
            'data' => $pedido->load(['itens.camisaPersonalizada', 'itens.tecido'])
        ]);
    }

    public function applyCoupon(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'codigo_cupom' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pedido = $this->getActiveCart($request);

        if (!$pedido) {
            return response()->json(['message' => 'Nenhum carrinho ativo encontrado'], 404);
        }

        $desconto = $this->validateCoupon($request->codigo_cupom);

        $pedido->update([
            'desconto' => $desconto,
            'total' => $pedido->subtotal - $desconto + $pedido->frete
        ]);

        return response()->json([
            'message' => 'Cupom aplicado com sucesso',
            'data' => $pedido
        ]);
    }

    public function checkout(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'endereco_entrega' => 'required|array',
            'endereco_entrega.cep' => 'required|string',
            'endereco_entrega.rua' => 'required|string',
            'endereco_entrega.numero' => 'required|string',
            'endereco_entrega.bairro' => 'required|string',
            'endereco_entrega.cidade' => 'required|string',
            'endereco_entrega.estado' => 'required|string',
            'criar_conta' => 'nullable|boolean',
            'endereco_entrega_id' => 'required|exists:endereco,id',
            'observacoes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pedido = $this->getActiveCart($request);

        if (!$pedido || $pedido->itens()->count() === 0) {
            return response()->json(['message' => 'Carrinho vazio ou não encontrado'], 400);
        }

        return \DB::transaction(function () use ($request, $pedido) {
            if ($pedido->guest_id && $request->criar_conta) {
                $user = $this->createUserFromGuest($request->email);
                $pedido->update(['usuario_id' => $user->id, 'guest_id' => null]);

                CamisaPersonalizada::where('guest_id', $pedido->guest_id)
                    ->update(['usuario_id' => $request->usuario_id, 'guest_id' => null]);
            }

            $pedido->update([
                'status' => 'pago',
                'email' => $request->email,
                'endereco_entrega' => json_encode($request->endereco_entrega),
                'observacoes' => $request->observacoes,
                'codigo' => 'ORD-' . Str::upper(Str::random(10))
            ]);

            $response = response()->json([
                'message' => 'Pedido finalizado com sucesso',
                'data' => $pedido,
                'account_created' => $request->criar_conta ?? false
            ]);

            if ($pedido->guest_id) {
                $response->cookie(Cookie::forget('guest_id'));
            }

            return $response;
        });
    }

    private function getActiveCart(Request $request)
    {

        $cliente = Cliente::with('usuario')->where('usuario_id', $request->usuario_id)->first();

        //usuários autenticados
        if (!empty($cliente)) {
            $usuario = $cliente->usuario;
            return Pedido::with(['itens.camisaPersonalizada', 'itens.tecido'])
                ->where('usuario_id', $usuario->id)
                ->where('status', 'pendente')
                ->first();
        }
        // Para guests
        else if ($guestId = $request->cookie('guest_id')) {
            return Pedido::with(['itens.camisaPersonalizada', 'itens.tecido'])
                ->where('guest_id', $guestId)
                ->where('status', 'pendente')
                ->first();
        }

        return null;
    }

    private function getCartItem($itemId, $usuarioId, Request $request)
    {
        $cliente = Cliente::with('usuario')->where('usuario_id', $request->usuario_id)->first();

        //usuários autenticados
        if (!empty($cliente)) {
            return ItemPedido::whereHas('pedido', function($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId)
                    ->where('status', 'pendente');
            })
                ->find($itemId);
        }

        if ($guestId = $this->getGuestId($request)) {
            return ItemPedido::whereHas('pedido', function($query) use ($guestId) {
                $query->where('guest_id', $guestId)
                    ->where('status', 'pendente');
            })
                ->find($itemId);
        }

        return null;
    }

    private function getGuestId(Request $request)
    {
        return $request->cookie('guest_id') ?? Str::uuid();
    }

    private function updateOrderTotals(Pedido $pedido)
    {
        $subtotal = $pedido->itens->sum(function($item) {
            return $item->preco_unitario * $item->quantidade;
        });

        $pedido->update([
            'subtotal' => $subtotal,
            'total' => $subtotal - $pedido->desconto + $pedido->frete
        ]);
    }

    private function validateCoupon($couponCode)
    {
        $coupons = [
            'DESCONTO10' => 10.00,
            'PROMO20' => 20.00
        ];

        return $coupons[strtoupper($couponCode)] ?? 0.00;
    }

    private function createUserFromGuest($email)
    {
        $password = Str::random(10);

        $user = User::create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        //  e-mail com asenha temporária

        return $user;
    }
}
