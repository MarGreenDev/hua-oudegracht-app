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
        'image',
        'image2',
        'image3',
        'image4',
        'image5',
    ];
}
