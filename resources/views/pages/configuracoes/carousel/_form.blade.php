{{-- resources/views/admin/carousel/_form.blade.php --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="title">Título</label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $carousel->title ?? '') }}">
            @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="subtitle">Subtítulo</label>
            <input type="text" name="subtitle" id="subtitle" class="form-control @error('subtitle') is-invalid @enderror"
                   value="{{ old('subtitle', $carousel->subtitle ?? '') }}">
            @error('subtitle')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="media_type">Tipo de Mídia</label>
    <select name="media_type" id="media_type" class="form-control @error('media_type') is-invalid @enderror">
        <option value="image" {{ old('media_type', $carousel->media_type ?? '') == 'image' ? 'selected' : '' }}>Imagem</option>
        <option value="video" {{ old('media_type', $carousel->media_type ?? '') == 'video' ? 'selected' : '' }}>Vídeo</option>
    </select>
    @error('media_type')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="media">Arquivo de Mídia</label>
    <input type="file" name="media" id="media" class="form-control-file @error('media') is-invalid @enderror"
        {{ !isset($carousel) ? 'required' : '' }}>
    @error('media')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if(isset($carousel) && $carousel->media_url)
        <div class="mt-2">
            @if($carousel->media_type == 'image')
                <img src="{{ $carousel->media_url }}" alt="Preview" style="max-width: 200px;" class="img-thumbnail">
            @else
                <video src="{{ $carousel->media_url }}" style="max-width: 200px;" class="img-thumbnail" controls></video>
            @endif
            <p class="text-muted mt-1">Mídia atual. Selecione um novo arquivo para substituir.</p>
        </div>
    @endif
</div>

<div class="form-group">
    <label for="alt_text">Texto Alternativo (SEO)</label>
    <input type="text" name="alt_text" id="alt_text" class="form-control @error('alt_text') is-invalid @enderror"
           value="{{ old('alt_text', $carousel->alt_text ?? '') }}">
    @error('alt_text')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Descrição para acessibilidade e SEO (max 100 caracteres)</small>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="order">Ordem de Exibição</label>
            <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror"
                   value="{{ old('order', $carousel->order ?? 0) }}" min="0">
            @error('order')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="is_active">Status</label>
            <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                <option value="1" {{ old('is_active', $carousel->is_active ?? 1) ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ !old('is_active', $carousel->is_active ?? 1) ? 'selected' : '' }}>Inativo</option>
            </select>
            @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
