<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagamentoBoleto extends Model
{
    use HasFactory;

    protected $table = 'pagamento_boleto';
    protected $primaryKey = 'pagamento_id';

    protected $fillable = [
        'pagamento_id',
        'codigo_barras',
        'data_vencimento',
        'url_boleto'
    ];

    protected $casts = [
        'data_vencimento' => 'date'
    ];

    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class, 'pagamento_id');
    }
}
