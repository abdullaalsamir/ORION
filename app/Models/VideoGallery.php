<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoGallery extends Model
{
    protected $fillable = ['title', 'video_path', 'thumbnail_path', 'order', 'is_active'];
}