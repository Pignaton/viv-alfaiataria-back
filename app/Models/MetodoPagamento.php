<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPagamento extends Model
{
    use HasFactory;

    protected $table = 'metodo_pagamento';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'apelido',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_cadastro' => 'datetime'
    ];

    const TIPOS = [
        'cartao_credito' => 'Cartão de Crédito',
        'cartao_debito' => 'Cartão de Débito'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function cartao()
    {
        return $this->hasOne(Cartao::class, 'metodo_id');
    }

    public function getTipoFormatadoAttribute()
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getDescricaoCompletaAttribute()
    {
        return $this->cartao
            ? "{$this->tipo_formatado} {$this->cartao->bandeira} **** **** **** {$this->cartao->ultimos_quatro_digitos}"
            : $this->apelido;
    }
}
