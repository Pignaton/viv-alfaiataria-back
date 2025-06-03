<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    use HasFactory;

    protected $table = 'item_pedido';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pedido_id',
        'camisa_personalizada_id',
        'tecido_id',
        'quantidade',
        'preco_unitario'
    ];

    protected $casts = [
        'preco_unitario' => 'decimal:2'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function camisaPersonalizada()
    {
        return $this->belongsTo(CamisaPersonalizada::class, 'camisa_personalizada_id');
    }

    public function tecido()
    {
        return $this->belongsTo(Tecido::class, 'tecido_id');
    }

    public function getPrecoTotalAttribute()
    {
        return $this->preco_unitario * $this->quantidade;
    }
}
