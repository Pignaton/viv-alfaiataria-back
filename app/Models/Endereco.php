<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'data_cadastro';

    const UPDATED_AT = null;

    protected $table = 'endereco';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'apelido',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'principal',
        'entrega'
    ];

    protected $casts = [
        'principal' => 'boolean',
        'entrega' => 'boolean',
        'data_cadastro' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function getEnderecoCompletoAttribute()
    {
        return sprintf(
            "%s, %s%s - %s, %s/%s",
            $this->logradouro,
            $this->numero,
            $this->complemento ? ', ' . $this->complemento : '',
            $this->bairro,
            $this->cidade,
            $this->estado
        );
    }

    public function getCepFormatadoAttribute()
    {
        return substr_replace($this->cep, '-', 5, 0);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($endereco) {
            if ($endereco->principal) {
                $endereco->usuario->enderecos()->update(['principal' => false]);
            }
        });

        static::updating(function ($endereco) {
            if ($endereco->principal) {
                $endereco->usuario->enderecos()
                    ->where('id', '!=', $endereco->id)
                    ->update(['principal' => false]);
            }
        });
    }

    public function enderecoCompleto()
    {
        if (!$this->endereco_entrega) return 'N/A';

        $endereco = $this->endereco_entrega;
        return sprintf(
            '%s, %s, %s - %s/%s, CEP: %s',
            $endereco['logradouro'] ?? '',
            $endereco['numero'] ?? '',
            $endereco['bairro'] ?? '',
            $endereco['cidade'] ?? '',
            $endereco['estado'] ?? '',
            $endereco['cep'] ?? ''
        );
    }
}
