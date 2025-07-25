<a href="{{ route('admin.tecidos.edit', $tecido->id) }}" class="btn btn-sm btn-primary">
    <i class="fas fa-edit"></i>
</a>&nbsp;
<form action="{{ route('admin.tecidos.destroy', $tecido->id) }}" method="POST" style="display:inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">
        <i class="fas fa-trash"></i>
    </button>
</form>

