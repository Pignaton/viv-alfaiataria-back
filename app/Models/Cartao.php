<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cartao extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'cartao';
    protected $primaryKey = 'metodo_id';

    protected $fillable = [
        'metodo_id',
        'ultimos_quatro_digitos',
        'bandeira',
        'nome_titular',
        'token_secure',
        'data_validade'
    ];

    public function metodoPagamento()
    {
        return $this->belongsTo(MetodoPagamento::class, 'metodo_id');
    }

    public function getDataValidadeFormatadaAttribute()
    {
        return implode('/', str_split($this->data_validade, 2));
    }
}
