<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['menu_id', 'file_path', 'file_name', 'image_width', 'image_height', 'is_active'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}