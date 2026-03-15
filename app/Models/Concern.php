<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
    protected $fillable = ['menu_id', 'web_address', 'is_redirect', 'cover_photo_path', 'description'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function galleries()
    {
        return $this->hasMany(ConcernGallery::class)->orderBy('order');
    }
}