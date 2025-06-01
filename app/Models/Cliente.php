<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'data_criacao';

    const UPDATED_AT = null;

    protected $table = 'cliente';
    protected $primaryKey = 'usuario_id';

    protected $fillable = [
        'usuario_id',
        'nome_completo',
        'cpf',
        'telefone',
        'data_nascimento'
    ];

    protected $casts = [
        'data_nascimento' => 'date'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }

    public function enderecos()
    {
        return $this->hasManyThrough(
            Endereco::class,
            Usuario::class,
            'id', //  usuarios tabela F
            'usuario_id', // enderecos tabela F
            'usuario_id', //  clientes tabela PK
            'id' // usuarios tabela PK
        );
    }
}
