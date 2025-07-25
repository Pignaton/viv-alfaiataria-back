<a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i>
</a>
<a href="{{ route('admin.pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-primary">
    <i class="fas fa-edit"></i>
</a>
<!--<form action="{{-- route('admin.pedidos.destroy', $pedido->id) --}}" method="POST" style="display:inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">
        <i class="fas fa-trash"></i>
    </button>
</form>-->
