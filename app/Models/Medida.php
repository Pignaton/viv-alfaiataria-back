<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medida extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'data_registro';

    const UPDATED_AT = null;

    protected $table = 'medida';

    protected $fillable = [
        'usuario_id',
        'nome',
        'valor',
        'unidade',
        'data_registro'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_registro' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }

    public function itemPedido()
    {
        return $this->belongsTo(ItemPedido::class, 'usuario_id', 'usuario_id');
    }
}
