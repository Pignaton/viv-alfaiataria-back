<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'data_criacao';

    const UPDATED_AT = 'data_atualizacao';

    protected $table = 'services';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'content',
        'video_url',
        'image_url',
        'media_type',
        'media',
        'alt_text'
    ];

    public static function getContent()
    {
        return self::firstOrCreate(['id' => 1]);
    }
}
