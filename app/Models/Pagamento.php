<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamento';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pedido_id',
        'metodo_id',
        'tipo_pagamento',
        'status',
        'valor',
        'codigo_transacao'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_criacao' => 'datetime'
    ];

    const TIPOS_PAGAMENTO = [
        'cartao' => 'Cartão de Crédito',
        'pix' => 'PIX',
        'boleto' => 'Boleto Bancário'
    ];

    const STATUS = [
        'pendente' => 'Pendente',
        'processando' => 'Processando',
        'aprovado' => 'Aprovado',
        'recusado' => 'Recusado',
        'reembolsado' => 'Reembolsado',
        'cancelado' => 'Cancelado'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function metodoPagamento()
    {
        return $this->belongsTo(MetodoPagamento::class, 'metodo_id');
    }

    public function pix()
    {
        return $this->hasOne(PagamentoPix::class, 'pagamento_id');
    }

    public function boleto()
    {
        return $this->hasOne(PagamentoBoleto::class, 'pagamento_id');
    }

    public function reembolsos()
    {
        return $this->hasMany(Reembolso::class, 'pagamento_id');
    }

    public function getTipoFormatadoAttribute()
    {
        return self::TIPOS_PAGAMENTO[$this->tipo_pagamento] ?? $this->tipo_pagamento;
    }

    public function getStatusFormatadoAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getDataCriacaoFormatadaAttribute()
    {
        return $this->data_criacao->format('d/m/Y H:i');
    }

    public function podeReembolsar()
    {
        return in_array($this->status, ['aprovado', 'reembolsado']);
    }
}
