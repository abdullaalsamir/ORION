<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Generic extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function generateSlug($name)
    {
        $slug = strtolower($name);

        $slug = str_replace('&', ' and ', $slug);
        $slug = str_replace('+', ' plus ', $slug);
        $slug = str_replace('®', ' r ', $slug);
        $slug = str_replace('/', ' - ', $slug);

        return Str::slug($slug);
    }

    protected static function booted()
    {
        static::saving(function ($generic) {
            if ($generic->isDirty('name')) {
                $generic->slug = self::generateSlug($generic->name);
            }
        });
    }
}
