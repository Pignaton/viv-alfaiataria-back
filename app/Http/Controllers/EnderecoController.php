<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EnderecoController extends Controller
{
    public function index()
    {
        $enderecos = auth()->user()->enderecos()->latest('data_cadastro')->get();
        return view('admin.clientes.enderecos', compact('enderecos'));
    }

    public function create()
    {
        return view('admin.clientes.enderecos-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuario,id',
            'apelido' => 'nullable|string|max:50',
            'cep' => 'required|string|max:9',
            'logradouro' => 'required|string|max:100',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:50',
            'bairro' => 'required|string|max:50',
            'cidade' => 'required|string|max:50',
            'estado' => 'required|string|size:2',
            'principal' => 'boolean',
            'entrega' => 'boolean'
        ]);

        if ($request->principal) {
            Endereco::where('usuario_id', $request->usuario_id)
                ->update(['principal' => false]);
        }

        Endereco::create($request->all());

        return redirect()->back()
            ->with('success', 'Endereço adicionado com sucesso!');
    }

    public function edit(Endereco $endereco)
    {
        $this->authorize('update', $endereco);
        return view('admin.clientes.enderecos-edit', compact('endereco'));
    }

    public function update(Request $request, Endereco $endereco)
    {
        $this->authorize('update', $endereco);

        $request->validate([
            'apelido' => 'nullable|string|max:50',
            'cep' => 'required|string|max:9',
            'logradouro' => 'required|string|max:100',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:50',
            'bairro' => 'required|string|max:50',
            'cidade' => 'required|string|max:50',
            'estado' => 'required|string|size:2',
            'principal' => 'boolean',
            'entrega' => 'boolean'
        ]);

        $endereco->update([
            'apelido' => $request->apelido,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => strtoupper($request->estado),
            'principal' => $request->principal ?? false,
            'entrega' => $request->entrega ?? true
        ]);

        return redirect()->route('admin.clientes.enderecos')
            ->with('success', 'Endereço atualizado com sucesso!');
    }

    public function destroy(Endereco $endereco)
    {
        $endereco->delete();

        if ($endereco->principal) {
            Endereco::where('usuario_id', $endereco->usuario_id)
                ->first()?->update(['principal' => true]);
        }

        return redirect()->back()
            ->with('success', 'Endereço removido com sucesso!');
    }

    public function buscarCep(Request $request)
    {
        $request->validate(['cep' => 'required|string|size:8']);

        $response = Http::get("https://viacep.com.br/ws/{$request->cep}/json/");

        if ($response->failed() || isset($response->json()['erro'])) {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }

        $data = $response->json();

        return response()->json([
            'logradouro' => $data['logradouro'] ?? '',
            'bairro' => $data['bairro'] ?? '',
            'cidade' => $data['localidade'] ?? '',
            'estado' => $data['uf'] ?? '',
            'complemento' => $data['complemento'] ?? ''
        ]);
    }
}
