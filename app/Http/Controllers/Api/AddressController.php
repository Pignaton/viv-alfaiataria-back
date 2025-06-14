<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function getUserAddresses($id)
    {
        try {

            $addresses = Endereco::where('usuario_id', $id)
                ->orderBy('principal', 'desc')
                ->orderBy('data_cadastro', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $addresses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar endereços',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createAddress(Request $request)
    {
        $request->validate([
            'apelido' => 'nullable|string|max:50',
            'cep' => 'required|string|max:9',
            'logradouro' => 'required|string|max:100',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:50',
            'bairro' => 'required|string|max:50',
            'cidade' => 'required|string|max:50',
            'estado' => 'required|string|size:2',
            'principal' => 'nullable|boolean',
            'entrega' => 'nullable|boolean'
        ]);

        /*if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }*/

        try {
            //$user = Auth::user();
            $cliente = Cliente::where('usuario_id', $request->id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do cliente não encontrados'
                ], 404);
            }

            $address = Endereco::create([
                'usuario_id' => $cliente->id,
                'apelido' => $request->apelido,
                'cep' => $request->cep,
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'principal' => $request->principal ?? false,
                'entrega' => $request->entrega ?? true
            ]);

            return response()->json([
                'success' => true,
                'data' => $address,
                'message' => 'Endereço cadastrado com sucesso'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar endereço',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'apelido' => 'nullable|string|max:50',
            'cep' => 'required|string|max:9',
            'logradouro' => 'required|string|max:100',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:50',
            'bairro' => 'required|string|max:50',
            'cidade' => 'required|string|max:50',
            'estado' => 'required|string|size:2',
            'principal' => 'nullable|boolean',
            'entrega' => 'nullable|boolean'
        ]);

        /*if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 404);
        }*/

        try {
            $cliente = Endereco::with('usuario')->where('id', $request->id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do cliente não encontrados'
                ], 404);
            }

            $usuario = $cliente->usuario;

            $address = Endereco::where('id', $id)
                ->where('usuario_id', $usuario->id)
                ->firstOrFail();

            $address->update([
                'apelido' => $request->apelido,
                'cep' => $request->cep,
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'principal' => $request->principal ?? $address->principal,
                'entrega' => $request->entrega ?? $address->entrega
            ]);

            return response()->json([
                'success' => true,
                'data' => $address,
                'message' => 'Endereço atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar endereço',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAddress($id)
    {
        try {
            $cliente = Endereco::with('usuario')->where('id', $id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do cliente não encontrados'
                ], 404);
            }

            $usuario = $cliente->usuario;

            $address = Endereco::where('id', $id)
                ->where('usuario_id', $usuario->id)
                ->firstOrFail();

            $address->delete();

            return response()->json([
                'success' => true,
                'message' => 'Endereço removido com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover endereço',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
