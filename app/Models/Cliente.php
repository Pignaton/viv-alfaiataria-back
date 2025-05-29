<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

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
}
