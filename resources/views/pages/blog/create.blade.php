@extends('adminlte::page')

@section('title', 'Gerenciar Blog')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/blog">Gerenciar Blog</a></li>
            <li class="breadcrumb-item active">Adicionar Post</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Criar Novo Post</h3>
            <div class="card-tools">
                <a href="{{ route('admin.blog.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="titulo">Título *</label>
                            <input type="text" name="titulo" id="titulo"
                                   class="form-control @error('titulo') is-invalid @enderror"
                                   value="{{ old('titulo') }}" required maxlength="120">
                            @error('titulo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="resumo">Resumo</label>
                            <textarea name="resumo" id="resumo" rows="3"
                                      class="form-control @error('resumo') is-invalid @enderror"
                                      maxlength="255">{{ old('resumo') }}</textarea>
                            @error('resumo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <small class="text-muted">Máximo 255 caracteres</small>
                        </div>

                        <div class="form-group">
                            <label for="conteudo">Conteúdo *</label>
                            <textarea name="conteudo" id="conteudo"
                                      class="form-control summernote @error('conteudo') is-invalid @enderror"
                                      required>{{ old('conteudo') }}</textarea>
                            @error('conteudo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tipo_conteudo">Tipo de Conteúdo *</label>
                            <select name="tipo_conteudo" id="tipo_conteudo"
                                    class="form-control @error('tipo_conteudo') is-invalid @enderror" required>
                                @foreach($tiposConteudo as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipo_conteudo') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_conteudo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="imagem_destaque">Imagem de Destaque</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('imagem_destaque') is-invalid @enderror"
                                       id="imagem_destaque" name="imagem_destaque">
                                <label class="custom-file-label" for="imagem_destaque">Escolher arquivo...</label>
                                @error('imagem_destaque')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Formatos: JPEG, PNG, JPG, GIF. Tamanho máximo: 2MB
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="midias">Outras Mídias</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('midias') is-invalid @enderror"
                                       id="midias" name="midias[]" multiple>
                                <label class="custom-file-label" for="midias">Escolher arquivos...</label>
                                @error('midias')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Formatos: Imagens (JPEG, PNG, JPG, GIF) ou Vídeos (MP4, MOV, AVI). Tamanho máximo: 5MB cada
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="publicado" name="publicado"
                                       value="1" {{ old('publicado') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="publicado">Publicar</label>
                            </div>
                        </div>

                        <div class="form-group" id="data_publicacao_group">
                            <label for="data_publicacao">Data de Publicação</label>
                            <input type="datetime-local" name="data_publicacao" id="data_publicacao"
                                   class="form-control @error('data_publicacao') is-invalid @enderror"
                                   value="{{ old('data_publicacao') }}">
                            @error('data_publicacao')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Post
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('#publicado').change(function() {
            $('#data_publicacao_group').toggle(this.checked);
        }).trigger('change');
    </script>
@endsection
