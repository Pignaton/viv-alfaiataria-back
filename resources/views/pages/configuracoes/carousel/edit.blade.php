@extends('adminlte::page')

@section('title', 'Gerenciar Carousel')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/carousel">Gerenciar Carousel</a></li>
            <li class="breadcrumb-item active">Editar Carousel</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1>Editar Item do Carrossel</h1>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.carousel.update', $carousel->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('pages.configuracoes.carousel._form')

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Atualizar Item</button>
                        <a href="{{ route('admin.carousel.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
