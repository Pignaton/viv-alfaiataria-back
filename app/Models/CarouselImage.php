<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CarouselImage extends Model
{
    use HasFactory;
    //use SoftDeletes;
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';
    //const DELETED_AT = 'data_delecao';

    protected $fillable = [
        'title',
        'subtitle',
        'media_url',
        'media_type',
        'alt_text',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /*public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc');
    }*/

    public function getIsImageAttribute()
    {
        return $this->media_type === 'image';
    }

    public function getIsVideoAttribute()
    {
        return $this->media_type === 'video';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeActiveOrdered($query)
    {
        return $query->active()->ordered();
    }

}
