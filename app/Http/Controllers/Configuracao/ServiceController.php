<?php

namespace App\Http\Controllers\Configuracao;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function index()
    {
        $defaultContent = [
            'title' => 'Nossos Serviços',
            'content' => '<p>Estamos preparando conteúdo especial para você.</p>',
            'video_url' => null,
            'image_url' => null
        ];

        $pageData = Service::first();

        if (!$pageData) {
            return response()->json(['data' => $defaultContent]);
        }

        $responseData = array_merge($defaultContent, [
            'title' => $pageData->title ?: $defaultContent['title'],
            'content' => $pageData->content ?: $defaultContent['content'],
            'video_url' => $pageData->video_url,
            'image_url' => $pageData->image_url
        ]);

        return response()->json(['data' => $responseData]);
    }

    public function create()
    {
        if (Service::exists()) {
            return redirect()->route('admin.service.edit')
                ->with('info', 'A página de serviços já foi criada. Você pode editá-la abaixo.');
        }

        return view('pages.configuracoes.service.create', [
            'useCkeditor' => true
        ]);
    }

    public function store(Request $request)
    {

        if (Service::exists()) {
            return redirect()->route('admin.service.edit')
                ->with('error', 'A página de serviços já existe.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'media_type' => 'required|in:video,image',
            'media' => 'required|file|mimetypes:video/mp4,video/quicktime,image/jpeg,image/png,image/webp',
            'alt_text' => 'nullable|string|max:100',
            'price' => 'required|numeric',
        ]);

        $mediaPath = null;

        if ($request->hasFile('media')) {
            $directory = $validated['media_type'] === 'video' ? 'services/videos' : 'services/images';
            $mediaPath = uploadToR2($request->file('media'), $directory);
        }

        $validated['preco_service'] = str_replace(['.', ','], ['', '.'], $request->price);

        Service::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'video_url' => $validated['media_type'] === 'video' ? $mediaPath : null,
            'image_url' => $validated['media_type'] === 'image' ? $mediaPath : null,
            'media_type' => $validated['media_type'],
            'alt_text' => $validated['alt_text'] ?? null,
            'preco_service' => $validated['preco_service'] ?? null
        ]);

        return redirect()->route('admin.service.edit')
            ->with('success', 'Página de serviços criada com sucesso!');
    }

    public function edit()
    {
        $servicePage = Service::first();

        if (!$servicePage) {
            return redirect()->route('admin.service.create')
                ->with('info', 'Por favor, crie a página de serviços primeiro.');
        }

        return view('pages.configuracoes.service.edit', compact('servicePage'));
    }

    public function update(Request $request)
    {
        $servicePage = Service::firstOrFail();

        $request->merge([
            'price' => str_replace(['.', ','], ['', '.'], $request->price)
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'media_type' => 'required|in:video,image',
            'media' => 'nullable|file|mimetypes:video/mp4,video/quicktime,image/jpeg,image/png,image/webp',
            'alt_text' => 'nullable|string|max:100',
            'remove_media' => 'nullable|boolean',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $validated['preco_service'] =  $request->price;

        if ($request->input('remove_media')) {
            if ($servicePage->video_url) {
                deleteFromR2($servicePage->video_url);
                $servicePage->video_url = null;
            }
            if ($servicePage->image_url) {
                deleteFromR2($servicePage->image_url);
                $servicePage->image_url = null;
            }
        }

        if ($request->hasFile('media')) {
            // Remove mídia antiga
            if ($servicePage->video_url) {
                deleteFromR2($servicePage->video_url);
            }
            if ($servicePage->image_url) {
                deleteFromR2($servicePage->image_url);
            }

            $directory = $validated['media_type'] === 'video' ? 'services/videos' : 'services/images';
            $mediaPath = uploadToR2($request->file('media'), $directory);

            if ($validated['media_type'] === 'video') {
                $servicePage->video_url = $mediaPath;
                $servicePage->image_url = null;
            } else {
                $servicePage->image_url = $mediaPath;
                $servicePage->video_url = null;
            }
        }

        $servicePage->title = $validated['title'];
        $servicePage->content = $validated['content'];
        $servicePage->alt_text = $validated['alt_text'] ?? null;
        $servicePage->preco_service = $validated['preco_service'] ?? null;
        $servicePage->save();

        return redirect()->route('admin.service.edit')
            ->with('success', 'Página de serviços atualizada com sucesso!');
    }

    public function getServicePrecoImage()
    {
        $defaultContent = [
            'preco_service' => null,
            'image_cart' => null
        ];

        $pageData = Service::first();

        if (!$pageData) {
            return response()->json(['data' => $defaultContent]);
        }

        $responseData = array_merge($defaultContent, [
            'preco_service' => $pageData->preco_service,
            'image_cart' => $pageData->image_cart
        ]);

        return response()->json(['data' => $responseData]);
    }
}
