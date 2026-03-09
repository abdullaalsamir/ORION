<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceSensitiveInformation extends Model
{
    protected $table = 'price_sensitive_information';
    protected $fillable = ['title', 'filename', 'description', 'publication_date', 'order', 'is_active'];
    protected $casts = ['publication_date' => 'date'];
}