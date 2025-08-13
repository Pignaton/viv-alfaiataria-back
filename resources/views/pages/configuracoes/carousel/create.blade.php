@extends('adminlte::page')

@section('title', 'Gerenciar Carousel')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/carousel">Gerenciar Carousel</a></li>
            <li class="breadcrumb-item active">Adicionar Carousel</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1>Adicionar Item ao Carrossel</h1>
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
                <form action="{{ route('admin.carousel.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('pages.configuracoes.carousel._form')

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Salvar Item</button>
                        <a href="{{ route('admin.carousel.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
