<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\MetodoPagamento;
use App\Models\Cartao;

class MetodoPagamentoController extends Controller
{

    /**
     * Obtém o metodo de pagamento do usuário
     */
    public function getMetodoPagamento($id)
    {
        //$user = Auth::user();
        $cliente = Cliente::where('usuario_id', $id)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }

        $metodo = MetodoPagamento::with('cartao')
            ->where('usuario_id', $cliente->id)
            ->first();

        if (!$metodo) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum método de pagamento cadastrado'
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $metodo]);
    }

    /**
     * Adiciona ou atualiza um cartão de crédito/débito
     */
    public function saveCartao(Request $request)
    {

        $request->validate([
            'tipo' => 'required|in:cartao_credito,cartao_debito',
            'apelido' => 'sometimes|string|max:50',
            'ultimos_quatro_digitos' => 'required|string|size:4',
            'bandeira' => 'required|string|max:20',
            'nome_titular' => 'required|string|max:100',
            'token_secure' => 'required|string|max:100',
            'data_validade' => 'required|string|size:5' // MM/AA
        ]);

        //$cliente = Cliente::where('usuario_id', $request->id)->first();
        $cliente = Cliente::with('usuario')->where('usuario_id', $request->id)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }

        // Verifica se já existe um metodo para o usuário
        $metodo = MetodoPagamento::where('usuario_id', $cliente->id)->first();

        // Inicia uma transação para garantir consistência
        return \DB::transaction(function () use ($cliente, $request, $metodo) {
            if ($metodo) {
                $metodo->update([
                    'tipo' => $request->tipo,
                    'apelido' => $request->apelido ?? 'Meu Cartão',
                    'ativo' => true
                ]);

                $metodo->cartao()->update([
                    'ultimos_quatro_digitos' => $request->ultimos_quatro_digitos,
                    'bandeira' => $request->bandeira,
                    'nome_titular' => $request->nome_titular,
                    'token_secure' => $request->token_secure,
                    'data_validade' => $request->data_validade
                ]);
            } else {
                $metodo = MetodoPagamento::create([
                    'usuario_id' => $cliente->id,
                    'tipo' => $request->tipo,
                    'apelido' => $request->apelido ?? 'Meu Cartão',
                    'ativo' => true
                ]);

                Cartao::create([
                    'metodo_id' => $metodo->id,
                    'ultimos_quatro_digitos' => $request->ultimos_quatro_digitos,
                    'bandeira' => $request->bandeira,
                    'nome_titular' => $request->nome_titular,
                    'token_secure' => $request->token_secure,
                    'data_validade' => $request->data_validade
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cartão salvo com sucesso',
                'data' => $metodo->load('cartao')
            ]);
        });
    }

    /**
     * Remove o cartão do usuário (desativa)
     */
    public function removeCartao($id)
    {

        $cliente = Cliente::with('usuario')->where('usuario_id', $id)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Dados do cliente não encontrados'
            ], 404);
        }

        $metodo = MetodoPagamento::where('usuario_id', $cliente->id)->first();

        if (!$metodo) {
            return response()->json(['success' => false, 'message' => 'Nenhum cartão cadastrado'], 404);
        }

        $metodo->update(['ativo' => false]);

        return response()->json(['success' => true, 'message' => 'Cartão desativado com sucesso']);
    }
}
