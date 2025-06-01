<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'destaque' => 'boolean'
    ];

    public function post()
    {
        return $this->belongsTo(PostBlog::class, 'post_id');
    }

    public function getUrlAttribute($value)
    {
        return $value ? (Str::startsWith($value, 'http') ? $value : asset('storage/' . $value)) : null;
    }

    public function getThumbnailAttribute($value)
    {
        return $value ? (Str::startsWith($value, 'http') ? $value : asset('storage/' . $value)) : null;
    }
}
