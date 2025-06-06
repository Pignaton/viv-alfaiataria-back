<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tecido extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'tecido';

    /**
     * @var bool
     */
    public $timestamps = true;

    const CREATED_AT = 'data_cadastro';

    const UPDATED_AT = null;

    /**
     *
     * @var array
     */
    protected $fillable = [
        'nome_produto',
        'composicao',
        'padrao',
        'suavidade',
        'tecelagem',
        'fio',
        'origem',
        'fabricante',
        'peso',
        'preco',
        'preco_promocional',
        'imagem_url'
    ];

    /**
     *
     * @var array
     */
    protected $casts = [
        'preco' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
        'data_cadastro' => 'datetime'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'preco_promocional' => 0,
        'imagem_url' => '/storage/images/default-fabric.jpg'
    ];

    /**
     * @return string
     */
    public function getPrecoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }

    /**
     * @return string|null
     */
    public function getPrecoPromocionalFormatadoAttribute()
    {
        return $this->preco_promocional > 0
            ? 'R$ ' . number_format($this->preco_promocional, 2, ',', '.')
            : null;
    }

    /**
     * @return bool
     */
    public function getEmPromocaoAttribute()
    {
        return $this->preco_promocional > 0;
    }

    /**
     * @param  string  $value
     * @return void
     */
    public function setPrecoAttribute($value)
    {
        $this->attributes['preco'] = (float) str_replace(',', '.', str_replace('.', '', $value));
    }

    /**
     * @param  string  $value
     * @return void
     */
    public function setPrecoPromocionalAttribute($value)
    {
        $this->attributes['preco_promocional'] = $value
            ? (float) str_replace(',', '.', str_replace('.', '', $value))
            : 0;
    }

    /**
     * @return void
     */
    public function deleteImagem()
    {
        if ($this->imagem_url && $this->imagem_url !== '/storage/images/default-fabric.jpg') {
            Storage::delete(str_replace('/storage', 'public', $this->imagem_url));
        }
    }

    /**
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePromocao($query)
    {
        return $query->where('preco_promocional', '>', 0);
    }

    /**
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $termo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBusca($query, $termo)
    {
        return $query->where('nome_produto', 'LIKE', "%{$termo}%")
            ->orWhere('composicao', 'LIKE', "%{$termo}%");
    }

    public function imagens()
    {
        return $this->hasMany(TecidoImagem::class, 'tecido_id');
    }
}
