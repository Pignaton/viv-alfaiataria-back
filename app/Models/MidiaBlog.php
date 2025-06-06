<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class MidiaBlog extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'midia_blog';
    protected $primaryKey = 'id';

    protected $fillable = [
        'post_id',
        'tipo',
        'url',
        'thumbnail',
        'legenda',
        'ordem',
        'destaque'
    ];

    protected $casts = [
        'destaque' => 'boolean',
        'ordem' => 'integer'
    ];

    public static $tiposPermitidos = ['imagem', 'video'];

    public function post()
    {
        return $this->belongsTo(PostBlog::class, 'post_id')->withDefault([
            'titulo' => 'Post não encontrado'
        ]);
    }

    public function getUrlAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return config('filesystems.disks.r2.url').'/'.ltrim($value, '/');
    }

    public function getThumbnailAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return $this->getUrlAttribute($value);
    }
}
