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
    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo',
        'usuario_id',
        'endereco_entrega_id',
        'status',
        'codigo_rastreio',
        'data_envio',
        'subtotal',
        'desconto',
        'frete',
        'total',
        'observacoes',
        'guest_id',
        'metodo_pagamento',
        'dados_cliente',
        'endereco_entrega',
        'dados_pagamento'
    ];

    protected $dates = [
        'data_pedido',
        'data_envio',
        'data_atualizacao'
    ];

    protected $casts = [
        'data_pedido' => 'datetime',
        'data_atualizacao' => 'datetime',
        'subtotal' => 'decimal:2',
        'desconto' => 'decimal:2',
        'frete' => 'decimal:2',
        'total' => 'decimal:2',
        'dados_pagamento' => 'array',
        'dados_cliente' => 'array',
        'endereco_entrega' => 'array'
    ];

    const STATUS = [
        'pendente' => 'Pendente',
        'pago' => 'Pago',
        'processando' => 'Processando',
        'enviado' => 'Enviado',
        'entregue' => 'Entregue',
        'cancelado' => 'Cancelado',
        'aguardando_pagamento' => 'Aguardando Pagamento',
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
        if ($status && in_array($status, array_keys(self::STATUS))) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'pedido_id');
    }

    public function getSubtotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }

    public function getTrackingUrlAttribute()
    {
        if (!$this->codigo_rastreio) {
            return null;
        }
        return "https://www.linkcorreios.com.br/?id={$this->codigo_rastreio}";
    }

    public function podeSerEnviado()
    {
        return $this->status === 'pago' || $this->status === 'processando';
    }

}
