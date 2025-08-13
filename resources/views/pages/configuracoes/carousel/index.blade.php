@extends('adminlte::page')

@section('title', 'Gerenciar Carousel')

@section('content_header')
    <h1 class="m-0 text-dark">
        <small>Gerenciar Carousel</small>
    </h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-between mb-4">
            <div class="col-md-6">
                <h1>Gerenciar Carrossel</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.carousel.create') }}" class="btn btn-primary">
                    Adicionar Imagem
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Título</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($images as $image)
                            <tr>
                                <td>
                                    <img src="{{ $image->media_url }}" alt="{{ $image->alt_text }}" style="max-width: 100px;">
                                </td>
                                <td>{{ $image->title }}</td>
                                <td>{{ $image->order }}</td>
                                <td>
                                    <span class="badge bg-{{ $image->is_active ? 'success' : 'danger' }}">
                                        {{ $image->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.carousel.edit', $image->id) }}" class="btn btn-sm btn-primary">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.carousel.destroy', $image->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
