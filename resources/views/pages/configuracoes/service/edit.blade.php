@extends('adminlte::page')

@section('title', 'Gerenciar Página Serviços')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/carousel">Gerenciar Página de Serviços</a></li>
            <li class="breadcrumb-item active">Editar Página de Serviços</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1>Editar Página de Serviços</h1>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

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
                <form action="{{ route('admin.service.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="title">Título *</label>
                        <input type="text" name="title" id="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $servicePage->title) }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content">Conteúdo *</label>

                        <textarea name="content" id="content" style="display:none;">
                            {{ old('content', $servicePage->content) }}
                        </textarea>

                        <div id="editor" style="min-height:300px;">{{$servicePage->content}}</div>

                        @error('content')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Tipo de Mídia Principal</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="media_type" id="media_video"
                                   value="video" {{ $servicePage->video_url ? 'checked' : '' }}>
                            <label class="form-check-label" for="media_video">Vídeo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="media_type" id="media_image"
                                   value="image" {{ $servicePage->image_url ? 'checked' : '' }}>
                            <label class="form-check-label" for="media_image">Imagem</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="media">Novo Arquivo de Mídia (opcional)</label>
                        <input type="file" name="media" id="media"
                               class="form-control-file @error('media') is-invalid @enderror">
                        @error('media')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Deixe em branco para manter a mídia atual
                        </small>
                    </div>

                    @if($servicePage->video_url || $servicePage->image_url)
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="remove_media" id="remove_media"
                                       class="form-check-input @error('remove_media') is-invalid @enderror">
                                <label for="remove_media" class="form-check-label">Remover mídia atual</label>
                                @error('remove_media')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-2">
                                @if($servicePage->video_url)
                                    <video src="{{ $servicePage->video_url }}" controls style="max-width: 300px;"></video>
                                @else
                                    <img src="{{ $servicePage->image_url }}" alt="Imagem atual" style="max-width: 300px;">
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="price">Preço do Serviço *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">R$</span>
                            </div>
                            <input type="text" name="price" id="price"
                                   class="form-control @error('price') is-invalid @enderror money-mask"
                                   value="{{ old('price', $servicePage->preco_service) }}" required>
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
                               value="{{ old('alt_text', $servicePage->alt_text) }}">
                        @error('alt_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
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
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{'header': 1}, {'header': 2}],
            [{'list': 'ordered'}, {'list': 'bullet'}],
            [{'script': 'sub'}, {'script': 'super'}],
            [{'indent': '-1'}, {'indent': '+1'}],
            [{'direction': 'rtl'}],
            [{'size': ['small', false, 'large', 'huge']}],
            [{'header': [1, 2, 3, 4, 5, 6, false]}],
            [{'color': []}, {'background': []}],
            [{'font': []}],
            [{'align': []}],
            ['clean'],
            ['link', 'image', 'video']
        ];

        const quill = new Quill('#editor', {
            modules: { toolbar: toolbarOptions },
            theme: 'snow',
            placeholder: 'Digite seu conteúdo aqui...',
        });

        let initialContent = document.getElementById('content').value;
        if (initialContent) {
            try {
                quill.setContents(JSON.parse(initialContent));
            } catch (e) {
                console.warn('Conteúdo salvo não é Delta válido');
            }
        }

        document.querySelector('form').addEventListener('submit', function () {
            let delta = quill.getContents();
            document.getElementById('content').value = JSON.stringify(delta);
        });

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
