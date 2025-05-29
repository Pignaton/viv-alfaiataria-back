<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ClienteController extends Controller
{
    public function index()
    {

        $clientes = Usuario::with('cliente')
            ->where('tipo_usuario', 'cliente')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('pages.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('pages.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:usuario,email',
            'senha' => ['required', 'confirmed', Password::min(8)],
            'nome_completo' => 'required|string|max:100',
            'cpf' => 'nullable|string|max:14|unique:cliente,cpf',
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'ativo' => 'boolean'
        ]);

        $usuario = Usuario::create([
            'email' => $request->email,
            'senha_hash' => Hash::make($request->senha),
            'tipo_usuario' => 'cliente',
            'ativo' => $request->ativo ?? true
        ]);

        $clienteData = $request->only(['nome_completo', 'cpf', 'telefone', 'data_nascimento']);
        $clienteData['cpf'] = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;
        $clienteData['telefone'] = $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null;

        $usuario->cliente()->create($clienteData);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show($id)
    {
        $cliente = Usuario::with('cliente')
            ->where('tipo_usuario', 'cliente')
            ->findOrFail($id);

        return view('pages.clientes.show', compact('cliente'));
    }

    public function edit($id)
    {
        $cliente = Usuario::with('cliente')
            ->where('tipo_usuario', 'cliente')
            ->findOrFail($id);

        return view('pages.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Usuario::with('cliente')
            ->where('tipo_usuario', 'cliente')
            ->findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:usuario,email,'.$cliente->id,
            'nome_completo' => 'required|string|max:100',
            'cpf' => 'nullable|string|max:14|unique:cliente,cpf,'.$cliente->id.',usuario_id',
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'ativo' => 'boolean'
        ]);

        $usuarioData = [
            'email' => $request->email,
            'ativo' => $request->ativo ?? $cliente->ativo
        ];

        if ($request->filled('senha')) {
            $request->validate([
                'senha' => ['confirmed', Password::min(8)]
            ]);
            $usuarioData['senha_hash'] = Hash::make($request->senha);
        }

        $cliente->update($usuarioData);

        $clienteData = $request->only(['nome_completo', 'cpf', 'telefone', 'data_nascimento']);
        $clienteData['cpf'] = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;
        $clienteData['telefone'] = $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null;

        $cliente->cliente()->update($clienteData);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $cliente = Usuario::where('tipo_usuario', 'cliente')->findOrFail($id);
        $cliente->delete();

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
}
