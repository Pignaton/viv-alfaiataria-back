<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\TokenAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

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


        /*Validator::extend('formato_cpf', function ($attribute, $value, $parameters, $validator) {
            return validaCPF($value); //
        });*/

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

        $usuario = Usuario::where(['email' => $request->email, 'tipo_usuario' => 'cliente'])->first();

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

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario) {
            return response()->json([
                'message' => 'Se o email existir em nosso sistema, um link de redefinição será enviado.'
            ], 200);
        }

        $token = Str::random(60);
        TokenAcesso::create([
            'usuario_id' => $usuario->id,
            'token' => Hash::make($token),
            'tipo' => 'password_reset',
            'expiracao' => now()->addHours(2), // Token válido por 2 horas
            'utilizado' => false,
            'ip_origem' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data_criacao' => now()
        ]);

        $resetLink = 'https://vivalfaiataria.com.br/reset-password/' . $token;

        Mail::to($usuario->email)->send(new PasswordResetMail($resetLink));

        return response()->json(['message' => 'Um link de redefinição de senha foi enviado para seu email.']);
    }

    public function resetPassword(Request $request, $token)
    {

        $validator = Validator::make($request->input('data'), [
            'password' => ['required', 'confirmed', PasswordRule::min(8),
                //->mixedCase()
                //->numbers()
                //->symbols(),
                'password_confirmation' => 'required'
            ], [
                'password.required' => 'A senha é obrigatória',
                'password.confirmed' => 'A confirmação de senha não corresponde',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres',
                'password.mixed_case' => 'A senha deve conter letras maiúsculas e minúsculas',
                'password.numbers' => 'A senha deve conter pelo menos um número',
                'password.symbols' => 'A senha deve conter pelo menos um símbolo'
            ]]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $tokenRecords = TokenAcesso::where('tipo', 'password_reset')
            ->where('utilizado', false)
            ->where('expiracao', '>=', now())
            ->get();

        $tokenRecord = null;

        foreach ($tokenRecords as $record) {
            if (Hash::check($token, $record->token)) {
                $tokenRecord = $record;
                break;
            }
        }

        if (!$tokenRecord) {
            return response()->json([
                'message' => 'Token inválido, expirado ou já utilizado.'
            ], 400);
        }

        $usuario = Usuario::find($tokenRecord->usuario_id);
        $usuario->senha_hash = Hash::make($request->input('data')['password']);
        $usuario->save();

        $tokenRecord->utilizado = true;
        $tokenRecord->save();

        return response()->json([
            'message' => 'Senha redefinida com sucesso.'
        ]);
    }

    public function checkResetToken($token)
    {
        $tokenRecord = TokenAcesso::where('tipo', 'password_reset')
            ->where('utilizado', false)
            ->where('expiracao', '>=', now())
            ->first();

        if (!$tokenRecord || !Hash::check($token, $tokenRecord->token)) {
            return response()->json([
                'message' => 'Token inválido ou expirado'
            ], 400);
        }

        $usuario = Usuario::find($tokenRecord->usuario_id);

        return response()->json([
            'valid' => true,
            'email' => $usuario->email,
            'expires_at' => $tokenRecord->expiracao
        ]);
    }
}
