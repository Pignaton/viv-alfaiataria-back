<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostBlog extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'data_criacao';

    const UPDATED_AT = 'data_atualizacao';

    protected $table = 'post_blog';
    protected $primaryKey = 'id';

    protected $fillable = [
        'titulo',
        'slug',
        'conteudo',
        'resumo',
        'usuario_id',
        'tipo_conteudo',
        'publicado',
        'data_publicacao'
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'data_publicacao' => 'datetime',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->titulo);
            $post->usuario_id = auth()->id();
        });

        static::updating(function ($post) {
            $post->slug = Str::slug($post->titulo);
        });
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id')->withDefault([
            'nome' => 'Usuário desconhecido'
        ]);
       //return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function midias()
    {
        return $this->hasMany(MidiaBlog::class, 'post_id')->orderBy('ordem');
    }

    public function imagemDestaque()
    {
        return $this->hasOne(MidiaBlog::class, 'post_id')
            ->where('destaque', true)
            ->where('tipo', 'imagem');
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true)
            ->where('data_publicacao', '<=', now());
    }

    public function getResumoAttribute($value)
    {
        return $value ?? Str::limit(strip_tags($this->conteudo), 200);
    }
}
