@extends('adminlte::page')

@section('title', 'Gerenciar Blog')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/blog">Gerenciar Blog</a></li>
            <li class="breadcrumb-item active">Visualizar Post</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalhes do Post</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2>{{ $post->titulo }}</h2>
                    <p class="text-muted">
                        Por {{ $post->usuario->nome ?? 'N/A' }} em
                        {{ $post->data_criacao->format('d/m/Y H:i') }}
                        @if($post->data_publicacao)
                            | Publicação: {{ $post->data_publicacao->format('d/m/Y H:i') }}
                        @endif
                    </p>

                    @if($post->imagemDestaque)
                        <div class="mb-4">
                            <img src="{{ $post->imagemDestaque->url }}" alt="{{ $post->titulo }}"
                                 class="img-fluid rounded">
                            @if($post->imagemDestaque->legenda)
                                <p class="text-muted mt-2">{{ $post->imagemDestaque->legenda }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="content">
                        {!! $post->conteudo !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Informações do Post</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($post->publicado && $post->data_publicacao <= now())
                                            <span class="badge bg-success">Publicado</span>
                                        @elseif($post->publicado)
                                            <span class="badge bg-warning">Agendado</span>
                                        @else
                                            <span class="badge bg-secondary">Rascunho</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>
                                        @switch($post->tipo_conteudo)
                                            @case('video') <span class="badge bg-info">Vídeo</span> @break
                                            @case('galeria') <span class="badge bg-success">Galeria</span> @break
                                            @default <span class="badge bg-secondary">Padrão</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td><code>{{ $post->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Mídias:</th>
                                    <td>{{ $post->midias->count() }}</td>
                                </tr>
                            </table>

                            <h6 class="mt-4">Galeria de Mídias</h6>
                            <div class="row">
                                @foreach($post->midias->where('destaque', false) as $midia)
                                    <div class="col-6 mb-3">
                                        @if($midia->tipo === 'imagem')
                                            <img src="{{ $midia->url }}" alt="Mídia {{ $loop->iteration }}"
                                                 class="img-thumbnail w-100">
                                        @else
                                            <div class="embed-responsive embed-responsive-16by9">
                                                <video controls class="embed-responsive-item">
                                                    <source src="{{ $midia->url }}" type="video/mp4">
                                                    Seu navegador não suporta vídeos.
                                                </video>
                                            </div>
                                        @endif
                                        @if($midia->legenda)
                                            <p class="small text-muted mt-1">{{ $midia->legenda }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
@endsection
