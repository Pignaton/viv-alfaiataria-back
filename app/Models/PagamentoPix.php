<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagamentoPix extends Model
{
    use HasFactory;

    protected $table = 'pagamento_pix';
    protected $primaryKey = 'pagamento_id';

    protected $fillable = [
        'pagamento_id',
        'chave_pix',
        'qrcode',
        'data_expiracao'
    ];

    protected $casts = [
        'data_expiracao' => 'datetime'
    ];

    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class, 'pagamento_id');
    }
}
