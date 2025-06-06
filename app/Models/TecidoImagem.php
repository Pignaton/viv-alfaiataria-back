<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TecidoImagem extends Model
{
    use HasFactory;

    protected $table = 'tecido_imagens';
    protected $fillable = ['tecido_id', 'imagem_url', 'ordem'];
    protected $casts = [
        'data_cadastro' => 'datetime'
    ];

    public function tecido()
    {
        return $this->belongsTo(Tecido::class);
    }
}
