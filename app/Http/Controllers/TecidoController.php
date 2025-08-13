<?php

namespace App\Http\Controllers;

use App\Models\Tecido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TecidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tecidos = Tecido::latest()->paginate(10);
        return view('pages.tecidos.index', compact('tecidos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.tecidos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_produto' => 'required|string|max:50|unique:tecido',
            'composicao' => 'required|string|max:50',
            'padrao' => 'required|string|max:100',
            'suavidade' => 'required|string|max:20',
            'tecelagem' => 'required|string|max:50',
            'fio' => 'required|string|max:10',
            'origem' => 'required|string|max:50',
            'fabricante' => 'nullable|string|max:100',
            'peso' => 'required|string|max:50',
            'preco' => 'required|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('imagem')) {
            $validated['imagem_url'] = uploadToR2($request->file('imagem'), 'tecidos');
        } else {
            $validated['imagem_url'] = 'https://pub-64c93879f2ec4956a0a9e00962589232.r2.dev/default-tissues.png';
        }

        Tecido::create($validated);

        return redirect()->route('admin.tecidos.index')
            ->with('success', 'Tecido cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tecido $tecido)
    {
        return view('pages.tecidos.show', compact('tecido'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tecido $tecido)
    {
        return view('pages.tecidos.edit', compact('tecido'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tecido $tecido)
    {
        $validated = $request->validate([
            'nome_produto' => 'required|string|max:50|unique:tecido,nome_produto,' . $tecido->id,
            'composicao' => 'required|string|max:50',
            'padrao' => 'required|string|max:100',
            'suavidade' => 'required|string|max:20',
            'tecelagem' => 'required|string|max:50',
            'fio' => 'required|string|max:10',
            'origem' => 'required|string|max:50',
            'fabricante' => 'nullable|string|max:100',
            'peso' => 'required|string|max:50',
            'preco' => 'required|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        if ($request->hasFile('imagem')) {
            // Remove a imagem antiga caso não seja apadrão
            if (!str_contains($tecido->imagem_url, 'default-fabric.jpg')) {
                deleteFromR2($tecido->imagem_url);
            }

            $validated['imagem_url'] = uploadToR2($request->file('imagem'), 'tecidos');
        }

        $tecido->update($validated);

        return redirect()->route('admin.tecidos.index')
            ->with('success', 'Tecido atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tecido $tecido)
    {
        if (!str_contains($tecido->imagem_url, 'default-fabric.jpg')) {
            deleteFromR2($tecido->imagem_url);
        }

        $tecido->delete();

        return redirect()->route('admin.tecidos.index')
            ->with('success', 'Tecido removido com sucesso!');
    }

    public function datatable()
    {
        $tecidos = Tecido::query();

        return DataTables::eloquent($tecidos)
            ->addColumn('actions', function (Tecido $tecido) {
                return view('pages.tecidos.actions', compact('tecido'))->render();
            })
            ->editColumn('data_cadastro', function (Tecido $tecido) {
                return $tecido->data_cadastro->format('Y-m-d H:i:s');
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    /*
     * public function datatable()
{
    return DataTables::eloquent(Tecido::query())
        ->filter(function ($query) {
            if (request()->has('search') && request('search')['value']) {
                $search = request('search')['value'];
                $query->where('nome_produto', 'like', "%{$search}%")
                      ->orWhere('composicao', 'like', "%{$search}%");
            }
        })
        ->toJson();
}*/

}
