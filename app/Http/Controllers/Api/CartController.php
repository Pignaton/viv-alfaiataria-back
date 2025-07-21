<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CamisaPersonalizada;
use App\Models\Cliente;
use App\Models\Endereco;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Tecido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function getCart(Request $request)
    {
        $cart = $this->getActiveCart($request);

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum carrinho ativo encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $cart->load(['itens.camisaPersonalizada', 'itens.tecido'])
        ]);
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'nullable|exists:users,id',
            'tecido_id' => 'required|exists:tecidos,id',
            'genero' => 'required|string|in:masculino,feminino,unissex',
            'modelagem' => 'required|string',
            'manga' => 'required|string',
            'punho' => 'nullable|string',
            'bolso' => 'nullable|string',
            'vista' => 'required|string',
            'colarinho' => 'required|string',
            'medidas' => 'nullable|array',
            'quantidade' => 'required|integer|min:1',
            'preco_unitario' => 'required|numeric|min:0.01',
            'imagem_preview' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $usuarioId = $request->usuario_id;
            $guestId = $usuarioId ? null : $this->getGuestId($request);

            // Verifica se o tecido existe e está ativo
            $tecido = Tecido::findOrFail($request->tecido_id);
            if (!$tecido->ativo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'O tecido selecionado não está disponível'
                ], 400);
            }

            // Busca ou cria um pedido
            $pedido = Pedido::firstOrCreate(
                [
                    $usuarioId ? 'usuario_id' : 'guest_id' => $usuarioId ?: $guestId,
                    'status' => 'pendente'
                ],
                [
                    'codigo' => 'CART-' . Str::upper(Str::random(10)),
                    'subtotal' => 0,
                    'frete' => 0,
                    'total' => 0
                ]
            );

            // Cria a camisa personalizada
            $camisa = CamisaPersonalizada::create([
                'usuario_id' => $usuarioId,
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

            // Adiciona o item ao pedido
            $item = ItemPedido::create([
                'pedido_id' => $pedido->id,
                'camisa_personalizada_id' => $camisa->id,
                'tecido_id' => $request->tecido_id,
                'quantidade' => $request->quantidade,
                'preco_unitario' => $request->preco_unitario
            ]);

            $this->updateOrderTotals($pedido);

            $responseData = [
                'status' => 'success',
                'message' => 'Item adicionado ao carrinho',
                'data' => $pedido->load(['itens.camisaPersonalizada', 'itens.tecido'])
            ];

            // Se for guest, retorna o cookie
            if (!$usuarioId) {
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
            'usuario_id' => 'nullable|exists:users,id',
            'quantidade' => 'required|integer|min:1',
            'preco_unitario' => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $item = $this->getCartItem($itemId, $request);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item não encontrado no carrinho'
            ], 404);
        }

        $item->update([
            'quantidade' => $request->quantidade,
            'preco_unitario' => $request->preco_unitario
        ]);

        $this->updateOrderTotals($item->pedido);

        return response()->json([
            'status' => 'success',
            'message' => 'Item atualizado no carrinho',
            'data' => $item->pedido->load(['itens.camisaPersonalizada', 'itens.tecido'])
        ]);
    }

    public function removeFromCart(Request $request, $itemId)
    {
        $item = $this->getCartItem($itemId, $request);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item não encontrado no carrinho'
            ], 404);
        }

        $pedido = $item->pedido;
        $item->delete();

        // Remove o pedido se não houver mais itens
        if ($pedido->itens()->count() === 0) {
            $pedido->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Carrinho vazio'
            ]);
        }

        $this->updateOrderTotals($pedido);

        return response()->json([
            'status' => 'success',
            'message' => 'Item removido do carrinho',
            'data' => $pedido->load(['itens.camisaPersonalizada', 'itens.tecido'])
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'nullable|exists:users,id',
            'codigo_cupom' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pedido = $this->getActiveCart($request);

        if (!$pedido) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum carrinho ativo encontrado'
            ], 404);
        }

        $desconto = $this->validateCoupon($request->codigo_cupom);

        $pedido->update([
            'desconto' => $desconto,
            'total' => $pedido->subtotal - $desconto + $pedido->frete
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cupom aplicado com sucesso',
            'data' => $pedido
        ]);
    }

    public function checkout(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'personal.firstName' => 'required|string|max:100',
            'personal.lastName' => 'required|string|max:100',
            'personal.personType' => 'required|in:physical,legal',
            'personal.documentNumber' => 'required|string|max:20',
            'personal.phone' => 'required|string|max:20',
            'personal.email' => 'required|email|max:100',
            'address.cep' => 'required|string|max:10',
            'address.street' => 'required|string|max:200',
            'address.number' => 'required|string|max:20',
            'address.complement' => 'nullable|string|max:100',
            'address.neighborhood' => 'required|string|max:100',
            'address.city' => 'required|string|max:100',
            'address.state' => 'required|string|size:2',
            'payment.method' => 'required|in:credit,pix,boleto',
            'deliveryMethod' => 'required|in:standard,express',
            'items' => 'required|array',
            'items.*.productId' => 'required|exists:tecido,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'guestId' => 'nullable|string',
            'usuario_id' => 'nullable|integer',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            // 1. Buscar ou criar o pedido
            $pedido = $this->getOrCreateOrder($request);

            // 2. Processar itens
            $this->processItems($request->items, $pedido);

            // 3. Calcular totais
            $this->calculateTotals($pedido, $request->deliveryMethod);

            // 4. Salvar informações do cliente e endereço
            $this->saveCustomerData($pedido, $request);

            // 5. Processar pagamento
            $paymentResult = $this->processPayment($pedido, $request->payment['method']);

            // 6. Retornar resposta
            return $this->buildResponse($pedido, $paymentResult, $request->guestId);
        });
    }

    private function getActiveCart(Request $request)
    {
        $usuarioId = $request->usuario_id;
        $guestId = $request->cookie('guest_id');

        if ($usuarioId) {
            return Pedido::with(['itens.camisaPersonalizada', 'itens.tecido'])
                ->where('usuario_id', $usuarioId)
                ->where('status', 'pendente')
                ->first();
        } elseif ($guestId) {
            return Pedido::with(['itens.camisaPersonalizada', 'itens.tecido'])
                ->where('guest_id', $guestId)
                ->where('status', 'pendente')
                ->first();
        }

        return null;
    }

    private function getCartItem($itemId, Request $request)
    {
        $usuarioId = $request->usuario_id;
        $guestId = $request->cookie('guest_id');

        if ($usuarioId) {
            return ItemPedido::whereHas('pedido', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId)
                    ->where('status', 'pendente');
            })->find($itemId);
        } elseif ($guestId) {
            return ItemPedido::whereHas('pedido', function ($query) use ($guestId) {
                $query->where('guest_id', $guestId)
                    ->where('status', 'pendente');
            })->find($itemId);
        }

        return null;
    }

    private function getGuestId(Request $request)
    {
        return $request->cookie('guest_id') ?? Str::uuid();
    }

    private function updateOrderTotals(Pedido $pedido)
    {
        $subtotal = $pedido->itens->sum(function ($item) {
            return $item->preco_unitario * $item->quantidade;
        });

        $pedido->update([
            'subtotal' => $subtotal,
            'total' => $subtotal - $pedido->desconto + $pedido->frete
        ]);
    }

    private function validateCoupon($couponCode)
    {
        // Implementar lógica real de validação de cupom
        $coupons = [
            'DESCONTO10' => 10.00,
            'PROMO20' => 20.00
        ];

        return $coupons[strtoupper($couponCode)] ?? 0.00;
    }

    private function createUserFromGuest($email, $password)
    {
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
            'nome' => 'Cliente',
        ]);

        //TODO: Enviar e-mail com a senha temporária

        return $user;
    }

    private function getOrCreateOrder(Request $request)
    {
        $usuarioId = $request->user()?->id ?? $request->input('personal.usuario_id') ?? null;
        $guestId = $request->guestId ?? ($usuarioId ? null : Str::uuid());

        $whereClause = $usuarioId
            ? ['usuario_id' => $usuarioId, 'status' => 'pendente']
            : ['guest_id' => $guestId, 'status' => 'pendente'];

        $createData = [
            'codigo' => 'CART-' . Str::upper(Str::random(10)),
            'subtotal' => 0,
            'frete' => 0,
            'total' => 0,
            'usuario_id' => $usuarioId,
            'guest_id' => $usuarioId ? null : $guestId,
            'status' => 'pendente'
        ];


        $createData = array_filter($createData, fn($value) => !is_null($value));

        return Pedido::firstOrCreate($whereClause, $createData);
    }

    private function processItems($items, Pedido $pedido)
    {
        foreach ($items as $item) {
            // Verificar se já existe no pedido
            $existingItem = $pedido->itens()
                ->where('tecido_id', $item['productId'])
                ->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantidade' => $item['quantity'],
                    'preco_unitario' => $item['price']
                ]);
            } else {
                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'tecido_id' => $item['productId'],
                    'quantidade' => $item['quantity'],
                    'preco_unitario' => $item['price']
                ]);
            }
        }
    }

    private function calculateTotals(Pedido $pedido, $deliveryMethod)
    {
        $subtotal = $pedido->itens->sum(function ($item) {
            return $item->preco_unitario; //* $item->quantidade
        });

        $frete = $deliveryMethod === 'express' ? 25.90 : 12.90;

        $pedido->update([
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $subtotal + $frete
        ]);
    }

    private function saveCustomerData(Pedido $pedido, $request)
    {
        $updateData = [
            'metodo_pagamento' => $request->payment['method'],
            'dados_cliente' => [
                'nome_completo' => $request->personal['firstName'] . ' ' . $request->personal['lastName'],
                'tipo_pessoa' => $request->personal['personType'],
                'documento' => $request->personal['documentNumber'],
                'telefone' => $request->personal['phone'],
                'email' => $request->personal['email'],
                'usuario_id' => $request->personal['usuario_id'],
            ],
            'endereco_entrega' => [
                'cep' => $request->address['cep'],
                'logradouro' => $request->address['street'],
                'numero' => $request->address['number'],
                'complemento' => $request->address['complement'] ?? null,
                'bairro' => $request->address['neighborhood'],
                'cidade' => $request->address['city'],
                'estado' => $request->address['state']
            ]
        ];

        if (!$request->user()) {
            $updateData['guest_id'] = $request->guestId ?? Str::uuid();
        }

        $pedido->update($updateData);
    }

    private function processPayment(Pedido $pedido, $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'pix':
                return $this->processPixPayment($pedido);
            case 'credit':
                return $this->processCreditPayment($pedido);
            case 'boleto':
                return $this->processBoletoPayment($pedido);
            default:
                throw new \InvalidArgumentException('Método de pagamento inválido');
        }
    }

    private function processCreditCardPayment(Pedido $pedido, Request $request)
    {
        // Implementar integração com gateway de pagamento
        return [
            'status' => 'pendente',
            'metodo' => 'credit_card',
            'valor' => $pedido->total,
            'parcelas' => $request->parcelas ?? 1
        ];
    }

    private function processPixPayment(Pedido $pedido)
    {
        $pixCode = $this->generatePixCode($pedido);

        $pedido->update([
            'metodo_pagamento' => 'pix',
            'dados_pagamento' => [
                'codigo_pix' => $pixCode,
                'qr_code' => $this->generatePixQrCode($pixCode),
                'data_expiracao' => now()->addHours(24)->format('Y-m-d H:i:s')
            ],
            'status' => 'aguardando_pagamento'
        ]);

        return [
            'tipo' => 'pix',
            'codigo' => $pixCode,
            'qr_code' => $this->generatePixQrCode($pixCode),
            'expiracao' => now()->addHours(24)->format('Y-m-d H:i:s')
        ];
    }

    private function generatePixCode(Pedido $pedido)
    {
        // Implementação real geraria um código PIX válido
        return '00020126360014BR.GOV.BCB.PIX0114+55319999999995204000053039865405' .
            str_pad(number_format($pedido->total, 2, '', ''), 10, '0', STR_PAD_LEFT) .
            '5802BR5925LOJA_EXEMPLO6008BRASILIA62070503***6304';
    }

    private function generatePixQrCode($pixCode)
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($pixCode);
    }

    private function processBoletoPayment(Pedido $pedido)
    {
        // Gerar boleto
        return [
            'status' => 'aguardando_pagamento',
            'metodo' => 'boleto',
            'valor' => $pedido->total,
            'codigo_barras' => '34191.11111 11111.111111 11111.111111 1 99990000000000',
            'data_vencimento' => now()->addDays(3)->format('Y-m-d'),
            'url_boleto' => 'https://api.geradorboleto.com/boleto/123456789'
        ];
    }

    private function buildResponse(Pedido $pedido, $paymentResult, $guestId)
    {
        $response = [
            'status' => 'success',
            'message' => 'Pedido criado com sucesso',
            'pedido' => [
                'codigo' => $pedido->codigo,
                'total' => $pedido->total,
                'status' => $pedido->status
            ],
            'pagamento' => $paymentResult
        ];

        if ($guestId) {
            return response()->json($response)
                ->cookie('guest_id', $guestId, 60 * 24 * 30, '/', null, false, true);
        }

        return response()->json($response);
    }
}
