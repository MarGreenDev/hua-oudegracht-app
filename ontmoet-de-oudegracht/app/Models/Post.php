<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'place',
        'text_content',
        'date',
        'latitude',
        'longitude',
        'video_timestamp',
        'image',
        'image2',
        'image3',
        'image4',
        'image5',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'video_timestamp' => 'decimal:3',
        ];
    }
}
