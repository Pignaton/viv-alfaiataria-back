<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    const CACHE_TIME = 60;

    public function index()
    {
        $posts = Cache::remember('blog_posts_list', self::CACHE_TIME, function () {
            return PostBlog::with(['usuario', 'midias' => function($query) {
                $query->where('destaque', true)
                    ->orWhere('ordem', 1)
                    ->orderBy('ordem', 'asc');
            }])
                ->select([
                    'id',
                    'titulo',
                    'slug',
                    'resumo',
                    'tipo_conteudo',
                    'usuario_id',
                    'data_publicacao',
                    'data_criacao'
                ])
                ->where('publicado', true)
                ->where('data_publicacao', '<=', now())
                ->orderBy('data_publicacao', 'desc')
                ->paginate(10);
        });

        return response()->json([
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'total' => $posts->total(),
        ]);
    }

    public function show($slug)
    {
        try {
            $post = Cache::remember("blog_post_{$slug}", self::CACHE_TIME, function () use ($slug) {
                return PostBlog::with(['usuario', 'midias' => function($query) {
                    $query->orderBy('ordem', 'asc');
                }])
                    ->where('slug', $slug)
                    ->where('publicado', true)
                    ->where('data_publicacao', '<=', now())
                    ->firstOrFail();
            });

            return response()->json($post);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post não encontrado'
            ], 404);
        }
    }

    public function latest()
    {
        $latestPosts = Cache::remember('blog_latest_posts', self::CACHE_TIME, function () {
            return PostBlog::with(['midias' => function($query) {
                $query->where('destaque', true)
                    ->limit(1);
            }])
                ->select([
                    'id',
                    'titulo',
                    'slug',
                    'resumo',
                    'tipo_conteudo',
                    'data_publicacao'
                ])
                ->where('publicado', true)
                ->where('data_publicacao', '<=', now())
                ->orderBy('data_publicacao', 'desc')
                ->limit(3)
                ->get();
        });

        return response()->json($latestPosts);
    }
}
