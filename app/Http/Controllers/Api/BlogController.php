<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostBlog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = PostBlog::with(['usuario', 'midias'])
            ->where('publicado', true)
            ->orderBy('data_publicacao', 'desc')
            ->paginate(10);

        return response()->json($posts);
    }

    public function show($slug)
    {
        $post = PostBlog::with(['usuario', 'midias'])
            ->where('slug', $slug)
            ->where('publicado', true)
            ->firstOrFail();

        return response()->json($post);
    }
}
