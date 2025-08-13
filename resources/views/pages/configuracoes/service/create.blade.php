@extends('adminlte::page')

@section('title', 'Gerenciar Carousel')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/carousel">Gerenciar Página de Serviços</a></li>
            <li class="breadcrumb-item active">Criar Página de Serviços</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1>Criar Página de Serviços</h1>
                <p class="text-muted">Preencha os campos abaixo para criar a página de serviços</p>
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
                <form action="{{ route('admin.service.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="title">Título *</label>
                        <input type="text" name="title" id="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content">Conteúdo *</label>
                        <textarea name="content" id="content" rows="10"
                                  class="form-control @error('content') is-invalid @enderror"
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Tipo de Mídia Principal *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="media_type" id="media_video"
                                   value="video" {{ old('media_type') == 'video' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="media_video">Vídeo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="media_type" id="media_image"
                                   value="image" {{ old('media_type') == 'image' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="media_image">Imagem</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="media">Arquivo de Mídia *</label>
                        <input type="file" name="media" id="media"
                               class="form-control-file @error('media') is-invalid @enderror" required>
                        @error('media')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Formatos aceitos: JPG, PNG, WEBP (até 2MB) ou MP4, MOV (até 10MB)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="price">Preço do Serviço *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">R$</span>
                            </div>
                            <input type="text" name="price" id="price"
                                   class="form-control @error('price') is-invalid @enderror money-mask"
                                   value="{{ old('price') }}" required>
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Informe o valor do serviço (use vírgula para centavos)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="alt_text">Texto Alternativo (SEO)</label>
                        <input type="text" name="alt_text" id="alt_text"
                               class="form-control @error('alt_text') is-invalid @enderror"
                               value="{{ old('alt_text') }}">
                        @error('alt_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Descrição para acessibilidade e SEO (max 100 caracteres)
                        </small>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Criar Página</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"/>

    <style>
        .ql-toolbar {
            border-radius: 4px 4px 0 0 !important;
            border: 1px solid #ddd !important;
            background-color: #f8f9fa !important;
        }

        .ql-container {
            border-radius: 0 0 4px 4px !important;
            border: 1px solid #ddd !important;
            font-family: inherit !important;
        }

        .ql-editor {
            min-height: 300px;
            font-size: 16px;
        }

        /* Dark mode */
        .dark-mode .ql-toolbar {
            background-color: #343a40 !important;
            border-color: #6c757d !important;
        }

        .dark-mode .ql-container {
            background-color: #454d55 !important;
            border-color: #6c757d !important;
            color: #f8f9fa !important;
        }

        .money-mask {
            text-align: right;
            font-weight: bold;
        }

        .input-group-text {
            font-weight: bold;
            background-color: #e9ecef;
        }
    </style>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],

            [{'header': 1}, {'header': 2}],               // custom button values
            [{'list': 'ordered'}, {'list': 'bullet'}],
            [{'script': 'sub'}, {'script': 'super'}],      // superscript/subscript
            [{'indent': '-1'}, {'indent': '+1'}],          // outdent/indent
            [{'direction': 'rtl'}],                         // text direction

            [{'size': ['small', false, 'large', 'huge']}],  // custom dropdown
            [{'header': [1, 2, 3, 4, 5, 6, false]}],

            [{'color': []}, {'background': []}],          // dropdown with defaults
            [{'font': []}],
            [{'align': []}],

            ['clean'],                                        // remove formatting
            ['link', 'image', 'video']                        // media
        ];

        const options = {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: 'Digite seu conteúdo aqui...',
        };

        new Quill('#content', options);

        document.addEventListener('DOMContentLoaded', function() {
            const priceInput = document.getElementById('price');

            priceInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = (value / 100).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                e.target.value = value;
            });

            if(priceInput.value) {
                let value = priceInput.value.replace(/\D/g, '');
                priceInput.value = (value / 100).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        });
    </script>
@endsection


