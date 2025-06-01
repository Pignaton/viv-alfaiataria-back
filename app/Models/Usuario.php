<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory;

    use Notifiable;

    public $timestamps = false;

    const CREATED_AT = 'data_criacao';

    const UPDATED_AT = null;

    protected $table = 'usuario';
    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'senha_hash',
        'tipo_usuario',
        'ativo',
        'ultimo_login',
        'tentativas_login',
        'bloqueado_ate'
    ];

    protected $hidden = [
        'senha_hash',
        'remember_token'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_criacao' => 'datetime',
        'ultimo_login' => 'datetime',
        'bloqueado_ate' => 'datetime'
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'usuario_id', 'id');
    }

    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'usuario_id', 'id');
    }

    public function tokensAcesso()
    {
        return $this->hasMany(TokenAcesso::class, 'usuario_id');
    }

    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    public function getNomeAttribute()
    {
        if ($this->tipo_usuario === 'admin' || $this->tipo_usuario === 'gerente') {
            return $this->administrador->nome ?? 'Administrador';
        }
        return $this->cliente->nome_completo ?? 'Usuário';
    }

    public function getNivelAcessoAttribute()
    {
        if ($this->tipo_usuario === 'admin' || $this->tipo_usuario === 'gerente') {
            return $this->administrador->nivel_acesso ?? 'total';
        }
        return 'cliente';
    }
    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'usuario_id');
    }

}
