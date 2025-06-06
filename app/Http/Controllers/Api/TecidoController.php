<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tecido;
use Illuminate\Http\Request;

class TecidoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Tecido::with('imagens')->orderBy('data_cadastro', 'desc');

            if ($request->has('suavidade')) {
                $query->where('suavidade', $request->suavidade);
            }

            if ($request->has('origem')) {
                $query->where('origem', $request->origem);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nome_produto', 'like', "%$search%")
                        ->orWhere('composicao', 'like', "%$search%")
                        ->orWhere('padrao', 'like', "%$search%");
                });
            }

            $tecidos = $query->paginate($request->per_page ?? 15);

            return response()->json([
                'success' => true,
                'data' => $tecidos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar tecidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $tecido = Tecido::with('imagens')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $tecido
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tecido não encontrado'
            ], 404);
        }
    }

    public function opcoesFiltros()
    {
        return response()->json([
            'suavidades' => Tecido::distinct()->pluck('suavidade'),
            'origens' => Tecido::distinct()->pluck('origem'),
            'tecidos' => Tecido::distinct()->pluck('tecelagem')
        ]);
    }
}
