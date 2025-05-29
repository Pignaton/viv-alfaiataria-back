<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\Usuario;

class PerfilController extends Controller
{
    public function perfil()
    {
        $usuario = auth()->user();

        $usuario->load('tokensAcesso');

        return view('pages.perfil.index', compact('usuario'));
    }

    public function atualizarPerfil(Request $request)
    {
        $usuario = auth()->user();

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:usuario,email,'.$usuario->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Atualiza a atabela usuário
        $usuario->update([
            'email' => $request->email
        ]);

        // Se for admin/gerente, atualiza o nome na tabela administrador
        if (in_array($usuario->tipo_usuario, ['admin', 'gerente'])) {
            $usuario->administrador()->updateOrCreate(
                ['usuario_id' => $usuario->id],
                ['nome' => $request->nome]
            );
        }

        return redirect()->route('admin.perfil')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function alterarSenha(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        $usuario = auth()->user();

        if (!Hash::check($request->senha_atual, $usuario->senha_hash)) {
            return back()->withErrors(['senha_atual' => 'A senha atual está incorreta']);
        }

        $usuario->update([
            'senha_hash' => Hash::make($request->nova_senha)
        ]);

        return redirect()->route('admin.perfil')
            ->with('success', 'Senha alterada com sucesso!');
    }

    public function revogarSessao($tokenId)
    {
        $token = TokenAcesso::where('usuario_id', auth()->id())
            ->where('id', $tokenId)
            ->firstOrFail();

        $token->update(['utilizado' => true]);

        // Caso esteja revogando o token atual, faz logout
        if ($token->token === request()->bearerToken() || $token->token === session('token_api')) {
            auth()->logout();
            return redirect()->route('login')
                ->with('success', 'Sessão revogada com sucesso.');
        }

        return redirect()->route('admin.perfil')
            ->with('success', 'Acesso revogado com sucesso!');
    }
}
