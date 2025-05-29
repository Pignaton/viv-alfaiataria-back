<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenAcesso extends Model
{
    use HasFactory;

    protected $table = 'token_acesso';
    protected $primaryKey = 'id';

    public $timestamps = false;
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'token',
        'tipo',
        'expiracao',
        'utilizado',
        'ip_origem',
        'user_agent'
    ];

    protected $casts = [
        'expiracao' => 'datetime',
        'utilizado' => 'boolean',
        'data_criacao' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
