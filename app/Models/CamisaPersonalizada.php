<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CamisaPersonalizada extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'camisa_personalizada';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'genero',
        'modelagem',
        'manga',
        'punho',
        'bolso',
        'vista',
        'colarinho',
        'tecido_id',
        'imagem_preview',
        'medidas',
        'guest_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'medidas' => 'array',
        'data_criacao' => 'datetime'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tecido()
    {
        return $this->belongsTo(Tecido::class, 'tecido_id');
    }

    public function itensPedido()
    {
        return $this->hasMany(ItemPedido::class, 'camisa_personalizada_id');
    }

    /**
     * Scope a query to only include shirts for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $usuarioId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDoUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $guestId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDoGuest($query, $guestId)
    {
        return $query->where('guest_id', $guestId);
    }

    /**
     * @return string
     */
    public function getDataCriacaoFormatadaAttribute()
    {
        return $this->data_criacao->format('d/m/Y H:i');
    }

    /**
     * @return string
     */
    public function getGeneroFormatadoAttribute()
    {
        $generos = [
            'masculino' => 'Masculino',
            'feminino' => 'Feminino',
            'unissex' => 'Unissex'
        ];

        return $generos[$this->genero] ?? $this->genero;
    }
}
