<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Career extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'location',
        'on_from',
        'on_to',
        'job_type',
        'apply_type',
        'file_path',
        'converted_images',
        'image_width',
        'image_height',
        'description',
        'is_active',
        'order'
    ];

    protected $casts = [
        'on_from' => 'date',
        'on_to' => 'date',
        'converted_images' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($career) {
            if ($career->isDirty('title') || empty($career->slug)) {
                $career->slug = self::generateUniqueSlug($career->title, $career->id);
            }
        });
    }

    public static function generateUniqueSlug(string $title, $ignoreId = null): string
    {
        $baseSlug = Str::slug(str_replace('&', 'and', $title));
        $slug = $baseSlug;

        $query = self::where('slug', $slug);
        if ($ignoreId)
            $query->where('id', '!=', $ignoreId);

        if (!$query->exists())
            return $slug;

        $counter = 2;
        while (true) {
            $slug = $baseSlug . '-' . $counter;
            $query = self::where('slug', $slug);
            if ($ignoreId)
                $query->where('id', '!=', $ignoreId);
            if (!$query->exists())
                return $slug;
            $counter++;
        }
    }
}