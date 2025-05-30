<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reembolso extends Model
{
    use HasFactory;

    protected $table = 'reembolso';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pagamento_id',
        'valor',
        'motivo',
        'status',
        'metodo_estorno'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_solicitacao' => 'datetime',
        'data_processamento' => 'datetime'
    ];

    const STATUS = [
        'pendente' => 'Pendente',
        'processado' => 'Processado',
        'falha' => 'Falha'
    ];

    const METODOS_ESTORNO = [
        'original' => 'Método Original',
        'credito_loja' => 'Crédito na Loja',
        'pix' => 'PIX'
    ];

    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class, 'pagamento_id');
    }

    public function getStatusFormatadoAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getMetodoEstornoFormatadoAttribute()
    {
        return self::METODOS_ESTORNO[$this->metodo_estorno] ?? $this->metodo_estorno;
    }
}
