<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $guarded = [];

    public function generic()
    {
        return $this->belongsTo(Generic::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if ($product->isDirty('trade_name')) {
                $product->slug = self::generateUniqueSlug(
                    $product->trade_name,
                    $product->id
                );
            }
        });
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

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = self::generateSlug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            self::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
