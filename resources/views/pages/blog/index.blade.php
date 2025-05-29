@extends('adminlte::page')

@section('title', 'Gerenciar Blog')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/blog">Gerenciar Blog</a></li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Posts do Blog</h3>
            <div class="card-tools">
                <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Post
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th style="width: 10%">Imagem</th>
                    <th>Título</th>
                    <th style="width: 15%">Autor</th>
                    <th style="width: 10%">Tipo</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 15%">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($posts as $post)
                    <tr>
                        <td class="text-center">
                            @if($post->imagemDestaque)
                                <img src="{{ $post->imagemDestaque->url }}" alt="{{ $post->titulo }}"
                                     style="max-width: 80px; max-height: 60px;" class="img-thumbnail">
                            @else
                                <span class="text-muted">Sem imagem</span>
                            @endif
                        </td>
                        <td>{{ $post->titulo }}</td>
                        <td>{{ $post->usuario->nome ?? 'N/A' }}</td>
                        <td>
                            @switch($post->tipo_conteudo)
                                @case('video') <span class="badge bg-info">Vídeo</span> @break
                                @case('galeria') <span class="badge bg-success">Galeria</span> @break
                                @default <span class="badge bg-secondary">Padrão</span>
                            @endswitch
                        </td>
                        <td>
                            @if($post->publicado && $post->data_publicacao <= now())
                                <span class="badge bg-success">Publicado</span>
                            @elseif($post->publicado)
                                <span class="badge bg-warning">Agendado</span>
                            @else
                                <span class="badge bg-secondary">Rascunho</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.blog.show', $post->id) }}" class="btn btn-sm btn-info" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.blog.destroy', $post->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir este post?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
