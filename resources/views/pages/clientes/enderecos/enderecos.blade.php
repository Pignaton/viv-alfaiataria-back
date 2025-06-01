@extends('admin.layout')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Meus Endereços</h3>
            <div class="card-tools">
                <a href="{{ route('admin.clientes.enderecos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Endereço
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($enderecos->isEmpty())
                <div class="alert alert-info">
                    Você ainda não possui endereços cadastrados.
                </div>
            @else
                <div class="row">
                    @foreach($enderecos as $endereco)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 {{ $endereco->principal ? 'border-primary' : '' }}">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        @if($endereco->principal)
                                            <span class="badge badge-primary">Principal</span>
                                        @endif
                                        {{ $endereco->apelido ?? 'Endereço #' . $loop->iteration }}
                                    </h4>
                                    <div class="card-tools">
                                        <a href="{{ route('admin.clientes.enderecos.edit', $endereco->id) }}" class="btn btn-tool">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.clientes.enderecos.destroy', $endereco->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-tool text-danger" onclick="return confirm('Remover este endereço?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <strong>CEP:</strong> {{ $endereco->cep_formatado }}<br>
                                        <strong>Endereço:</strong> {{ $endereco->endereco_completo }}
                                    </p>
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="entrega{{ $endereco->id }}"
                                               {{ $endereco->entrega ? 'checked' : '' }} disabled>
                                        <label class="custom-control-label" for="entrega{{ $endereco->id }}">Usar para entrega</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
