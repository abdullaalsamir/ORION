<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['image_path', 'header_1', 'header_2', 'description', 'button_text', 'link_url', 'order', 'is_active'];
}