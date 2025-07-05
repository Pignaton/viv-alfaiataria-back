<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'data_pedido';

    const UPDATED_AT = 'data_atualizacao';

    protected $table = 'pedido';
    //protected $primaryKey = 'id';

    protected $fillable = [
        'codigo',
        'usuario_id',
        'endereco_entrega_id',
        'status',
        'subtotal',
        'desconto',
        'frete',
        'total',
        'observacoes'
    ];

    protected $casts = [
        'data_pedido' => 'datetime',
        'data_atualizacao' => 'datetime',
        'subtotal' => 'decimal:2',
        'desconto' => 'decimal:2',
        'frete' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    const STATUS = [
        'pendente' => 'Pendente',
        'pago' => 'Pago',
        'processando' => 'Processando',
        'enviado' => 'Enviado',
        'entregue' => 'Entregue',
        'cancelado' => 'Cancelado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function enderecoEntrega()
    {
        return $this->belongsTo(Endereco::class, 'endereco_entrega_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }

    public function getStatusFormatadoAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getDataPedidoFormatadaAttribute()
    {
        return $this->data_pedido->format('d/m/Y H:i');
    }

    public function scopeFiltrarPorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'pedido_id');
    }
}
