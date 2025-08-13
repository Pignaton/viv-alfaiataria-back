<?php

namespace App\Http\Controllers\Configuracao;

use App\Http\Controllers\Controller;
use App\Models\CarouselImage;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    public function index()
    {
        $images = CarouselImage::ordered()->get();
        return view('pages.configuracoes.carousel.index', compact('images'));
    }

    public function create()
    {
        return view('pages.configuracoes.carousel.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string|max:200',
            'media' => 'required|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi|max:10240', // 10 MB
            'media_type' => 'required|in:image,video',
            'alt_text' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        // Garantir que o arquivo existe no request
        if (!$request->hasFile('media')) {
            return back()->with('error', 'Nenhum arquivo enviado.');
        }

        $file = $request->file('media');

// Se o upload falhou, mostra erro detalhado
        if (!$file->isValid()) {
            return back()->with('error', 'Falha no upload: [' . $file->getError() . '] ' . $file->getErrorMessage());
        }

// Faz o upload para o R2
        $mediaUrl = uploadToR2(
            $file,
            'carousel/' . $validated['media_type'],
        );

// Salva no banco
        CarouselImage::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'media_url' => $mediaUrl,
            'media_type' => $validated['media_type'],
            'alt_text' => $validated['alt_text'] ?? '',
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true
        ]);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Item do carrossel adicionado com sucesso!');
    }

    public function edit(CarouselImage $carousel)
    {
        return view('pages.configuracoes.carousel.edit', compact('carousel'));
    }

    public function update(Request $request, CarouselImage $carousel)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1048576',
            'alt_text' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        // Atualizar imagem se fornecida
        if ($request->hasFile('image')) {
            // Deletar imagem antiga se existir
            $this->deleteImage($carousel->media_url);

            // Fazer upload da nova imagem
            $validated['image_url'] = $this->uploadImage($request->file('image'));
        }

        $carousel->update($validated);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Imagem do carrossel atualizada com sucesso!');
    }

    public function destroy(CarouselImage $carousel)
    {
        // Deletar imagem do storage
        $this->deleteImage($carousel->media_url);

        // Deletar registro
        $carousel->delete();

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Imagem do carrossel removida com sucesso!');
    }

    protected function uploadImage($image)
    {
        return uploadToR2($image, 'carousel');
    }

    protected function deleteImage($imageUrl)
    {
        if (!str_contains($imageUrl, 'default-fabric.jpg')) {
            deleteFromR2($imageUrl);
        }

    }
}
