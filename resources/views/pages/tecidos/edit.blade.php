@extends('adminlte::page')

@section('title', 'Gerenciar Cliente')

@section('content_header')
    <div class="col-sm-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/tecidos">Gerenciar Tecidos</a></li>
            <li class="breadcrumb-item active">Editar Tecido</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Tecido</h3>
            <div class="card-tools">
                <a href="{{ route('admin.tecidos.show', $tecido->id) }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.tecidos.update', $tecido->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_produto">Nome do Produto *</label>
                            <input type="text" name="nome_produto" id="nome_produto"
                                   class="form-control @error('nome_produto') is-invalid @enderror"
                                   value="{{ old('nome_produto', $tecido->nome_produto) }}" required maxlength="20">
                            @error('nome_produto')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="composicao">Composição *</label>
                            <input type="text" name="composicao" id="composicao"
                                   class="form-control @error('composicao') is-invalid @enderror"
                                   value="{{ old('composicao', $tecido->composicao) }}" required maxlength="50">
                            @error('composicao')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="padrao">Padrão *</label>
                            <input type="text" name="padrao" id="padrao"
                                   class="form-control @error('padrao') is-invalid @enderror"
                                   value="{{ old('padrao', $tecido->padrao) }}" required maxlength="100">
                            @error('padrao')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="suavidade">Suavidade *</label>
                            <select name="suavidade" id="suavidade"
                                    class="form-control @error('suavidade') is-invalid @enderror" required>
                                <option value="Macio" {{ old('suavidade', $tecido->suavidade) == 'Macio' ? 'selected' : '' }}>Macio</option>
                                <option value="Médio" {{ old('suavidade', $tecido->suavidade) == 'Médio' ? 'selected' : '' }}>Médio</option>
                                <option value="Áspero" {{ old('suavidade', $tecido->suavidade) == 'Áspero' ? 'selected' : '' }}>Áspero</option>
                                <option value="Liso" {{ old('suavidade', $tecido->suavidade) == 'Liso' ? 'selected' : '' }}>Liso</option>
                                <option value="Texturizado" {{ old('suavidade', $tecido->suavidade) == 'Texturizado' ? 'selected' : '' }}>Texturizado</option>
                            </select>
                            @error('suavidade')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tecelagem">Tecelagem *</label>
                            <input type="text" name="tecelagem" id="tecelagem"
                                   class="form-control @error('tecelagem') is-invalid @enderror"
                                   value="{{ old('tecelagem', $tecido->tecelagem) }}" required maxlength="50">
                            @error('tecelagem')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fio">Fio *</label>
                            <input type="text" name="fio" id="fio"
                                   class="form-control @error('fio') is-invalid @enderror"
                                   value="{{ old('fio', $tecido->fio) }}" required maxlength="10">
                            @error('fio')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="origem">Origem *</label>
                            <input type="text" name="origem" id="origem"
                                   class="form-control @error('origem') is-invalid @enderror"
                                   value="{{ old('origem', $tecido->origem) }}" required maxlength="50">
                            @error('origem')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fabricante">Fabricante</label>
                            <input type="text" name="fabricante" id="fabricante"
                                   class="form-control @error('fabricante') is-invalid @enderror"
                                   value="{{ old('fabricante', $tecido->fabricante) }}" maxlength="100">
                            @error('fabricante')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="peso">Peso *</label>
                            <input type="text" name="peso" id="peso"
                                   class="form-control @error('peso') is-invalid @enderror"
                                   value="{{ old('peso', $tecido->peso) }}" required maxlength="50">
                            @error('peso')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preco">Preço (R$) *</label>
                                    <input type="number" name="preco" id="preco" step="0.01" min="0"
                                           class="form-control @error('preco') is-invalid @enderror"
                                           value="{{ old('preco', $tecido->preco) }}" required>
                                    @error('preco')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preco_promocional">Preço Promocional (R$)</label>
                                    <input type="number" name="preco_promocional" id="preco_promocional" step="0.01" min="0"
                                           class="form-control @error('preco_promocional') is-invalid @enderror"
                                           value="{{ old('preco_promocional', $tecido->preco_promocional) }}">
                                    @error('preco_promocional')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Imagem Atual</label>
                            <div>
                                <img src="{{ $tecido->imagem_url }}" alt="{{ $tecido->nome_produto }}"
                                     class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="imagem">Alterar Imagem</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('imagem') is-invalid @enderror"
                                       id="imagem" name="imagem">
                                <label class="custom-file-label" for="imagem">Escolher nova imagem...</label>
                                @error('imagem')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Deixe em branco para manter a imagem atual
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Atualizar Tecido
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Mostrar nome do arquivo selecionado
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
@endsection
