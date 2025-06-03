<?php

namespace App\Http\Controllers;

use App\Models\PostBlog;
use App\Models\MidiaBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = PostBlog::with(['usuario', 'imagemDestaque'])
            ->latest('data_criacao')
            ->paginate(10);

        return view('pages.blog.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposConteudo = [
            'padrao' => 'Padrão',
            'video' => 'Vídeo',
            'galeria' => 'Galeria'
        ];

        return view('pages.blog.create', compact('tiposConteudo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:120',
            'conteudo' => 'required|string',
            'resumo' => 'nullable|string|max:255',
            'tipo_conteudo' => 'required|in:padrao,video,galeria',
            'publicado' => 'boolean',
            'data_publicacao' => 'nullable|date',
            'imagem_destaque' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'midias.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:5120'
        ]);

        $post = PostBlog::create([
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'resumo' => $request->resumo,
            'tipo_conteudo' => $request->tipo_conteudo,
            'publicado' => $request->publicado ?? false,
            'data_publicacao' => $request->data_publicacao ?: ($request->publicado ? now() : null)
        ]);

        // Upload de imagem de destaque
        if ($request->hasFile('imagem_destaque')) {
            $path = $request->file('imagem_destaque')->store('public/blog');
            $post->midias()->create([
                'tipo' => 'imagem',
                'url' => Storage::url($path),
                'destaque' => true,
                'ordem' => 0
            ]);
        }

        // Upload de outras mídias
        if ($request->hasFile('midias')) {
            foreach ($request->file('midias') as $key => $midia) {
                $path = $midia->store('public/blog');
                $tipo = Str::startsWith($midia->getMimeType(), 'video') ? 'video' : 'imagem';

                $post->midias()->create([
                    'tipo' => $tipo,
                    'url' => Storage::url($path),
                    'ordem' => $key + 1
                ]);
            }
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = PostBlog::with(['usuario', 'midias'])->findOrFail($id);
        //$post->load(['usuario', 'midias']);

        return view('pages.blog.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tiposConteudo = [
            'padrao' => 'Padrão',
            'video' => 'Vídeo',
            'galeria' => 'Galeria'
        ];

        $post = PostBlog::with(['usuario', 'midias'])->findOrFail($id);
        //$post->load(['usuario', 'midias']);
        return view('pages.blog.edit', compact('post', 'tiposConteudo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PostBlog $post)
    {
        $request->validate([
            'titulo' => 'required|string|max:120',
            'conteudo' => 'required|string',
            'resumo' => 'nullable|string|max:255',
            'tipo_conteudo' => 'required|in:padrao,video,galeria',
            'publicado' => 'boolean',
            'data_publicacao' => 'nullable|date',
            'imagem_destaque' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'midias.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:5120',
            'midias_removidas' => 'nullable|array',
            'midias_removidas.*' => 'exists:midia_blog,id'
        ]);

        $post->update([
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'resumo' => $request->resumo,
            'tipo_conteudo' => $request->tipo_conteudo,
            'publicado' => $request->publicado ?? false,
            'data_publicacao' => $request->data_publicacao ?: ($request->publicado ? now() : null)
        ]);

        // Remover mídias marcadas para exclusão
        if ($request->filled('midias_removidas')) {
            $midiasRemovidas = MidiaBlog::whereIn('id', $request->midias_removidas)->get();

            foreach ($midiasRemovidas as $midia) {
                Storage::delete(str_replace('/storage', 'public', $midia->url));
                if ($midia->thumbnail) {
                    Storage::delete(str_replace('/storage', 'public', $midia->thumbnail));
                }
                $midia->delete();
            }
        }

        // Upload de nova imagem de destaque
        if ($request->hasFile('imagem_destaque')) {
            // Remove a imagem de destaque atual se existir
            $post->imagemDestaque?->delete();

            $path = $request->file('imagem_destaque')->store('public/blog');
            $post->midias()->create([
                'tipo' => 'imagem',
                'url' => Storage::url($path),
                'destaque' => true,
                'ordem' => 0
            ]);
        }

        // novas mídias
        if ($request->hasFile('midias')) {
            $ultimaOrdem = $post->midias()->max('ordem') ?? 0;

            foreach ($request->file('midias') as $key => $midia) {
                $path = $midia->store('public/blog');
                $tipo = Str::startsWith($midia->getMimeType(), 'video') ? 'video' : 'imagem';

                $post->midias()->create([
                    'tipo' => $tipo,
                    'url' => Storage::url($path),
                    'ordem' => $ultimaOrdem + $key + 1
                ]);
            }
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostBlog $post)
    {
        // Remove midas associadas
        foreach ($post->midias as $midia) {
            Storage::delete(str_replace('/storage', 'public', $midia->url));
            if ($midia->thumbnail) {
                Storage::delete(str_replace('/storage', 'public', $midia->thumbnail));
            }
        }

        $post->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post removido com sucesso!');
    }
}
