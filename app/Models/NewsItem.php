<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'news_date',
        'file_type',
        'file_path',
        'order',
        'is_pin',
        'is_active'
    ];

    protected $casts = ['news_date' => 'date'];
}
