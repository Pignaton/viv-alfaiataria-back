<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CamisaPersonalizada;
use App\Models\Medida;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{

    public function getUserData($email)
    {

        //$user = Auth::user();
        try {
            $usuario = Usuario::where('email', $email)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $cliente = Cliente::where('usuario_id', $usuario->id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do cliente não encontrados'
                ], 404);
            }

            $nomeParts = explode(' ', $cliente->nome_completo);
            $tratamento = count($nomeParts) > 0 ? $nomeParts[0] : '';
            $nome = count($nomeParts) > 1 ? $nomeParts[1] : '';
            $sobrenome = count($nomeParts) > 2 ? implode(' ', array_slice($nomeParts, 2)) : '';

            //$dataNascimento = $cliente->data_nascimento ? explode('-', $cliente->data_nascimento) : [];
            $dataNascimento = Carbon::parse($cliente->data_nascimento);

            return response()->json([
                'success' => true,
                'data' => [
                    'tratamento' => $tratamento,
                    'nome' => $nome,
                    'sobrenome' => $sobrenome,
                    'dia' => $dataNascimento->format('d'),
                    'mes' => $dataNascimento->format('m'),
                    'ano' => $dataNascimento->format('Y'),
                    'cpf' => $cliente->cpf ?? '',
                    'telefone' => $cliente->telefone ?? '',
                    'email' => $usuario->email
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar dados do usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUserData(Request $request)
    {
        $cliente = Cliente::with('usuario')->where('usuario_id', $request->id)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }

        $usuario = $cliente->usuario;

        $request->validate([
            'email' => ['required', 'email', Rule::unique('usuario')->ignore($usuario->id)],
            'nome' => 'required|string|max:100',
            'sobrenome' => 'required|string|max:100',
            //'cpf' => ['required', 'string', 'max:14', Rule::unique('cliente')->ignore($usuario->id)],
            'telefone' => 'nullable|string|max:20',
            //'data_nascimento' => 'nullable|date'
        ]);

        try {
            $nomeCompleto = trim(
                ($request->tratamento ?? '') . ' ' .
                ($request->nome ?? '') . ' ' .
                ($request->sobrenome ?? '')
            );

            $cliente->nome_completo = $nomeCompleto;
            //$cliente->cpf = $request->cpf ?? $cliente->cpf;
            $cliente->telefone = $request->telefone ?? $cliente->telefone;

            if ($request->dia && $request->mes && $request->ano) {
                $cliente->data_nascimento = sprintf(
                    '%04d-%02d-%02d',
                    $request->ano,
                    $request->mes,
                    $request->dia
                );
            }

            $cliente->save();

            if ($request->email && $request->email !== $usuario->email) {
                $usuario->email = $request->email;
                $usuario->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Dados do usuário atualizados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar dados do usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMedidas($usuarioId)
    {
        try {
            $medidas = Medida::where('usuario_id', $usuarioId)
                ->orderBy('data_registro', 'desc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->data_registro)->format('d/m/Y');
                });


            $perfis = [];
            foreach ($medidas as $data => $itens) {
                $perfis[] = [
                    'nome_perfil' => 'Perfil ' . count($perfis) + 1,
                    'data_registro' => $data,
                    'medidas' => $itens->map(function($item) {
                        return [
                            'nome' => $item->nome,
                            'valor' => $item->valor,
                            'unidade' => $item->unidade
                        ];
                    })->toArray()
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $perfis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar medidas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMedida($id)
    {
        try {
            $medida = Medida::where('usuario_id', $id)
                ->first();

            return response()->json([
                'success' => true,
                'data' => $medida
            ]);
            //return response()->json($medida);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar medidas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     */
    public function saveMedidas(Request $request, $id)
    {
        //$user = Auth::user();

        $data = $request->all();

        $request->validate([
            '*.nome' => 'required|string|max:50',
            '*.valor' => 'required|numeric|min:0.1|max:999.99',
            '*.unidade' => 'sometimes|string|max:10'
        ]);

        try {
            $cliente = Cliente::where('usuario_id', $id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do cliente não encontrados'
                ], 404);
            }

            $savedMedidas = [];

            foreach ($data as $medidaData) {
                $medida = Medida::updateOrCreate(
                    [
                        'usuario_id' => $id,
                        'nome' => $medidaData['nome']
                    ],
                    [
                        'valor' => $medidaData['valor'],
                        'unidade' => $medidaData['unidade'] ?? 'cm',
                        'data_registro' => now()
                    ]
                );

                $savedMedidas[] = $medida;
            }

            return response()->json([
                'success' => true,
                'data' => $savedMedidas,
                'message' => 'Medidas salvas com sucesso',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar medidas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMedida($id)
    {
        try {
            //$user = Auth::user();
            $cliente = Cliente::where('usuario_id', $id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do cliente não encontrados'
                ], 404);
            }

            $deleted = Medida::where('usuario_id', $cliente->id)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Medida removida com sucesso'
                ]);
            }

            return response()->json([ 'success' => true,  'message' => 'Medida não encontrada']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medida não encontrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para salvar um perfil completo de medidas
     */
    public function savePerfilMedidas(Request $request, $id)
    {
        $cliente = Cliente::where('usuario_id', $id)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }

        $data = $request->validate([
            'nome_perfil' => 'required|string|max:50',
            'medidas' => 'required|array',
            'medidas.*.nome' => 'required|string|max:50',
            'medidas.*.valor' => 'required|numeric|min:0.1|max:999.99'
        ]);

        Medida::where('usuario_id', $cliente->id)
            ->where('nome', 'like', $data['nome_perfil'] . '%')
            ->delete();

        $savedMedidas = [];

        foreach ($data['medidas'] as $medidaData) {
            $medida = Medida::create([
                'usuario_id' => $cliente->id,
                'nome' => $data['nome_perfil'] . '_' . $medidaData['nome'],
                'valor' => $medidaData['valor'],
                'unidade' => 'cm',
                'data_registro' => now()
            ]);

            $savedMedidas[] = $medida;
        }

        return response()->json([
            'success' => true,
            'Perfil de medidas salvo com sucesso',
            'data' => $savedMedidas
        ]);
    }

    public function getPerfilMedidas($nomePerfil, $id)
    {
        $cliente = Cliente::where('usuario_id', $id)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }

        $medidas = Medida::where('usuario_id', $id)
            ->where('nome', 'like', $nomePerfil . '_%')
            ->get()
            ->map(function ($medida) use ($nomePerfil) {
                return [
                    'nome' => str_replace($nomePerfil . '_', '', $medida->nome),
                    'valor' => $medida->valor,
                    'unidade' => $medida->unidade
                ];
            });

        if ($medidas->isEmpty()) {
            return response()->json(['message' => 'Perfil de medidas não encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'nome_perfil' => $nomePerfil,
            'medidas' => $medidas
        ]);
    }

    public function convertGuestToUser(Request $request)
    {
        $cliente = Cliente::where('usuario_id', $request->usuario_id)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }
        $guestId = $request->cookie('guest_id');

        if ($guestId) {
            Pedido::where('guest_id', $guestId)
                ->update(['guest_id' => null, 'usuario_id' => $request->usuario_id]);

            CamisaPersonalizada::where('guest_id', $guestId)
                ->update(['guest_id' => null, 'usuario_id' => $request->usuario_id]);

            return response()->json(['message' => 'Dados migrados com sucesso']);
        }

        return response()->json(['message' => 'Nada para migrar']);
    }

    public function getPurchaseHistory(Request $request, $usuario_id)
    {
        try {

            $cliente = Cliente::where('usuario_id', $usuario_id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do cliente não encontrados'
                ], 404);
            }

            $request->validate([
                'periodo' => 'sometimes|in:3,6,12,all'
            ]);

            $periodo = $request->input('periodo', '3'); // Default: últimos 3 meses

            $query = Pedido::with(['itens.camisaPersonalizada.tecido', 'enderecoEntrega'])
                ->where('usuario_id', $usuario_id)
                ->where('status', '!=', 'pendente')
                ->orderBy('data_pedido', 'desc');

            if ($periodo !== 'all') {
                $query->where('data_pedido', '>=', now()->subMonths($periodo));
            }

            $pedidos = $query->get()->map(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'numero' => $pedido->codigo,
                    'data' => $pedido->data_pedido->format('Y-m-d'),
                    'status' => $pedido->status,
                    'total' => $pedido->total,
                    'metodo_pagamento' => $pedido->metodo_pagamento,
                    'endereco' => is_string($pedido->endereco_entrega) ? json_decode($pedido->endereco_entrega, true) : $pedido->endereco_entrega,
                    'itens' => $pedido->itens->map(function ($item) {
                        return [
                            'produto' => [
                                'nome' => $item->camisaPersonalizada ?
                                    'Camisa Personalizada - ' . $item->camisaPersonalizada->tecido->nome :
                                    'Produto não disponível',
                                'imagem' => $item->camisaPersonalizada->tecido->imagem_url ?? null
                            ],
                            'quantidade' => $item->quantidade,
                            'preco_unitario' => $item->preco_unitario,
                            'subtotal' => $item->quantidade * $item->preco_unitario
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $pedidos,
                'meta' => [
                    'total_pedidos' => $pedidos->count(),
                    'periodo' => $periodo === 'all' ? 'Todo o histórico' : "Últimos $periodo meses"
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar histórico de compras',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

