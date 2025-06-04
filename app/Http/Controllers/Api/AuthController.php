<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:usuario',
            'senha' => ['required', Password::min(8)],
            'nome_completo' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:cliente',
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date'
        ]);

        $usuario = Usuario::create([
            'email' => $request->email,
            'senha_hash' => Hash::make($request->senha),
            'tipo_usuario' => 'cliente'
        ]);

        Cliente::create([
            'usuario_id' => $usuario->id,
            'nome_completo' => $request->nome_completo,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'data_nascimento' => $request->data_nascimento
        ]);

        return response()->json([
            'message' => 'Usuário registrado com sucesso',
            'usuario' => $usuario
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->senha, $usuario->senha_hash)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        if (!$usuario->ativo) {
            return response()->json(['message' => 'Conta desativada'], 403);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        $usuario->update([
            'ultimo_login' => now(),
            'tentativas_login' => 0
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario->load('cliente')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
}
